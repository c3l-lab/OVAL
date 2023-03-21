<?php

namespace oval;

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
    public function user() {
    	return $this->belongsTo('oval\User');
    }
    
    /**
    *   One-to-Many relationship (inverse).
    *	Returns the groupvideo this comment belongs to.
    *	@return GroupVideo object
    **/
    public function groupVideo() {    	
		$this->belongsToMany('oval\GroupVideo');
    }	

    /**
    *   One-to-One relationship.
    *   @return Feedback object
    **/
    public function feedback() {
        return $this->hasOne('oval\Feedback');
    }

    /**
    *   One-to-One relationship.
    *   @return ConfidenceLevel object
    **/
    public function confidenceLevel() {
        return $this->hasOne('oval\ConfidenceLevel');
    }

    /**
    *   Many-to-Many relationship.
    *   @return collection of Tag objects
    **/
    public function tags() {
        return $this->belongsToMany('oval\Tag', 'comment_tags')->withTimeStamps();
    }
    protected static function newFactory()
    {
        return \Database\Factories\CommentFactory::new();
    }
}
