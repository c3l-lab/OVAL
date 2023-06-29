<?php

namespace oval\Services;

use oval\Course;
use oval\Group;
use oval\User;

class LtiLaunchService
{
  public $course;


  public function updateOrCreateCourse(string $platformCourseId, string $name)
  {
    $this->course = Course::firstOrNew(['platform_course_id' => $platformCourseId]);
    $this->course->platform_course_id = $platformCourseId;
    $this->course->name = $name;
    $this->course->save();
    if (empty($this->course->defaultGroup())) {
      $this->course->groups()->create(['name' => $name . ' - ' . 'Default Group']);
    }
  }

  public function addToGroup(User $user, Group $group) {
    $user->addToGroup($group);
  }

  public function enrolUser(User $user, $isInstructor)
  {
    $user->enrollIn($this->course);

    if ($isInstructor) {
      $user->makeInstructorOf($this->course);
    }
  }
}
