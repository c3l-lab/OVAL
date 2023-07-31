<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for table 'comments'
 */
class Comment extends Model
{
    use HasFactory;
    /** @var array Table name */
    protected $table = 'comments';

    /** @var array The attributes that are mass assignable.*/
    protected $fillable = [
        'group_video_id', 'user_id', 'description', 'tags', 'is_private'
    ];

    /**
    *	One-to-Many relationship (inverse).
    *	Returns the User that wrote this Comment.
    *	@return User
    **/
    public function user()
    {
        return $this->belongsTo('oval\Models\User');
    }

    /**
    *   One-to-Many relationship (inverse).
    *	Returns the groupvideo this comment belongs to.
    *	@return GroupVideo object
    **/
    public function groupVideo()
    {
        $this->belongsToMany('oval\Models\GroupVideo');
    }

    /**
    *   One-to-One relationship.
    *   @return Feedback object
    **/
    public function feedback()
    {
        return $this->hasOne('oval\Models\Feedback');
    }

    /**
    *   One-to-One relationship.
    *   @return ConfidenceLevel object
    **/
    public function confidenceLevel()
    {
        return $this->hasOne('oval\Models\ConfidenceLevel');
    }

    /**
    *   Many-to-Many relationship.
    *   @return collection of Tag objects
    **/
    public function tags()
    {
        return $this->belongsToMany('oval\Models\Tag', 'comment_tags')->withTimeStamps();
    }

    /**
     * Private method that returns comments for group_video_id passed in that are visible to user_id passed in
     *
     * This method fetches comments with status "current" that are made by the user whose ID is passed in,
     * and "current" comments that are made by others that are visible to the user.
     * The returned array contains data ready for display.
     *
     * @param integer $group_video_id
     * @param integer $user_id
     * @return array Array of array with keys - id, user_id, name, description, tags, is_mine, privacy, updated_at, created_at
     */
    public static function groupVideoComments($group_video_id, $user_id)
    {
        $mine = static::where([
                        ['user_id', '=', $user_id],
                        ['group_video_id', '=', $group_video_id],
                        ['status', '=', 'current']
                    ])
                    ->get();
        $others = static::where([
                        ['user_id', '<>', $user_id],
                        ['group_video_id', '=', $group_video_id],
                        ['privacy', '<>', 'private'],
                        ['status', '=', 'current']
                    ])
                    ->get();
        foreach ($others as $key=>$val) {
            if($val->privacy == 'nominated') {
                $nominated = json_decode($val->visible_to);
                if (!empty($nominated)) {
                    if (!in_array($user_id, $nominated)) {
                        unset($others[$key]);
                    }
                }
            }
        }
        $all_comments = $mine->merge($others)->sortByDesc('updated_at')->values()->all();

        $comments = [];
        $course = GroupVideo::find($group_video_id)->course();
        foreach ($all_comments as $c) {
            $user = User::find($c->user_id);
            if (empty($user)) {
                $name = "Unknown User";
                $mine = false;
                $instructor = false;
            } else {
                $name = $user->fullName();
                $mine = $c->user_id==$user_id ? true : false;
                $instructor = $user->isInstructorOf($course);
            }

            $date = empty($c->updated_at) ? null : $c->updated_at->format('g:iA d M, Y');

            $comments[] = [
                "id" => $c->id,
                "user_id" => $user_id,
                "name" => $name,
                "description" => $c->description,
                "tags" => $c->tags->pluck('tag'),
                "is_mine" => $mine,
                "privacy" => $c->privacy,
                "updated_at" => $date,
                "by_instructor" => $instructor
            ];
        }
        return $comments;
    }
}
