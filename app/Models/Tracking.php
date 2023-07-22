<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'trackings'.
 */
class Tracking extends Model
{

    protected $table = 'trackings';
    protected $fillable = [
      'group_video_id', 'user_id', 'event', 'target', 'info', 'event_time'
    ];
    public $timestamps = false;

    /**
    *	One-to-Many relationship (inverse)
    *	@return GroupVideo object
    */
    public function group_video() {
    	return $this->belongsTo('oval\Models\GroupVideo');
    }

    /**
    *	One-to-Many relationship (inverse)
    *	@return User object
    **/
    public function user() {
    	return $this->belongsTo('oval\Models\User');
    }
}
