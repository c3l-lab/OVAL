<?php

namespace oval;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtiRegistration extends Model
{
    use HasFactory;

    public function generateKey()
    {
        $private_key = openssl_pkey_new([
            "digest_alg" => "sha256",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        $this->public_key = \Crypt::encryptString(openssl_pkey_get_details($private_key)['key']);
        $private_key_pem = '';
        openssl_pkey_export($private_key, $private_key_pem);
        $this->private_key = \Crypt::encryptString($private_key_pem);
        $this->key_id = \Str::uuid();
    }

    public function getPrivateKey()
    {
        return \Crypt::decryptString($this->private_key);
    }
}
