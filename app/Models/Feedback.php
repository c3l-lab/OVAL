<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'feedbacks'.
 *
 * Feedback contains value selected for "Point" (question checking if certain things were written about in comment).
 * This only exists for comment on group_video that has Point attached to it.
 */
class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $fillable = ['user_id', 'point_id'];

    /**
    *   One-to-Many relationship (inverse)
    *   @return collection of Point objects
    **/
    public function forPoint()
    {
        return $this->belongsTo('oval\Models\Point');
    }

    /**
    *   One-to-One relationship (inverse)
    *   @return Comment object
    **/
    public function forComment()
    {
        return $this->belongsTo('oval\Models\Comment');
    }

}
