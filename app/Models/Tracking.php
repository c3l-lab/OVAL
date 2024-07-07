<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model class for table 'trackings'.
 */
class Tracking extends Model
{
    use HasFactory;

    protected $table = 'trackings';
    protected $fillable = [
      'group_video_id', 'user_id', 'event', 'target', 'video_time', 'info', 'event_time', 'ref_id', 'ref_type', 'session_id'
    ];
    public $timestamps = false;

    /**
    *	One-to-Many relationship (inverse)
    *	@return GroupVideo object
    */
    public function group_video()
    {
        return $this->belongsTo('oval\Models\GroupVideo');
    }

    /**
    *	One-to-Many relationship (inverse)
    *	@return User object
    **/
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
