<?php

namespace oval\Services\Lti1p3;

use oval\Services\AbstractLtiMessage;
use Packback\Lti1p3\LtiConstants;

class LtiMessage extends AbstractLtiMessage
{
  private $launchData;

  public function __construct($launchData)
  {
    $this->launchData = $launchData;
  }

  public function getUserEmail()
  {
    return $this->launchData['email'] ?? null;
  }

  public function getUserFirstName()
  {
    return $this->launchData['preferred_username'] ?? 'Unknow';
  }

  public function getUserLastName()
  {
    return 'student';
  }

  public function isUserInstructor()
  {
    return $this->incldueRole(LtiConstants::SYSTEM_ADMINISTRATOR) || $this->incldueRole(LtiConstants::INSTITUTION_INSTRUCTOR);
  }

  public function getCourseId()
  {
    if (empty($this->context())) {
      return null;
    }
    return $this->context()['id'];
  }

  public function getCourseName()
  {
    if (empty($this->context())) {
      return null;
    }
    return $this->context()['title'];
  }

  private function context()
  {
    return $this->launchData[LtiConstants::CONTEXT] ?? null;
  }

  private function incldueRole($targetRole)
  {
    foreach ($this->launchData[LtiConstants::ROLES] as $role) {
      if ($role == $targetRole) {
        return true;
      }
    }
  }
}
