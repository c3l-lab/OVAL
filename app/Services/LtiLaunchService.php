<?php

namespace oval\Services;

use oval\Course;
use oval\Group;
use oval\User;

const LTI_PASSWORD = '[lti_password]';

class LtiLaunchService
{
  public $course;
  private AbstractLtiMessage $ltiMessage;

  public function __construct(AbstractLtiMessage $ltiMessage)
  {
    $this->ltiMessage = $ltiMessage;
  }

  public function loginUser()
  {
    $email = $this->ltiMessage->getUserEmail();
    if (empty($email)) {
      return;
    }
    $user = User::where('email', $email)->first();
    if (empty($user)) {
      $user = new User;
      $user->email = $email;
      $user->first_name = $this->ltiMessage->getUserFirstName();
      $user->last_name = $this->ltiMessage->getUserLastName();
      $user->role = 'O';
      $user->password = bcrypt(LTI_PASSWORD);
      $user->save();
    }
    \Auth::login($user);
  }

  public function updateOrCreateCourse()
  {
    $this->course = Course::firstOrNew(['platform_course_id' => $this->ltiMessage->getCourseId()]);
    $this->course->platform_course_id = $this->ltiMessage->getCourseId();
    $this->course->name = $this->ltiMessage->getCourseName();
    $this->course->save();
    if (empty($this->course->defaultGroup())) {
      $this->course->groups()->create(['name' => $this->ltiMessage->getCourseName() . ' - ' . 'Default Group']);
    }
  }

  public function addUserToGroup(Group $group)
  {
    $user = \Auth::user();
    $user->addToGroup($group);
  }

  public function enrolUser()
  {
    $user = \Auth::user();
    $user->enrollIn($this->course);

    if ($this->ltiMessage->isUserInstructor()) {
      $user->makeInstructorOf($this->course);
    }
  }
}
