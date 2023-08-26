<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use oval\Http\Controllers\AnalysisRequestController;
use oval\Http\Controllers\AnalyticsController;
use oval\Http\Controllers\AnnotationController;
use oval\Http\Controllers\CommentController;
use oval\Http\Controllers\CommentInstructionController;
use oval\Http\Controllers\GroupController;
use oval\Http\Controllers\GroupVideoController;
use oval\Http\Controllers\HomeController;
use oval\Http\Controllers\QuizResultController;
use oval\Http\Controllers\TrackingController;
use oval\Http\Controllers\TranscriptController;
use oval\Http\Controllers\Video;
use oval\Http\Controllers\GroupVideo;
use oval\Http\Controllers\Course;
use oval\Http\Controllers\Lti\ConsumerController;
use oval\Http\Controllers\Lti\RegistrationController;
use oval\Http\Controllers\VideoController;
use oval\Http\Middleware\RequireAdmin;
use oval\Http\Middleware\RequireInstructor;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::post('/videos/{video}/assign', [VideoController::class, 'assign'])->name('videos.assign');
    Route::resource('videos', VideoController::class);

    Route::get('/group_videos/{id}/embed', [GroupVideoController::class, 'embed'])->name('group_videos.show.embed');

    Route::get('/comments/tag', [CommentController::class, 'tag'])->name('comments.tag');
    Route::get('/comments/report', [CommentController::class, 'report'])->name('comments.report');
    Route::get('/comments/detail', [CommentController::class, 'detail'])->name('comments.detail');
    Route::resource('comments', CommentController::class);
    Route::resource('comment_instructions', CommentInstructionController::class);

    Route::get('/groups/unassigned', [GroupController::class, 'unassigned'])->name('comments.unassigned');
    Route::resource('groups', GroupController::class);

    Route::get('/videos/{video}/groups/with_contents', [Video\GroupController::class, 'withContents'])->name('videos.groups.with_contents');
    Route::resource('videos.groups', Video\GroupController::class);

    Route::get('/annotations/download', [AnnotationController::class, 'download'])->name('annotations.download');
    Route::get('/annotations/tag', [AnnotationController::class, 'tag'])->name('annotations.tag');
    Route::get('/annotations/report', [AnnotationController::class, 'report'])->name('annotations.report');
    Route::get('/annotations/detail', [AnnotationController::class, 'detail'])->name('annotations.detail');
    Route::resource('annotations', AnnotationController::class);

    Route::resource('courses.videos', Course\VideoController::class);

    Route::get('/quiz_results/detail', [QuizResultController::class, 'detail'])->name('quiz_results.detail');
    Route::resource('quiz_results', QuizResultController::class);
    Route::singleton('group_videos.quiz', GroupVideo\QuizController::class);

    Route::get('trackings/export', [TrackingController::class, 'export'])->name('trackings.export');
    Route::resource('trackings', TrackingController::class);

    Route::middleware([RequireAdmin::class])->group(function () {
        Route::post('/analysis_requests/batch_resend', [AnalysisRequestController::class, 'batch_resend'])->name('analysis_requests.batch_resend');
        Route::post('/analysis_requests/batch_reject', [AnalysisRequestController::class, 'batch_reject'])->name('analysis_requests.batch_reject');
        Route::post('/analysis_requests/batch_recover', [AnalysisRequestController::class, 'batch_recover'])->name('analysis_requests.batch_recover');
        Route::post('/analysis_requests/batch_delete', [AnalysisRequestController::class, 'batch_delete'])->name('analysis_requests.batch_delete');
        Route::post('/analysis_requests/{analysis_request}/resend', [AnalysisRequestController::class, 'resend'])->name('analysis_requests.resend');
        Route::post('/analysis_requests/{analysis_request}/reject', [AnalysisRequestController::class, 'reject'])->name('analysis_requests.reject');
        Route::post('/analysis_requests/{analysis_request}/recover', [AnalysisRequestController::class, 'recover'])->name('analysis_requests.recover');
        Route::resource('analysis_requests', AnalysisRequestController::class);

        Route::post('/transcripts/upload', [TranscriptController::class, 'upload'])->name('transcripts.upload');
        Route::resource('transcripts', TranscriptController::class);
    });

    Route::middleware([RequireInstructor::class])->group(function () {
        Route::post('/group_videos/{groupVideo}/toggle_visibility', [GroupVideoController::class, 'toggleVisibility'])->name('group_videos.toggle_visibility');
        Route::post('/group_videos/{groupVideo}/toggle_comments', [GroupVideoController::class, 'toggleComments'])->name('group_videos.toggle_comments');
        Route::post('/group_videos/{groupVideo}/toggle_annotations', [GroupVideoController::class, 'toggleAnnotations'])->name('group_videos.toggle_annotations');
        Route::post('/group_videos/{groupVideo}/toggle_analysis', [GroupVideoController::class, 'toggleAnalysis'])->name('group_videos.toggle_analysis');
        Route::post('/group_videos/{groupVideo}/archive', [GroupVideoController::class, 'archive'])->name('group_videos.archive');
        Route::get('/group_videos/by_course', [GroupVideoController::class, 'byCourse'])->name('group_videos.by_course');
        Route::get('/group_videos/by_group', [GroupVideoController::class, 'byGroup'])->name('group_videos.by_group');
        Route::post('/group_videos/sort', [GroupVideoController::class, 'sort'])->name('group_videos.sort');
        Route::resource('group_videos', GroupVideoController::class);

        Route::singleton('group_videos.controls', GroupVideo\ControlsController::class);

        Route::get('/group_videos/{groupVideo}/quiz/result', [GroupVideo\QuizController::class, 'result'])->name('group_videos.quiz.result');
        Route::post('/group_videos/{groupVideo}/quiz/toggle_visible', [GroupVideo\QuizController::class, 'toggleVisible'])->name('group_videos.quiz.toggle_visible');

        Route::resource('analytics', AnalyticsController::class);
    });

    Route::prefix('lti')->group(function () {
        Route::middleware([RequireAdmin::class])->group(function () {
            Route::resources([
                'registrations' => RegistrationController::class,
                'consumers' => ConsumerController::class,
            ]);
        });
    });
});

// ----------- ajax routes ------------- //
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/save_feedback', 'AjaxController@save_feedback');
    Route::post('/save_confidence_level', 'AjaxController@save_confidence_level');
    Route::post('/check_if_course_wide_points', 'AjaxController@check_if_course_wide_points');
    Route::post('/save_points', 'AjaxController@save_points');
    Route::post('/get_points_for_group_video', 'AjaxController@get_points_for_group_video');
    Route::post('/delete_points', 'AjaxController@delete_points');
    Route::post('/get_nominated_students_ids', 'AjaxController@get_nominated_students_ids');
    Route::post('/check_student_activity', 'AjaxController@check_student_activity');
    Route::post('/delete_keywords', 'AjaxController@delete_keywords');
});

// ----------- youtube data api ------------- //
Route::post('/add_google_cred', 'GoogleAPIController@add_google_cred');
Route::get('/youtube_auth_redirect', 'GoogleAPIController@youtube_auth_redirect');
Route::post('/check_youtube_caption', 'GoogleAPIController@check_youtube_caption');

/*------ analysis api ------*/
Route::get('/get_student_view', 'AjaxController@get_student_view');
Route::get('/get_key_point', 'AjaxController@get_key_point');
