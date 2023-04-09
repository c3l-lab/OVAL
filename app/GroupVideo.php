<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for 'group_videos'.
 */
class GroupVideo extends Model
{
	use HasFactory;
	protected $table = 'group_videos';
	protected $fillable = ['group_id', 'video_id', 'hide', 'show_analysis'];
	protected $casts = ['hide'=>'boolean', 'show_analysis'=>'boolean'];
	
	
	/**
	 * Default values.
	 * These are used to set object values when using new or create:: 
	 * But not used if there are values obtained from database table
	 **/
	protected $attributes;

	/**
	 * Constructor
	 * 
	 * Set the attributes with default values taken from config file (config/settings.php)
	 * The value of config is read in runtime, so it has to be set in constructor.
	 */
	public function __construct() {
		$this->attributes = [
			'hide' => config('settings.defaults.group_video_hide'),
			'show_analysis' => config('settings.defaults.group_video_show_analysis')
		];
	}

	
	/**
	*	One-to-Many relationship.
	*	Returns Annotations related to the video for the group
	*	@return collection of Annotation objects
	**/
	public function annotations() {
		return $this->hasMany('oval\Annotation');
	}
	
	/**
	*	One-to-Many relationship.
	*	Method to get Comments related to the video for the group
	*	@return collection of Comment objects
	**/
	public function comments() {
		return $this->hasMany('oval\Comment');
	}

	/**
	*	One-to-Many relationship
	*	@return collection of Point objects
	**/
	public function points() {
		return $this->hasMany('oval\Point');
	}
	
	/**
	*	Method to return the Course the group of this GroupVideo belongs to
	*	@return Course object
	**/
	public function course() {
		$group = Group::find($this->group_id);
		return $group->course;
	}
	
	/**
	*	Method to return the Group of this GroupVideo
	*	@return Group object
	**/
	public function group() {
		return Group::find($this->group_id);
	}
	
	/**
	*	Method to return the Video of this GroupVideo
	*	@return Video object
	**/	
	public function video() {
		return Video::find($this->video_id);
	}
	
	/**
    *	Method to return Groups that has access to this Video that belongs to Course
    *	whose id is passed in
    *	@param int course_id
    *	@return collection of Group objects
    **/
    public function allGroupsInCourseWithAccess($course_id) {
		$groups = Group::where('course_id', '=', $course_id)
					->whereIn('id', function($q) {
						$q->select('group_id')
							->from('group_videos')
							->where('video_id', '=', $this->video_id);
					})
					->get();
		return $groups;
    }


	/**
	 * Method to return the course's default group's GroupVideo
	 * 
	 * This method can be used to get to the "default group" of 
	 * the course this GroupVideo belongs to.
	 * @return GroupVideo
	 */
	public function defaultGroupVideo () {
		$def_group = $this->course()->defaultGroup();
		$group_video = GroupVideo::where([
							['group_id', '=', $def_group->id],
							['video_id', '=', $this->video_id]
						])
						->first();
		return $group_video;
	}

	/**
	*	Method to find related Points for this GroupVideo.
	*	This returns the course-wide points for this video 
	*	if it was saved to be the same for all groups in the course.
	*	@return collection of Point objects
	**/
	public function relatedPoints() {
		$points = collect();
		if(config('settings.course_wide.point')==1) {
			//--see if there're course-wide points for this video
			$def_group = $this->course()->defaultGroup();
			$group_video = $this->defaultGroupVideo();
			if (!empty($group_video)) {
				$points = $group_video->points;
			}
		}
		return $points;
	}

	/**
    *	One-to-One relationship.
    *	Returns instruction for points for this GroupVideo
    *	@return PointInstruction object
    **/
    public function point_instruction() {
    	return $this->hasOne('oval\PointInstruction');
	}
	
	public function relatedPointInstruction() {
		$instruction = null;
		if (config('settings.course_wide.point')){
			$def_group_video = $this->defaultGroupVideo();
			$instruction = $def_group_video->point_instruction;
		}
		return $instruction;
	}
    

    /**
    *	One-to-Many relationship
    *	@return collection of Tracking objects
    **/
    public function trackings() {
    	return $this->hasMany('oval\Tracking');
    }
    
    
    /**
    *	Method used for analytics page to show number of unique users who viewed this video
    *	@return int 
    **/	
    public function numUniqueViews() {
    	return Tracking::where('group_video_id', '=', $this->id)
    			->distinct()
    			->count('user_id');
	}

	/**
    *	Method used for analytics page to show group member
    *	@return string
    **/	
    public function memberList() {
		$usersWithAccess = $this->group()->members;
		$list = '';

		for($i = 0; $i < count($usersWithAccess); $i++){
			if($i == count($usersWithAccess)-1){
				$list = $list.$usersWithAccess[$i]->id;
			}else{
				$list = $list.$usersWithAccess[$i]->id.",";
			}
			
		}
		
    	return $list;
	}
	
	public function usersWhoAccessed() {
		$viewers_ids = Tracking::where([
							['group_video_id', '=', $this->id],
							['event', '=', 'View']
						])
						->pluck('user_id')
						->unique()
						->all();
		$accessed = User::whereIn('id', $viewers_ids)->get();
		return $accessed;
	}
	
    
    /**
    *	Method used for analytics page to show the percentage users who viewed this video
    *	@return float (rounded to 2 decimal places) 
    **/	
    public function percentageUsersViewed() {
    	$usersWithAccess = count($this->group()->members);
    	$uniqueViewers = $this->numUniqueViews();
    	$percent = 0;
    	if ($uniqueViewers != 0) { 
    		$percent = $uniqueViewers/$usersWithAccess*100;
    	}
    	return round($percent, 2);
    }

	/**
    *	Method used for analytics page to show average number of annotation users made
    *	@return float (rounded to 2 decimal places) 
    **/
	public function aveAnnotationsPerUser() {
		$numAnnotations = count($this->annotations);
		$numViewers = $this->numUniqueViews();
		$ave = 0;
		if ($numViewers !=0) {
			$ave = round($numAnnotations/(float)$numViewers, 2);
		}
		return $ave;
	}
	
	/**
    *	Method used for analytics page to show average number of comments users made
    *	@return float (rounded to 2 decimal places) 
    **/
	public function aveCommentsPerUser() {
		$numComments = count($this->comments);
		$numViewers = $this->numUniqueViews();
		$ave = 0;
		if ($numViewers != 0) {
			$ave = round($numComments/(float)$numViewers, 2);
		}
		return $ave;
	}
	
	/**
    *	Method used for analytics page to show number times 'download annotations' button was clicked on this video
    *	@return int 
    **/	
	public function numAnnotationDownloads() {
		return $this->trackings
					->where('target', '=', '.download-comments')
					->count();
	}
	
	/**
    *	Method used for analytics page to show number of times an annotation was expanded to view
    *	@return int 
    **/	
	public function numTimesAnnotationViewed() {
		return $this->trackings
					->where ('target', '=', '.annotation-list-item')
					->count();
	}
	
	/**
    *	Method to return total number of times this video was viewed
    *	@return int 
    **/	
	public function numViews() {
		return $this->trackings
					->where('event', '=', 'View')
					->count();
	}
	protected static function newFactory()
    {
        return \Database\Factories\GroupVideoFactory::new();
    }
}
