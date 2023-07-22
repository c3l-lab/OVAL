<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'tags'
 */
class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['tag'];

    /**
    *   Many-to-Many relationship
    *   @return collection of Comment objects
    **/
    public function comments()
    {
        return $this->belongsToMany('oval\Models\Comment')->withTimeStamps();
    }

    /**
    *   Many-to-Many relationship
    *   @return collection of Annotation objects
    **/
    public function annotations()
    {
        return $this->belongsToMany('oval\Models\Annotation')->withTimeStamps();
    }
}
