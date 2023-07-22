<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'transcripts'
 */
class Transcript extends Model
{
    protected $table = "transcripts";
    public $timestamps = false;

    /**
     * One-to-One relationship.
     * @return Video
     */
    public function video() {
        return $this->belongsTo("oval\Models\Video");
    }
}
