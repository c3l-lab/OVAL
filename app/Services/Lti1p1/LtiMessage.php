<?php

namespace oval\Services\Lti1p1;
use IMSGlobal\LTI\ToolProvider\User;
use oval\Services\AbstractLtiMessage;

class LtiMessage extends AbstractLtiMessage {
  private $launchData;
  private User $ltiUser;

  public function __construct($launchData, User $ltiUser) {
    $this->launchData = $launchData;
    $this->ltiUser = $ltiUser;
  }

  public function getUserEmail() {
    return $this->ltiUser->email;
  }

  public function getUserFirstName() {
    return $this->ltiUser->firstname;
  }
  public function getUserLastName() {
    return $this->ltiUser->lastname;
  }

  public function isUserInstructor() {
    return $this->ltiUser->isAdmin() || $this->ltiUser->isStaff();
  }
  public function getCourseId() {
      return $this->launchData['context_id'];
  }
  public function getCourseName() {
      return $this->launchData['context_title'];
  }
}
