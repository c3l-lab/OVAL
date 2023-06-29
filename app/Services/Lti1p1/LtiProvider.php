<?php

namespace oval\Services\Lti1p1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use IMSGlobal\LTI\ToolProvider;
use oval\Services\Lti1p1\DataConnector;

class LtiProvider extends ToolProvider\ToolProvider
{
    public function __construct(Request $request)
    {
        $data_connector = new DataConnector(null);
        parent::__construct($data_connector);

        global $_POST;
        $_POST = $request->all();

        /**
         * oat-sa/imsglobal-lti requires these variables to verify the request
         * from LTI consumer, but I don't know why these variables are either
         * not set or incorrect on server, so I set them exipitly.
         */
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '';
        $_SERVER['SERVER_NAME'] = $request->getHost();

        $this->setParameterConstraint('oauth_consumer_key', TRUE, 50, array('basic-lti-launch-request', 'ContentItemSelectionRequest', 'DashboardRequest'));
        $this->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
        $this->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
        $this->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));
    }

    function onError()
    {
        Log::error('Error in LTI handling', ['error_message' => $this->reason, 'details' => $this->details]);
    }
}
