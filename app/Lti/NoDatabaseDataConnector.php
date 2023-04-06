<?php

namespace oval\Lti;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoDatabaseDataConnector extends DataConnector
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        Log::debug('NoDatabaseDataConnector initialized with Request data', ['requestData' => $this->request->all()]);
    }

    public function getRequestParameter($name, $default = '')
    {
        return $this->request->input($name, $default);
    }
}
