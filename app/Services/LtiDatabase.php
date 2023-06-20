<?php
namespace oval\Services;

use Packback\Lti1p3;
use oval\LtiRegistration;

class LtiDatabase implements Lti1p3\Interfaces\IDatabase
{
    public function findRegistrationByIssuer($issuer, $client_id = null)
    {
        $registration = LtiRegistration::where('issuer', $issuer)->where('client_id', $client_id)->first();
        if (empty($registration)) {
            return false;
        }
        return Lti1p3\LtiRegistration::new()
            ->setAuthLoginUrl($registration->auth_login_url)
            ->setAuthTokenUrl($registration->auth_token_url)
            // ->set_auth_server($_SESSION['iss'][$iss]['auth_server'])
            ->setClientId($client_id)
            ->setKeySetUrl($registration->keyset_url)
            ->setKid($registration->key_id)
            ->setIssuer($registration->issuer)
            ->setToolPrivateKey(\Crypt::decryptString($registration->private_key));
    }

    public function findDeployment($issuer, $deployment_id, $client_id = null)
    {
        $registration = LtiRegistration::where('issuer', $issuer)->where('client_id', $client_id)->first();
        if (empty($registration)) {
            return false;
        }
        return Lti1p3\LtiDeployment::new()
            ->setDeploymentId($registration->deployment_id);
    }
}
