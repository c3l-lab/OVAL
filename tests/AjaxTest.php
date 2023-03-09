<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AjaxTest extends TestCase
{

    
    private function loginAsMinnieMouse() {
    	$minnie = oval\User::find(2);
    	$this->be($minnie);
    }
    
    /*public function testGetAnnotations() {
    	//have to run "php artisan migrate:refresh --seed" before this test
    	$minnie = oval\User::find(2);
    	$this->be($minnie);
    	//echo "User is ".$minnie->fullName()."\n";

		$this -> json('GET', '/get_annotation_list', 
    				['video_id'=>'1', 'annotation_mode'=>'true', 'view_mode'=>'1'])
    		-> seeJson();
    }*/
    
    public function testGetComments() {
    	$this->loginAsMinnieMouse();
    	
    	$this -> json('GET', '/get_comments', ['video_id'=>'1'])
    			-> seeJson();
    }
    

}
