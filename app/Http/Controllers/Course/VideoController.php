<?php

namespace oval\Http\Controllers\Course;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\Course;

class VideoController extends Controller
{
    public function index(Course $course)
    {
        $videos = $course->videos();
        return compact('videos');
    }
}
