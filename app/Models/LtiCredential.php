<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use oval\Traits\Encriptable;

/**
 * Model class for table lti_credentials
 * @uses Traits\Encryptable
 * Fields 'username' and 'password' are encrypted/decrypted using trait
 */
class LtiCredential extends Model
{
    use Traits\Encryptable;

    protected $table = 'lti_credentials';
    protected $encryptable = ['username', 'password'];

    /**
     * One-to-One relationship.
     *
     * Returns Lti2Consumer object this db credential is for.
     * @return LtiConsumer
     */
    public function consumer() {
        return $this->belongsTo('oval\Models\LtiConsumer', 'consumer_pk', 'consumer_id');
    }
}
