<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model class for 'quiz_result'
 * @author Max
 * TODO: add relationship
 */
class QuizResult extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quiz_result';

    protected $fillable = ['user_id', 'identifier', 'media_type', 'quiz_data'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
