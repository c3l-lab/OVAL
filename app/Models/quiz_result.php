<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for 'quiz_result'
 * @author Max
 * TODO: add relationship
 */
class quiz_result extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quiz_result';

    protected $fillable = ['user_id', 'identifier', 'media_type', 'quiz_data'];
}
