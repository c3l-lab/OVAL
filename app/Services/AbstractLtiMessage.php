<?php

namespace oval\Services;

abstract class AbstractLtiMessage
{
    abstract public function getUserEmail();
    abstract public function getUserFirstName();
    abstract public function getUserLastName();
    abstract public function isUserInstructor();
    abstract public function getCourseId();
    abstract public function getCourseName();
}
