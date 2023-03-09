<?php

namespace oval;

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
        return $this->hasOne('oval\LtiCredential', 'consumer_id', 'consumer_pk');
    }
}
