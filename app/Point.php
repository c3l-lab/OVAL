<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'points'.
 * 
 * The table stores items students are supposed to write about in comment.
 */
class Point extends Model
{
    protected $table = 'points';
    
    protected $fillable = ['group_video_id', 'description'];
    
    /**	
    *	One-to-Many relationship (Inverse)
    *	@return GroupVideo this Point belongs to
    **/
    public function groupVideo() {
        return $this->belongsTo('oval\GroupVideo');
    }
    
    /**
    *	One-to-Many relationship
    *	@return Feedback (answer) to this Point
    **/
    public function feedbacks() {
	    return $this->hasMany('oval\Feedback');
    }	

    /**
     * Method to get number of "yes" feedback for this point.
     * @return int
     */
	public function numYes() {
        return $this->feedbacks->where('answer', '=', true)->count();
    }
	
}
