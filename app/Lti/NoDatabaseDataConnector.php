<?php

namespace oval\Lti;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use Illuminate\Http\Request;

class NoDatabaseDataConnector extends DataConnector
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequestParameter($name, $default = '')
    {
        return $this->request->input($name, $default);
    }
}
