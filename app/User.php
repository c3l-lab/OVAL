<?php

namespace oval;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for table 'users'.
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'is_lti',
    ];


    /**
     * Method to return full name of the user.
     * @return string
     */
    public function fullName() {
    	return $this->first_name." ".$this->last_name;
    }

    /**
     * Method to return id of the user.
     * TODO: check where it's used... $user->id does the same thing..
     */
    public function id() {
    	return $this->id;
    }

    /**
    *	Method to check if the user is an instructor of any courses
    *	@return boolean
    **/
    public function isAnInstructor() {
    	return DB::table('enrollments')
				->whereUserId($this->id)
				->whereIsInstructor(true)
				->count() >0;
    }

    /**
    *	Method to check if the user is an instructor of the course passed in as parameter
    *	@param Course $course
    *	@return boolean
    **/
    public function isInstructorOf($course) {
    	return DB::table('enrollments')
    			->whereUserId($this->id)
    			->whereCourseId($course->id)
    			->whereIsInstructor(true)
    			->count() >0;
    }

    /**
    *	Method to check if the user is enrolled in the course passed in as parameter
    *	@param Course $course
    *	@return boolean true if enrolled, false if not.
    **/
    public function checkIfEnrolledIn($course) {
    	return DB::table('enrollments')
			->whereUserId($this->id)
			->whereCourseId($course->id)
			->count() > 0;
    }

    /**
    *	Many to many Relationship - Courses the user is enrolled in.
    *	@return collection of Course objects
    **/
    public function enrolledCourses() {
    	return $this->belongsToMany('oval\Course', "enrollments")->withPivot('is_instructor');
    }

    /**
    *	Get collection of Course objects the user teaches
    *	@return collection of Course objects
    **/
    public function coursesTeaching() {
    	$user_id = $this->id;
    	return Course::whereIn('id', function($q) use ($user_id) {
    					$q	->select('course_id')
    						->from('enrollments')
    						->where('user_id', '=', $user_id)
    						->where('is_instructor', '=', true);
					})
					->get();
    }

    /**
    *	Add many-to-many relationship.
    *	Enroll this user into the course passed as parameter
    *	@param Course $course
    **/
    public function enrollIn($course) {
    	//check if user is enrolled in the course first...
		$alreadyEnrolled = $this->checkIfEnrolledIn($course);
		if (!$alreadyEnrolled) {
			$this->enrolledCourses()->attach($course);
		}
    }

    /**
    *	Add many-to-many relationship.
    *	Make this user an instructor of a Course
    *	@param Course $course
    **/
    public function makeInstructorOf($course) {
    	//first check if enrolled, and unenroll if enrolled
    	if($this->checkIfEnrolledIn($course)) {
    		echo "\nalready enrolled";
    		$this->enrolledCourses()->detach($course->id);
    	}
    	$this->enrolledCourses()->save($course, ['isInstructor'=>true]);
    }

    /**
    *	Get many-to-many relationship
    *	@return collection of Group objects
    **/
    public function groupMemberOf() {
    	return $this->belongsToMany('oval\Group', "group_members");
    }

    /**
    *	Method to check if the user belongs to the group passed in as parameter
    *	@param Group $group
    *	@return boolean true if in group, false if not.
    **/
    public function checkIfInGroup($group) {
    	return DB::table('group_members')
			->whereUserId($this->id)
			->whereGroupId($group->id)
			->count() > 0;
    }

	/**
	*	Add this user to the group passed in
	*	@param	Group $group
	**/
	public function addToGroup($group) {
		if (!$this->checkIfInGroup($group)) {
			$this->groupMemberOf()->attach($group);
		}
	}

    /**
    *	Remove this user from the Group passed in
    *	@param Group $group
    **/
    public function removeFromGroup($group) {
    	$this->groupMemberOf->detach($group->id);
    }

    /**
    *	Videos that are assigned to the groups this user belongs to
    *	@return collection of Video objects
    **/
    public function viewableGroupVideos() {
    	$group_videos = collect();
    	foreach ($this->groupMemberOf as $group) {
            $group_videos = $group_videos->merge($group->availableGroupVideosForUser($this));

    	}
    	return $group_videos;
    }

    /**
     * One-to-many relationship - Annotation created by the User
     *	@return collection of Annotation objects
     **/
    public function annotations() {
    	return $this->hasMany('oval\Annotation');
    }

    /**
    *	One-to-many relationship.
    *	Comments written by this user.
    *	@return collection of Comment objects
    **/
    public function comments() {
    	return $this->hasMany('oval\Comment');
    }

    /**
     * One to many relationship - Videos added by the User
     *	@return collection of Video objects
     **/
    public function videosAdded() {
    	return $this->hasMany('oval\Video', 'added_by');
    }

    /**
    *	Many-to-many relationship.
    *	Returns videos viewed by this user.
    *	@return collection of Video objects
    **/
    public function videosViewed() {
    	return $this->belongsToMany('oval\Video', 'videos_viewed_by');
    }

    /**
    *	Many-to-many relationship.
    *	Annotations viewed by this user.
    *	@return collection of Annotation objects
    **/
    public function annotationViewed() {
    	return $this->belongsToMany('oval\Annotation', 'annotation_viewed_by');
    }

    /**
    *	One-to-Many relationship.
    *	Feedbacks made by this user.
    *	@return collection of Feedback objects
    **/
    public function feedbacks() {
    	return $this->hasMany('oval\Feedback');
    }

    /**
    *	Method to get this user's ConfidenceLevel for GroupVideo whose id is passed in
    *	@param int group_video_id
    *	@return ConfidenceLevel object
    **/
    public function confidenceLevelForVideo($group_video_id) {
    	return DB::table('confidence_levels')
    			->where([
    				['user_id', '=', $this->id],
    				['group_video_id', '=', $group_video_id]
    			])
    			->first();
    }

    /**
    *	One-to-Many relationship
    *	@return collection of Tracking objects
    **/
    public function trackings() {
    	return $this->hasMany('oval\Tracking');
    }

    /**
    *   One-to-Many relationship
    *   @return collection of AnalysisRequest objects
    **/
    public function analysis_request() {
        return $this->hasMany('oval\AnalysisRequest');
    }
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }
}//end class
