<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LonghornOpen\LaravelCelticLti\LtiToolProvider;
use LonghornOpen\LaravelCelticLti\LtiContext;

use oval\User;
use oval\Course;
use oval\Enrollment;
use oval\Group;
use oval\GroupMember;
use oval\GroupVideo;
use oval\LtiCredential;

const LTI_PASSWORD = '[lti_password]';

/**
 * This class handles LTI connection.
 * @author Ken
 */
class LtiController extends Controller
{
    public function __construct() {

    }

    /**
     * Method called from route /lti - the route used when user clicks on LTI link on Moodle
     * 
     * This method uses LTI library to check authentication,
     * saves info coming from LTI request,
     * then redirects instructor to /select-video page,
     * student to /view (or /course/{course_id}).
     * 
     * @see https://github.com/longhornopen/laravel-celtic-lti
     * @param Request $req
     * @return Illuminate\Http\RedirectResponse
     * 
     */
    public function launch(Request $req) {
        // Handle LTI request and create a context object
        $context = LtiToolProvider::handleLaunchRequest($req);

        // Get the LTI user and save it to the local user
        $ltiUser = $context->getUser();
        $user = User::where('email', '=', $ltiUser->email)->first();

        if (empty($user)) {
            $user = new User;
            $user->email = $ltiUser->email;
            $user->first_name = $ltiUser->firstname;
            $user->last_name = $ltiUser->lastname;
            $user->role = $ltiUser->isAdmin() ? 'A' : 'O';
            $user->password = bcrypt(LTI_PASSWORD);
            $user->save();

            Log::info('User created', ['user' => $user->toArray()]);
        }

        Auth::login($user);

        // Get course information from LTI request
        $context = $lti->getContext();
        $contextId = intval($context->getId());
        $contextTitle = $context->getTitle();
        $contextLabel = $context->getLabel();
        $contextStartDate = $context->getStartDate();
        $contextEndDate = $context->getEndDate();

        Log::info('Course information from LTI request', [
            'contextId' => $contextId,
            'contextTitle' => $contextTitle,
            'contextLabel' => $contextLabel,
            'contextStartDate' => $contextStartDate,
            'contextEndDate' => $contextEndDate
        ]);

        $course = Course::where('moodle_course_id', '=', $contextId)->first();

        if (empty($course)) {
            $course = new Course;
            $course->moodle_course_id = $contextId;
            $course->name = $contextTitle;
            $course->start_date = $contextStartDate;
            $course->end_date = $contextEndDate;
            $course->save();
        }

        Log::info('Course information', ['course' => $course->toArray()]);

        // Update user enrollment
        $enrollment = Enrollment::where([
            ['course_id', '=', $course->id],
            ['user_id', '=', $user->id]
        ])->first();
   
        if (empty($enrollment)) {
            $enrollment = new Enrollment;
            $enrollment->course_id = $course->id;
            $enrollment->user_id = $user->id;
        }
    
        if ($ltiUser->isAdmin() || $ltiUser->isStaff()) {
            $enrollment->is_instructor = true;
        }
        $enrollment->save();
    
        Log::info('Enrollment information', ['enrollment' => $enrollment->toArray()]);
    
        // Handle group information based on the user role
        $defaultGroup = Group::firstOrCreate(['moodle_group_id' => NULL, 'course_id' => $course->id], ['name'=>'Course Group']);
        $defaultGroup->addMember($user);
    
        $link_id = $req->resource_link_id;
        $group_video = GroupVideo::where([
            ['moodle_resource_id', '=', $link_id],
            ['status', '=', 'current']
        ])->first();
    
        if ($ltiUser->isAdmin() || $ltiUser->isStaff()) {
            // Redirect instructor to select video page
            return redirect()->secure('/select-video/' . $link_id . (!empty($group_video) ? '/' . $group_video->id : ""));
        } elseif (!empty($group_video)) {
            // Redirect student to the group video
            return redirect()->secure('/view/' . $group_video->id);
        } else {
            // Redirect student to the course page
            return redirect()->secure('/course/' . $course->id);
        }
    }
 }
    