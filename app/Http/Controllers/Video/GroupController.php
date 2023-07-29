<?php

namespace oval\Http\Controllers\Video;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\Video;

class GroupController extends Controller
{
    public function index(Video $video)
    {
        $groups = $video->groups;
        return [
          'groups' => $groups,
        ];
    }
}
