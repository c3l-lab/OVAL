<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'quiz_createion'.
 * @author Max
 * TODO: add relationship
 */
class QuizCreation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quiz_creation';

    protected $fillable = ['creator_id', 'identifier', 'media_type', 'quiz_data','visable'];


}
