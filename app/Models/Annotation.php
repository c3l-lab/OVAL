<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Annotation extends Model
{
    use HasFactory;
    protected $tablename = 'annotations';
    protected $fillable = ['group_video_id', 'user_id', 'start_time'];

    /**
     *	One-to-many(inverse) relationship
     *
     *	Returns who wrote this annotation.
     *	@return User object
     **/
    public function writtenBy()
    {
        return $this->belongsTo('oval\Models\User');
    }

    /**
     *	One-to-many(inverse) relationship
     *
     *	Returns the GroupVideo this Annotation belongs to
     *	@return GroupVideo ojbect
     **/
    public function groupVideo()
    {
        return $this->belongsToMany('oval\Models\GroupVideo');
    }

    /**
     *	Many-to-Many relationship
     *
     *	Returns Tags for this annotation.
     *	@return collection of Tag objects
     **/
    public function tags()
    {
        return $this->belongsToMany('oval\Models\Tag', 'annotation_tags')->withTimeStamps();
    }

    public static function groupVideoAnnotations($group_video_id, $user_id)
    {
        $all_annotations = static::where([
            ['group_video_id', "=", $group_video_id],
            ['status', '=', 'current']
        ])->get();

        foreach ($all_annotations as $key => $a) {
            $visible = false;
            $privacy = $a->privacy;
            $mine = $a->user_id == $user_id ? true : false;
            $visible = ($mine || $privacy == "all") ? true : false;
            if ($privacy == "nominated") {
                $audience = json_decode($a->visible_to);
                if (!empty($audience)) {
                    if (in_array($user_id, $audience)) {
                        $visible = true;
                    }
                }
            }
            if (!$visible) {
                $all_annotations->forget($key);
            }
        }
        return $all_annotations;
    }
}
