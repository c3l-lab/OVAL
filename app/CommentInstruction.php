<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table comment_instructions.
 * 
 * The table comment_instructions stores instruction for students 
 * on what to write in the "general comment"
 */
class CommentInstruction extends Model
{
    protected $table = 'comment_instructions';

    /**
    *   One-to-One relationship.
    *   The group_video this comment_instruction is for. 
    *   @return GroupVideo object
    **/
    public function group_video() {
        return $this->belongsTo('oval\GroupVideo');
    }
}
