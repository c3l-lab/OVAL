<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model class for table 'courses'.
 */
class Course extends Model
{
	use HasFactory;
	protected $table = 'courses';
	protected $fillable = ['name'];

	/**
	*	Many to many relationship.
	*	Returns enrolled users.
	*	@reurn collection of User objects
	**/
	public function enrolledUsers() {
		return $this->belongsToMany('oval\User', "enrollments");
	}

	/**
	*	Many-to-many relationship.
	*	Returns instructors of this Course
	*	@return collection of User objects
	**/
	public function instructors() {
		return $this->belongsToMany('oval\User', 'enrollments')->wherePivot('is_instructor', 1);
	}

	/**
	*	One-to-many relationship.
	*	Get Groups that belong to this course
	*	@return collection of Group objects
	**/
	public function groups() {
		return $this->hasMany('oval\Group');
	}

	/**
	*	Method to get the default group for this course
	*	@return Group object
	**/
	public function defaultGroup() {
		return $this->groups
					->where('moodle_group_id', null)
					->first();
	}

	/**
	*	Method to return all videos related to this course.
	*	This returns videos assigned to all groups in this course.
	*	@return collection of Video objects
	**/
	public function videos() {
		$videos = DB::table('videos')
					->join('group_videos', 'videos.id', '=', 'group_videos.video_id')
					->join('groups', 'group_videos.group_id', '=', 'groups.id')
					->select('videos.*')
					->where('groups.course_id', '=', $this->id)
					->groupBy('videos.id')
					->get();
		return $videos;
	}

	/**
	*	Method to return number of students enrolled in this course
	*	@return int number of students in course
	**/
	public function numStudents() {
		$enrolled = $this->enrolledUsers();
		$retVal = 0;
		foreach ($enrolled as $person) {
			if(!$person->isAnInstructor()) {
				$retVal++;
			}
		}
		return $retVal;
	}

	/**
	*	Method to return enrolled students in a form of collection of User objects
	*	@return collection of User objects
	**/
	public function enrolledStudents() {
		$all = $this->enrolledUsers;
		$students = $all->reject(function($user) {
			$user->is_instructor;
		});
		return $students;
	}
	protected static function newFactory()
    {
        return \Database\Factories\CourseFactory::new();
    }
}
