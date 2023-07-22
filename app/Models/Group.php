<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for table 'groups'.
 */
class Group extends Model
{
    use HasFactory;
    protected $table = "groups";
    protected $fillable = ['name', 'course_id'];

    /**
    *	Inverse of one-to-many relationship.
    *	The Course that this Group is in.
    *	@return Course
    **/
    public function course()
    {
        return $this->belongsTo('oval\Models\Course');
    }

    /**
    *	Many-to-many relationship.
    *	Get the Users that belong to this group
    *	@return collection of User objects
    **/
    public function members()
    {
        return $this->belongsToMany('oval\Models\User', 'group_members');
    }

    /**
    *	Method to return students who belongs to this group
    *	@return collection of User objects
    **/
    public function students()
    {
        $all = $this->members;
        $students = $all->reject(function ($user) {
            return $user->isInstructorOf($this->course);
        });
        return $students;
    }

    /**
    *	Method to return array containing User ID of students who belongs to this group
    *	@return array of int (user->id)
    **/
    public function students_ids()
    {
        $students = $this->students();
        $students_ids = array();
        foreach ($students as $s) {
            $students_ids[] = $s->id;
        }
        return $students_ids;
    }

    /**
    *	Many-to-many relationship.
    *	Get the Videos that are assigned to this Group
    *	@return collection of Video objects
    **/
    public function videos()
    {
        return $this->belongsToMany('oval\Models\Video', 'group_videos')->withTimestamps();
    }

    /**
    *   Method to return GroupVideos that are available to the user,
    *   in ascending order of group_video.order.
    *   If the user is student, this doesn't include ones
    *   that are set "hidden"
    *   @param User $user
    *   @return collection of GroupVideo objects
    **/
    public function availableGroupVideosForUser(User $user)
    {
        $group_videos = $this->group_videos()
                            ->reject(function ($v, $key) {
                                return $v->status!="current";
                            })
                            ->sortBy('order');
        if ($user->isInstructorOf($this->course) == false) {
            $group_videos = $group_videos->reject(function ($v, $k) {
                return $v->hide == true;
            });
        }
        return $group_videos;
    }

    /**
    *	Method to get all GroupVideo for this Group
    *	@return collection of GroupVideo objects
    **/
    public function group_videos()
    {
        return GroupVideo::where("group_id", '=', $this->id)->get();
    }

    /**
    *	Method to check if the video passed as parameter already belongs to this group
    *	@param Video $video
    *	@return boolean true if assigned, false if not.
    **/
    public function checkIfAlreadyHasVideo($video)
    {
        return DB::table('group_videos')
            ->whereGroupId($this->id)
            ->whereVideoId($video->id)
            ->count() > 0;
    }

    /**
    *	Method to assign a video to this group.
    *	First checks if the relationship already exists.
    *	@param int id of Video
    **/
    public function addVideo($video)
    {
        if (!$this->checkIfAlreadyHasVideo($video)) {
            $this->videos()->attach($video);
        }
    }

    /**
    *	Method to check if the user passed in as paremeter is a member of this group
    *	@param User $user
    *	@return boolean true if member, false if not.
    **/
    public function checkIfAlreadyMember($user)
    {
        return DB::table('group_members')
            ->whereGroupId($this->id)
            ->whereUserId($user->id)
            ->count() > 0;
    }

    /**
    *	Method to add User passed in as parameter to this Group
    *	@param User $user
    **/
    public function addMember($user)
    {
        $alreadyMember = $this->checkIfAlreadyMember($user);
        if (!$alreadyMember) {
            $this->members()->attach($user);
        }
    }

    /**
    *	Method to update the members of this group with array of user id passed in.
    *	Checks if the user already belongs to this group, then add/remove
    *	@param array of int (user->id)
    **/
    public function updateMembers($user_ids_array)
    {
        $members = $this->members;

        foreach ($user_ids_array as $updated) {
            if ($members->where('id', $updated)->count() == 0) {
                $this->addMember(User::find($updated));
            }
        }
        foreach ($members as $existing) {
            if (!in_array($existing->id, $user_ids_array)) {
                $this->members()->detach($existing->id);
            }
        }
    }
    protected static function newFactory()
    {
        return \Database\Factories\GroupFactory::new();
    }
}
