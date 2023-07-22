<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for table 'videos'
 */
class Video extends Model
{
    use HasFactory;
    protected $table = "videos";
    protected $fillable = ['identifier', 'title', 'description', 'duration', 'thumbnail_url', 'media_type', 'added_by'];

    /**
    *	One-to-Many relationship (inverse).
    *	Get the User that added this Video.
    *	@return User
    **/
    public function addedBy()
    {
        return $this->belongsTo('oval\Models\User', 'added_by');
    }

    /**
    *	Many-to-many relationship.
    *	Get the groups this video is assigned to
    *	@return collection of Group objects
    **/
    public function groups()
    {
        return $this->belongsToMany('oval\Models\Group', 'group_videos')->withTimestamps();
    }

    /**
    *	Method to return collection of Group objects that belongs to Course
    *	whose id is passed in
    *	@param int course_id
    *	@return collection of Group objects
    **/
    public function groupsInCourse($course_id)
    {
        return $this->groups->where('course_id', '=', $course_id);
    }

    /**
    *	Utility method to check if this Video belongs to Group passed in
    *	@param Group $group
    *	@return true if already assigned, false if not
    **/
    public function checkIfAssignedTo($group)
    {
        return DB::table('group_videos')
            ->whereVideoId($this->id)
            ->whereGroupId($group->id)
            ->count() > 0;
    }

    /**
    *	Method to assign this video to the group passed in as parameter
    *
    *   Use this method instead of calling $video->attach($group);
    *   because Eloquent doesn't check for existance and duplicate may be inserted,
    *   as well as we need to set default values for GroupVideo by calling new
    *   instead of attach()
    *	@param Group
    **/
    public function assignToGroup($group)
    {
        if (!$this->checkIfAssignedTo($group)) {
            // $this->groups()->attach($group);
            //-- in order for the "default values" to be set, instantiate it
            //-- rather than attaching it
            $group_video = new GroupVideo();
            $group_video->group_id = $group->id;
            $group_video->video_id = $this->id;
            $group_video->save();
        }
    }

    /**
    *	One-to-Many relationship.
    *	Returns Annotations for this Video
    *	@return collection of Annotation objects
    **/
    public function annotations()
    {
        return $this->hasManyThrough('oval\Models\Annotation', 'oval\Models\GroupVideo');
    }

    /**
    *   One-to-One relationship
    *   @return Transcript object
    **/
    public function transcript()
    {
        return $this->hasOne('oval\Models\Transcript');
    }

    /**
    *   One-to-Many relationship
    *   @return collection of Keyword objects
    **/
    public function keywords()
    {
        return $this->hasMany('oval\Models\Keyword', 'videoId', 'id');
    }

    public function keywords_for_edits()
    {
        $keywords = $this->keywords
                    ->filter(function ($kw) {
                        return ($kw->type=="keywords" || $kw->type=="concepts");
                    })
                    ->pluck('keyword')
                    ->unique();
        return $keywords;
    }

    /**
    *   Method to construct and return the url for the video
    *   @return string
    **/
    public function video_url()
    {
        return "https://youtube.com/embed/".$this->identifier;
    }

    /**
    *   One-to-Many relationship
    *   @return collection Collection of AnalysisRequest
    **/
    public function analysis_request()
    {
        return $this->hasMany('oval\Models\AnalysisRequest');
    }

    /**
    *   Used for displaying duration in human-friendly format
    *   @return string duration in "x hours y minutes and z seconds" format
    **/
    public function formattedDuration()
    {
        $retVal = "0 seconds";
        $hour = 0;
        $min = 0;
        $sec = $this->duration;
        if($sec > 60) {
            $min = (int)($this->duration/60);
            $sec = $this->duration%60;
            if ($min > 60) {
                $hour = $min/60;
                $min = $min%60;
                $retVal = $hour." hours ".$min." minutes and ".$sec." seconds";
            } else {
                $retVal = $min." minutes and ".$sec." seconds";
            }
        } else {
            $retVal = $sec." seconds";
        }

        return $retVal;
    }
}
