<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

use oval\User;
use oval\Course;
use oval\Enrollment;
use oval\Group;
use oval\GroupMember;
use oval\GroupVideo;
use oval\LtiCredential;


const LTI_PASSWORD = '[lti_password]';


/**
 * Class inheriting ToolProvider from IMSGlobal's LIT library.
 * 
 * (This method runs as part of $tool->handleRequest() in LtiController class)
 * Get LtiCredential(credential of database where the LTI request originates from)
 * for the consumer_pk and import course and group info for OVAL
 * 
 * @uses IMSGlobal\LTI\ToolProvider
 * @author Ken
 * @see https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/wiki/Usage
 */
class OvalLtiProvider extends ToolProvider\ToolProvider {

function onLaunch() {
  // Authentication success (user has valid credentials)
  if ($this->user->isAuthenticated()) {
    Log::info('User authenticated successfully');

    // Retrieve user information from the LTI request
    $user_id = $this->user->getId();
    $roles = $this->user->roles;
    $isInstructor = $this->user->isStaff();

    // Use the request's context_id as the course ID
    $context_id = $this->context->getId();

    // Retrieve course information from the LTI request
    $course = Course::where('moodle_course_id', '=', $context_id)->first();
    if (empty($course)) {
      $course = new Course;
    }
    $course->moodle_course_id = $context_id;
    // You will need to find an alternative method to obtain or provide the consumer_id and other required information
    $course->name = $this->context->title;
    $course->save();

    Log::info("Course information saved");

    // Process user enrollments
    if ($isInstructor) {
      $enrollment = $course->enrollments()->where('user_id', $user_id)->first();
      if (empty($enrollment)) {
        $enrollment = new Enrollment;
      }
      $enrollment->user_id = $user_id;
      $enrollment->course_id = $course->id;
      $enrollment->role_id = 1; // Role ID for instructor
      $enrollment->save();

      Log::info("Enrollment information saved for instructor");
    } else {
      $enrollment = $course->enrollments()->where('user_id', $user_id)->first();
      if (empty($enrollment)) {
        $enrollment = new Enrollment;
      }
      $enrollment->user_id = $user_id;
      $enrollment->course_id = $course->id;
      $enrollment->role_id = 2; // Role ID for student
      $enrollment->save();

      Log::info("Enrollment information saved for student");
    }

    // You will need to find an alternative method to obtain or provide group information
    // Process group enrollments

  } else {
    // Authentication failed (user has invalid credentials or not authorized)
    $this->message = "Sorry, you don't have access to this application.";
  }
}

function onError() {
  // Error occurred during launch
  $this->message = "An error occurred during launch: " . $this->reason;
}

}
