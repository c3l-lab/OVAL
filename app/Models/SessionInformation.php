<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionInformation extends Model
{
    use HasFactory;

    protected $table = 'session_information';

    protected $fillable = [
        'id',
        'os',
        'doc_width',
        'doc_height',
        'layout',
        'init_screen_width',
        'init_screen_height'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
