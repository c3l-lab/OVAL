<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Annotation extends Model
{
    use HasFactory;
    protected $tablename = 'annotations';
    protected $fillable = ['group_video_id','user_id','start_time'];

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
    protected static function newFactory()
    {
        return \Database\Factories\AnnotationFactory::new();
    }
}
