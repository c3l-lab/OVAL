<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model class for database table lti2_consumer
 */
class LtiConsumer extends Model
{
    use HasFactory;

    protected $table = 'lti2_consumer';
    protected $primaryKey = 'consumer_pk';
    protected $dates = ['enable_from', 'enable_until'];
    public const CREATED_AT = 'created';
    public const UPDATED_AT = 'updated';

    public function passport()
    {
        return join(":", [
            $this->consumer_pk,
            $this->consumer_key256,
            $this->secret
        ]);
    }
}
