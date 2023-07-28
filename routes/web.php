<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use oval\Http\Controllers\GroupVideoController;
use oval\Http\Controllers\HomeController;
use oval\Http\Controllers\Lti\RegistrationController;
use oval\Http\Controllers\VideoController;
use oval\Http\Middleware\RequireAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/course/{course_id}', 'HomeController@course');
Route::get('/group/{group_id}', 'HomeController@group');

Route::middleware(['auth'])->group(function () {
    Route::get('/group_videos/{id}/embed', [GroupVideoController::class, 'embed'])
        ->name('group_videos.show.embed');
    Route::post('/group_videos/{id}/toggle_comments', [GroupVideoController::class, 'toggleComments'])
        ->name('group_videos.toggle_comments');
    Route::post('/group_videos/{id}/toggle_annotations', [GroupVideoController::class, 'toggleAnnotations'])
        ->name('group_videos.toggle_annotations');

    Route::resource('videos', VideoController::class);
    Route::resource('group_videos', GroupVideoController::class);
});

Route::get('/analytics/{course_id?}/{group_id?}', 'HomeController@analytics');

Route::get('/content-analysis/{course_id?}/{group_id?}', 'HomeController@content_analysis');

Route::get('/points-details/{group_video_id}', 'HomeController@points_details');

Route::get('/tracking-details/{group_video_id}', 'HomeController@tracking_details');

Route::get('/text-analysis-details/{video_id}', 'HomeController@text_analysis_details');

Route::get('/select-video/{link_id}/{group_video_id?}', 'HomeController@select_video');

Route::get('/manage-analysis-requests', 'HomeController@manage_analysis_requests');

Route::get('/batch-upload', 'HomeController@batch_upload');

Route::get('/manage-lti-connections', 'HomeController@manage_lti_connections');

// ----------- ajax routes ------------- //
Route::group(['middleware'=>'auth:api'], function () {
    Route::post('/get_annotations', 'AjaxController@get_annotations');
    Route::post('/get_comments', 'AjaxController@get_comments');
    Route::post('/add_comment', 'AjaxController@add_comment');
    Route::post('/add_annotation', 'AjaxController@add_annotation');
    Route::post('/edit_annotation', 'AjaxController@edit_annotation');
    Route::post('/edit_comment', 'AjaxController@edit_comment');
    Route::post('/delete_annotation', 'AjaxController@delete_annotation');
    Route::post('/delete_comment', 'AjaxController@delete_comment');
    Route::post('/get_groups', 'AjaxController@get_groups');
    Route::post('/save_video_group', 'AjaxController@assign_video_to_groups');
    Route::post('/download_annotations', 'AjaxController@download_annotations');
    Route::post('/save_feedback', 'AjaxController@save_feedback');
    Route::post('/get_group_info_for_video', 'AjaxController@get_group_info_for_video');
    Route::post('/save_confidence_level', 'AjaxController@save_confidence_level');
    Route::post('/get_videos_for_course', 'AjaxController@get_videos_for_course');
    Route::post('/get_groups_for_video', 'AjaxController@get_groups_for_video');
    Route::post('/check_if_course_wide_points', 'AjaxController@check_if_course_wide_points');
    Route::post('/save_points', 'AjaxController@save_points');
    Route::post('/get_points_for_group_video', 'AjaxController@get_points_for_group_video');
    Route::post('/delete_points', 'AjaxController@delete_points');
    Route::post('/add_trackings', 'AjaxController@add_trackings');
    Route::post('/add_analysis_request', 'AjaxController@add_analysis_request');
    Route::post('/get_nominated_students_ids', 'AjaxController@get_nominated_students_ids');
    Route::post('/edit_comment_instruction', 'AjaxController@edit_comment_instruction');
    Route::post('/delete_comment_instruction', 'AjaxController@delete_comment_instruction');
    Route::post('/get_comments_for_tag', 'AjaxController@get_comments_for_tag');
    Route::post('/get_annotations_for_tag', 'AjaxController@get_annotations_for_tag');
    Route::post('/edit_visibility', 'AjaxController@edit_visibility');
    Route::post('/edit_video_order', 'AjaxController@edit_video_order');
    Route::post('/edit_text_analysis_visibility', 'AjaxController@edit_text_analysis_visibility');
    Route::post('/set_lti_resource_link', 'AjaxController@set_lti_resource_link');
    Route::post('/check_student_activity', 'AjaxController@check_student_activity');
    Route::post('/archive_group_video', 'AjaxController@archive_group_video');
    Route::post('/delete_keywords', 'AjaxController@delete_keywords');
    Route::post('/get_groups_with_video', 'AjaxController@get_groups_with_video');
    Route::post('/get_video_info', 'AjaxController@get_video_info');
    Route::post('/delete_lti_connection', 'AjaxController@delete_lti_connection');
    Route::post('/get_lti_connection_detail', 'AjaxController@get_lti_connection_detail');
    Route::post('/edit_lti_connection', 'AjaxController@edit_lti_connection');

    /*------ quiz API ------*/
    Route::post('/store_quiz', 'AjaxController@store_quiz');
    Route::post('/submit_quiz_result', 'AjaxController@submit_ans');
    /*------ quiz API end ------*/

});
// ----------- form processing -----------
Route::post('/upload_transcript', 'FileController@upload_transcript');
Route::post('/request_text_analysis', 'ProcessController@request_text_analysis');
Route::post('/reject_text_analysis_request', 'ProcessController@reject_text_analysis_request');
Route::post('/recover_text_analysis_request', 'ProcessController@recover_text_analysis_request');
Route::post('/delete_text_analysis_request', 'ProcessController@delete_text_analysis_request');
Route::post('/send_all_text_analysis_requests', 'ProcessController@send_all_text_analysis_requests');
Route::post('/reject_all_text_analysis_requests', 'ProcessController@reject_all_text_analysis_requests');
Route::post('/recover_all_rejected_text_analysis_requests', 'ProcessController@recover_all_rejected_text_analysis_requests');
Route::post('/delete_all_rejected_text_analysis_requests', 'ProcessController@delete_all_rejected_text_analysis_requests');
Route::post('/batch_data_insert', 'ProcessController@batch_data_insert');
Route::post('/add_lti_connection', 'ProcessController@add_lti_connection');

// ----------- lti routes ------------- //
Route::prefix('lti')->group(function () {
    Route::middleware([RequireAdmin::class])->group(function () {
        Route::resources([
            'registrations' => RegistrationController::class,
        ]);
    });
});

// ----------- youtube data api ------------- //
Route::post('/add_google_cred', 'GoogleAPIController@add_google_cred');
Route::get('/youtube_auth_redirect', 'GoogleAPIController@youtube_auth_redirect');
Route::post('/check_youtube_caption', 'GoogleAPIController@check_youtube_caption');

/*------ quiz api ------*/
Route::get('/get_quiz', 'AjaxController@get_quiz');

/*------ analysis api ------*/
Route::get('/get_student_view', 'AjaxController@get_student_view');
Route::get('/get_annotations_column', 'AjaxController@get_annotations_column');
Route::get('/get_comment_column', 'AjaxController@get_comment_column');
Route::get('/get_quiz_question', 'AjaxController@get_quiz_question');
Route::get('/get_key_point', 'AjaxController@get_key_point');
Route::get('/change_quiz_visable', 'AjaxController@change_quiz_visable');
Route::get('/get_quiz_visable_status', 'AjaxController@get_quiz_visable_status');
Route::get('/get_all_student_record', 'AjaxController@get_all_student_record');
