<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModelTest extends TestCase
{
	/**
	*	After test, database will be reset to the state before test.
	**/
	//use DatabaseMigrations;
	
	
    public function testExample()
    {
        $this->assertTrue(true);
    }
    
/*   public function testMakeUser() {
        $user = factory(oval\User::class) -> create();
        echo "USER: ".$user->first_name." ".$user->last_name." created. (id-".$user->id." email-".$user->email.")\n";
		$this->seeInDatabase('users', ['id'=>1]);
    }
*/
    
/*    public function testMakeAnnotation() {
    	$annotation = factory(oval\Annotation::class)->create();
    	echo "\n\nANNOTATION:\n";
    	echo "annotation_id = ".$annotation->annotation_id."\n";
    	echo "video_id = ".$annotation->video_id."\n";
    	echo "user_id = ".$annotation->user_id."\n";
    	echo "start_time = ".$annotation->start_time."\n";
    	echo "description = ".$annotation->description."\n";
    	echo "tags = ".$annotation->tags."\n";
    	echo "is_private = ".$annotation->is_private."\n";
    	echo "is_deleted = ".$annotation->is_deleted."\n";
    	echo "parent_id = ".$annotation->parent_id."\n";
    	echo "child_id = ".$annotation->child_id."\n";
    	echo "created_at = ".$annotation->created_at."\n";
    	echo "updated_at = ".$annotation->updated_at."\n";
    	//print_r($annotation);
     }*/
    
/*	public function testMakeVideo() {
    	$video = factory(oval\Video::class)->create();
    	
    	echo "\n\nVIDEO:\n";
    	echo "video_id = ".$video->video_id."\n";
    	echo "user_id = ".$video->user_id."\n";
    	echo "title = ".$video->title."\n";
    	echo "description = ".$video->description."\n";
    	echo "duration = ".$video->duration."\n";
    	echo "thumbnail_url = ".$video->thumbnail_url."\n";
    	echo "media_type = ".$video->media_tyoe."\n";
    	echo "point_one = ".$video->point_one."\n";
    	echo "point_two = ".$video->point_two."\n";    	
    	echo "point_three = ".$video->point_three."\n";
    	echo "created_at = ".$video->created_at."\n";
    }*/
    
/*    public function testVideoViewedBy() {   	
    	factory(oval\User::class, 5) -> create() -> each(function($u) {
    		$u->viewedVideos()->save(factory(oval\Video::class)->create());
    	});
    	
    	$user = oval\User::find(1);
    	echo "\nuser = ".$user->fullName()."\n";
    	echo "Videos viewed by this user...\n";
    	foreach ($user->viewedVideos as $v) {
    		echo "- ".$v;
    		echo "\n\nViewed by...";
    		foreach ($v->viewedBy as $u) {
    			echo "-".$u->fullName()."\n";
    		}
    	}
    	echo "__________________\n";
    }
*/  
    
/*    public function testEnrollment() {
    	$user = factory(oval\User::class)->create()->each(function($u) {
    		echo "user: ".$u->fullName()."\n";
    		$u->enrolledIn()->save(factory(oval\Course::class)->make());
    		echo "\tenrolled in: ".$u->enrolledIn."\n";
    	});
		echo "__________________\n";

    }
*/
/*	public function testEnrollUser() {
		$user = factory(oval\User::class)->create();
		echo "\nUser created (full name: ".$user->fullName().")\n";
		$course = factory(oval\Course::class)->create();
		echo "Enroll in same course twice (course name: ".$course->name."...)\n";
		$user->enrollIn($course);
		$user->enrollIn($course);
		echo "Enrollment List:\n";
		foreach($user->enrolledIn as $c) {
			echo "\t * ".$c->name."\n";
		}
	}
*/
/*	public function testCourseInstructors() {
		$user = oval\User::create(array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'admin@oval.com',
			'password' => 'ABC',
		));
		$course = oval\Course::create(array(
			'name' => 'Test Course 1'
		));
		echo "\nMake him instructor...";		////
		$user->makeInstructorOf($course);
		echo "\nMake him instructor again...";		////
		$user->makeInstructorOf($course);
		
		$courses = oval\Course::all();
		foreach($courses as $c) {
			echo "\ncourse: ".$c->name."\n";
			echo "instructors: \n";
			$instructors = $c->instructors;
			foreach($instructors as $i) {
				echo "\t-".$i->fullName()."\n";
			}
			
		}
		echo "__________________\n";
	}
  */
/*	public function testMakeGroup() {
		//echo "\nNew group name= Group 1\n";
		$group = factory(oval\Group::class)->create();
		$this->seeInDatabase('groups', ['id'=>$group->id]);
		print_r($group);
	}
*/

/*	public function testAddUserToGroupThenRemove() {
		$user = factory(oval\User::class)->create();
		$group = factory(oval\Group::class)->create();
		echo "\nGroup: ".$group->name." created\n";
		$group2 = factory(oval\Group::class)->create();
		echo "Group: ".$group2->name." created\n";

		$user->groupMemberOf()->attach($group);
		$user->groupMemberOf()->attach($group2);
		echo "Enrollment List:\n";
		foreach ($user->groupMemberOf as $g) {
			echo "\t- ".$g->name."\n";
		}
		echo "Removing user from the first group...\n";
		$user->removeFromGroup($group);
		$user->load('groupMemberOf');
		echo "Enrollment List:\n";
		foreach ($user->groupMemberOf as $g) {
			echo "\t- ".$g->name."\n";
		}
		
		$student = factory(oval\User::class)->create();
		echo "Add a student(".$student->fullName()." to second group\n";
		//$student->groupMemberOf()->attach($group2);
		$group2->addMember($student);
		$group2->load('members');
		echo "Enrolled users of 2nd group:\n";
		foreach ($group2->members as $u) {
			echo "\t- ".$u->fullName()."\n";
		}
	}  
	*/
	
	/*public function testGroupVideos() {
		$u1 = factory(oval\User::class)->create();
		$u2 = factory(oval\User::class)->create();
		
		$g1 = factory(oval\Group::class)->create();
		$g2 = factory(oval\Group::class)->create();
		
		$v1 = factory(oval\Video::class)->create();
		$v2 = factory(oval\Video::class)->create();
		$v3 = factory(oval\Video::class)->create();
		$v4 = factory(oval\Video::class)->create();
		$v5 = factory(oval\Video::class)->create();
		$v6 = factory(oval\Video::class)->create();
		
		echo "\nadd user1(".$u1->first_name.") to group1(".$g1->name.")\n";
		$u1->addToGroup($g1);
		echo "add user2(".$u2->first_name.") to group2(".$g2->name.")\n";
		$u2->addToGroup($g2);
		echo "-----------------\n";

		echo "\nadd video1(".$v1->identifier.") to group1\n";
		$v1->assignToGroup($g1);
		echo "add video2(".$v2->identifier.") to group1\n";
		$v2->assignToGroup($g1);
		echo "add video3(".$v3->identifier.") to group1\n";
		$v3->assignToGroup($g1);
		echo "add video4(".$v4->identifier.") to group2\n";
		$v4->assignToGroup($g2);
		echo "add video5(".$v5->identifier.") to group2\n";
		$v5->assignToGroup($g2);
		echo "add video6(".$v6->identifier.") to group2\n";
		$v6->assignToGroup($g2);
		
		echo "-----------------\n";
		echo "videos for group1:\n";
		foreach ($g1->videos as $v) {
			echo "\t- ".$v->identifier."\n";
		}
		echo "videos for group2:\n";
		foreach ($g2->videos as $v) {
			echo "\t- ".$v->identifier."\n";
		}
		echo "-----------------\n";

		echo "videos for user1:\n";
		foreach($u1->viewableVideos() as $v) {
			echo "\t- ".$v->identifier."\n";
		}
		echo "videos for user2:\n";
		foreach($u2->viewableVideos() as $v) {
			echo "\t- ".$v->identifier."\n";
		}
		
		echo "-----------------\n";
		echo "groups for video1 = ".$v1->groups->first()->name."\n";

	}*/
	
	/*public function testRelationships() {
		$u = factory(oval\User::class)->create();
		
		$g1 = factory(oval\Group::class)->create();
		$g2 = factory(oval\Group::class)->create();
		$c1 = $g1->course;
		$c2 = $g2->course;
		
		$v1 = factory(oval\Video::class)->create();
		$v2 = factory(oval\Video::class)->create();
		$v3 = factory(oval\Video::class)->create();
		$v4 = factory(oval\Video::class)->create();
		
		$u->enrollIn($c1);
		$u->enrollIn($c2);
		$g1->addMember($u);
		$g2->addMember($u);
		
		$v1->assignToGroup($g1);
		$v2->assignToGroup($g1);
		$v3->assignToGroup($g2);
		$g2->addVideo($v4);
		
		echo "\nGroup1 has videos->";
		foreach($g1->videos as $v) {
			echo "\n\t- ".$v->id.": ".$v->title;
		}
		echo "\nGroup2 has videos->";
		foreach($g2->videos as $v) {
			echo "\n\t- ".$v->id.": ".$v->title;
		}
		echo "\n\nUser can see videos->";
		foreach($u->viewableVideos() as $v) {
			echo "\n\t- ".$v->id.": ".$v->title;
		}

		echo "\n\nGet course with id=2->";
		echo oval\Course::find(2)->name;
		
// 		echo "\n\nGet the user's group for course 1->";
// 		echo $u->groupMemberOf->where('course_id', '1');

	}*/
	
	/*function testVideosAdded() {
		$minnie = oval\User::find(2);
    	$this->be($minnie);
    	dd($minnie->videosAdded);
	}*/
	
	public function testIsInstructor() {
		$minnie = oval\User::find(2);
		$this->be($minnie);
		echo "minnie is instructor of course(1) = ".$minnie->isInstructorOf(oval\Course::find(1));
	}	
	
	
}//end class
