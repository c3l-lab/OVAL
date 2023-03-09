<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    // Insert code here to handle incoming connections - use the user,
    // context and resourceLink properties of the class instance
    // to access the current user, context and resource link.
    $this->user->save();

    //--find user with email address
    //--leave it as is if already exists
    $user = User::where('email', '=', $this->user->email)->first();
    if (empty($user)) {
      $user = new User;
      $user->email = $this->user->email;
      $user->first_name = $this->user->firstname;
      $user->last_name = $this->user->lastname;
      $user->role = $this->getOvalUserRole();
      $user->password = bcrypt(LTI_PASSWORD);
      $user->save();
    }
    Auth::login($user);

    //--set db credentials for the LTI source
    //--get values from DB and set the config
    $consumer_id = $this->consumer->getRecordId();
    $cred = LtiCredential::where('consumer_id', '=', $consumer_id)->first();
    config(["database.connections.moodle" => [
      "driver" => $cred->db_type,
      "host" => $cred->host,
      "port" => $cred->port,
      "database" => $cred->database,
      "username" => $cred->username,
      "password" => $cred->password,
      "prefix" => $cred->prefix,
      "charset" => 'utf8'
    ]]);
    
    $course = Course::where('moodle_course_id', '=', intval($_POST['context_id']))->first();
    $course_id = empty($course) ? -1 : $course->id;
    
    // update course information
    $enrollments = DB::connection('moodle')->table('user_enrolments')
          -> leftJoin('enrol', 'user_enrolments.enrolid', '=', 'enrol.id')
          -> leftJoin('course', 'enrol.courseid', '=', 'course.id')
          -> select('course.id', 'course.fullname', 'course.startdate', 'course.enddate')
          -> whereNotNull('course.id')
          -> where([
            ['user_enrolments.userid', '=', $this->user->getId()],
          ])
          ->get();

    foreach ($enrollments as $enrol) {
      set_time_limit(30);
      // $course = Course::firstOrNew(['id' => $enrol->id]);
      $course = Course::where('moodle_course_id', '=', $enrol->id)->first();
      if (empty($course)) {
        $course = new Course;
      }
      $course->moodle_course_id = $enrol->id;
      $course->consumer_id = $consumer_id;
      $course->name = $enrol->fullname;
      $course->start_date = date('Y-m-d H:i:s', $enrol->startdate);
      $course->end_date = date('Y-m-d H:i:s', $enrol->enddate);
      $course->save();

      $enrollment = Enrollment::where([
        ['course_id', '=', $course->id],
        ['user_id', '=', $user->id]
      ])->first();
      if (empty($enrollment)) {
        $enrollment = new Enrollment;
        $enrollment->course_id = $course->id;
        $enrollment->user_id = $user->id;
      }
      if ($course->id == $course_id) {
        $enrollment->is_instructor = $this->isInstructor();
      }
      $enrollment->save();

      if ($this->isInstructor()) {
        // update groups information
        $groups = DB::connection('moodle')->table('groups')
              -> select('id', 'name')
              -> whereNotNull('id')
              -> where([
                ['courseid', '=', $course->moodle_course_id],
              ])
              ->get();
      } else {
        $groups = DB::connection('moodle')->table('groups')
              -> select('groups.id', 'groups.name')
              -> leftJoin('groups_members', 'groups.id', '=', 'groups_members.groupid')
              -> whereNotNull('groups.id')
              -> where([
                ['groups.courseid', '=', $course->id],
                ['groups_members.userid', '=', $user->id],
              ])
              ->get();
      }

      // Create a default Course Group if no group is found for the course
      $g = Group::firstOrCreate(['moodle_group_id' => NULL, 'course_id' => $course->id], ['name'=>'Course Group']);

      // Add current people into the Course GroupMember
      $g->addMember($user);

      foreach ($groups as $group) {
        $g = Group::firstOrNew(['moodle_group_id' => $group->id]);
        $g->name = $group->name;
        $g->course_id = $course->id;
        $g->moodle_group_id = $group->id;
        $g->save();

        $g->addmember($user);
      }
    }
  }

  /**
   * Method to return the value for oval.user table's role column.
   * 
   * @return string 'A' if admin, 'O' if not
   */
  function getOvalUserRole() {
		if ($this->user->isAdmin()) return 'A';
		return 'O';
	}

  /**
   * Method to check if the user is an instructor' in OVAL
   * 
   * @return boolean true if Moodle role is admin or staff, false for others. 
   */
  function isInstructor() {
		if ($this->user->isAdmin()) return true;
  	if ($this->user->isStaff()) return true;
		return false;
	}
}

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
     * @see https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/wiki/Usage
     * @uses IMSGlobal\LTI\ToolProvider\ToolConsumer
     * @uses OvalLtiProvider::handleRequest()
     * 
     * @param Request $req
     * @return Illuminate\Http\RedirectResponse
     * 
     */
    public function launch(Request $req) {
      global $_POST;
      $_POST = $req->all();

      try {
        $db_config = DB::getConfig();
        $conn_str = $db_config['driver'] . ':host=' . $db_config['host'] . ';port=' . $db_config['port'] . ';dbname=' . $db_config['database'];
        $pdo = new \PDO($conn_str, $db_config['username'], $db_config['password']);
      } catch (PDOException $e) {
        return 'Connection failed: ' . $e->getMessage();
      }
      $db_connector = DataConnector\DataConnector::getDataConnector('', $pdo);

      $tool = new OvalLtiProvider($db_connector);
      $tool->setParameterConstraint('oauth_consumer_key', TRUE, 50, array('basic-lti-launch-request', 'ContentItemSelectionRequest', 'DashboardRequest'));
      $tool->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
      $tool->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
      $tool->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));
      $tool->handleRequest();

      // close PDO connection
      $pdo = null;

      $lti_user = Auth::user();

      $link_id = $req->resource_link_id;
      $group_video = GroupVideo::where([
                        ['moodle_resource_id', '=', $link_id],
                        ['status', '=', 'current']
                    ])->first();
      $course = Course::where('moodle_course_id', '=', intval($req->context_id))->first();
      if(empty($course)) {
        return view('pages.message-page', ['title'=>'ERROR', 'message'=>'Oops, something is wrong. Please try again later.']);
      }
      
      if($lti_user->isInstructorOf($course)){
        //--if instructor, redirect to select video page 
        return redirect()->secure('/select-video/'.$link_id.(!empty($group_video) ? '/'.$group_video->id : ""));
      }
      elseif(!empty($group_video)) {
        //--if student and lti-resource-id is linked to group_video, redirect to it
        return redirect()->secure('/view/'.$group_video->id);
      }
      else {
        //--else redirect to course if student & no group_video associated to link
        return redirect()->secure('/course/'.$course->id);
      }
      
    }
}
