<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'enrollments'
 */
class Enrollment extends Model
{
    protected $table = 'enrollments';
    protected $fillable = ['course_id', 'user_id'];
}
