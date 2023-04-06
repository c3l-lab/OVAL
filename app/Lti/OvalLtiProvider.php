<?php

namespace oval\Lti;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IMSGlobal\LTI\ToolProvider;

use oval\User;
use oval\Course;
use oval\Enrollment;
use oval\Group;
use oval\GroupMember;
use oval\GroupVideo;
use oval\Lti\NoDatabaseDataConnector;

const LTI_PASSWORD = '[lti_password]';

class OvalLtiProvider extends ToolProvider\ToolProvider {
    public function __construct(NoDatabaseDataConnector $data_connector)
    {
        parent::__construct($data_connector);
        Log::debug('OvalLtiProvider initialized with NoDatabaseDataConnector');
    }
    
    function onLaunch() {
        Log::debug('Starting onLaunch method');
    
        try {
            Log::debug('Attempting to save user');
            $this->user->save();
            Log::debug('User details', ['user' => $this->user]);
    
            $user = User::where('email', '=', $this->user->email)->first();
            if (empty($user)) {
                Log::debug('Creating new user');
                $user = new User;
                $user->email = $this->user->email;
                $user->first_name = $this->user->firstname;
                $user->last_name = $this->user->lastname;
                $user->role = $this->getOvalUserRole();
                $user->password = bcrypt(LTI_PASSWORD);
                $user->save();
            }
            Log::debug('Attempting to log in user');
            Auth::login($user);
            Log::debug('User logged in successfully');
    
            // The LMS database connection and related code have been removed.
            // You will need to find an alternative method to obtain or provide the course, enrollment, and group information.
    
        } catch (\Exception $e) {
            Log::error('Exception during onLaunch: ' . $e->getMessage() . '; Stack trace: ' . $e->getTraceAsString());
            $this->message->setError('Sorry, there was an error connecting you to the application.');
        }
    }
    

    function getOvalUserRole() {
        if ($this->user->isAdmin()) return 'A';
        return 'O';
    }

    function isInstructor() {
        if ($this->user->isAdmin()) return true;
        if ($this->user->isStaff()) return true;
        return false;
    }

    function onError() {
        Log::error('Error in LTI handling', ['error_message' => $this->message]);
    }
}