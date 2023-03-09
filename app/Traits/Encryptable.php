<?php

namespace oval\Traits;
use Illuminate\Support\Facades\Crypt;

/**
 * Use this trait in Model to have fields encrypted before saving to database
 * and decrypted as read from database.
 * The model class has to have protected variable $encryptable
 * that holds keys(column name) for the value
 */
trait Encryptable {
 
    /**
     * Decrypts the value as it is retrieved from database
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
 
        if (in_array($key, $this->encryptable) && ( ! is_null($value)))
        {
            $value = Crypt::decrypt($value);
        }
 
        return $value;
    }
 
    /**
     * Encryptss value before saving to database
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable))
        {
            $value = Crypt::encrypt($value);
        }
 
        return parent::setAttribute($key, $value);
    }
} 