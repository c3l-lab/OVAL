<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'point_instruction'.
 *
 * The table stores instruction for students about the point, and displays in the form to check off "points".
 */
class PointInstruction extends Model
{
    protected $table = 'point_instructions';

    protected $fillable = ['group_video_id'];

    /**
    *	One-to-One relationship
    *	@return GroupVideo this instruction belongs to
    **/
    public function forGroupVideo() {
    	return $this->belongsTo('oval\Models\GroupVideo');
    }
}
