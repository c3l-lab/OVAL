<?php

namespace oval\Services;

abstract class AbstractLtiMessage
{
  public abstract function getUserEmail();
  public abstract function getUserFirstName();
  public abstract function getUserLastName();
  public abstract function isUserInstructor();
  public abstract function getCourseId();
  public abstract function getCourseName();
}
