<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model class for table "confidence_levels".
 * Confidence_levels table is used to store the optional entry of "level of confidence".
 * This is only applicable if there is "Points" attached to the group_video.
 */
class ConfidenceLevel extends Model
{
    protected $table = "confidence_levels";
    protected $fillable = ['group_video_id', 'user_id', 'level'];

    /**
    *   One-to-One relationship (inverse).
    *   The comment this ConfidenceLevel is about.
    *   @return Comment object
    **/
    public function forComment()
    {
        return $this->belongsTo('oval\Models\Comment');
    }
}
