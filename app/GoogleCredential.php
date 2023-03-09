<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'google_credentials'.
 * 
 * The table is used to store credentials to access Youtube Data API. 
 */
class GoogleCredential extends Model
{
    protected $table = "google_credentials";
    
    protected $casts = [
        'access_token' => 'array',
    ];
    
}
