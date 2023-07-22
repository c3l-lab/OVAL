<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for database table lti2_consumer
 */
class LtiConsumer extends Model
{
    protected $table = 'lti2_consumer';
    protected $primaryKey = 'consumer_pk';
    protected $dates = ['enable_from', 'enable_until'];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    public function credential() {
        return $this->hasOne('oval\Models\LtiCredential', 'consumer_id', 'consumer_pk');
    }

    public function passport()
    {
        return join(":", [
            $this->consumer_pk,
            $this->consumer_key256,
            $this->secret
        ]);
    }
}
