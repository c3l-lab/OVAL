<?php
namespace oval\Services\Lti1p1;

use IMSGlobal\LTI\ToolProvider;
use oval\Models\LtiConsumer;

class DataConnector extends ToolProvider\DataConnector\DataConnector
{

  public function loadToolConsumer($consumer)
  {
      $ltiConsumer = LtiConsumer::where('consumer_key256', $consumer->getKey())->firstOrFail();
      $consumer->secret = $ltiConsumer->secret;
      $consumer->enabled = true;
      $now = time();
      $consumer->created = $now;
      $consumer->updated = $now;

      return true;
  }
}
