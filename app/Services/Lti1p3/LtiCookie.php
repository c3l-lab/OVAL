<?php

namespace oval\Services\Lti1p3;

use Packback\Lti1p3;

class LtiCookie implements Lti1p3\Interfaces\ICookie
{
    public function getCookie(string $name): ?string
    {
        return \Cookie::get($name);
    }

    public function setCookie(string $name, string $value, $exp = 3600, $options = []): void
    {
        \Cookie::queue($name, $value, $exp / 60, null, null, null, false, false, 'None');
    }
}
