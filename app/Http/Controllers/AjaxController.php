<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use oval;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use oval\Classes\YoutubeDataHelper;
use oval\Models\Video;

/**
 * Controller class to handle Ajax requests
 *
 */
class AjaxController extends Controller
{
    public function __construct()
    {
        //         $this->middleware('auth');
    }

    /**
     * Method called from route /save_video_group
     *
     * This method associates video to groups.
     *
     * @param Request $req Request contains course_id(int), group_ids(array of int), video_id(int)
     * @return void
     */
    public function assign_video_to_groups(Request $req)
    {
        $course_id = intval($req->course_id);
        $group_ids = $req->group_ids;
        $video_id = intval($req->video_id);

        $copy_from_group_id = intval($req->copy_from);
        $copy_comment_instruction = $req->copy_comment_instruction;
        $copy_points = $req->copy_points;
        $copy_quiz = $req->copy_quiz;

        $video = oval\Models\Video::find($video_id);
        $copy_origin = $copy_from_group_id == -1 ? null : oval\Models\GroupVideo::where([['group_id', '=', $copy_from_group_id], ['video_id', '=', $video_id]])->first();

        if (count($group_ids) > 0) {
            foreach($group_ids as $gid) {
                $group = oval\Models\Group::find($gid);
                $video->assignToGroup($group);
            }
        }
        if(!empty($copy_origin)) {
            foreach($group_ids as $gid) {
                $gv = oval\Models\GroupVideo::where([
                        ['group_id', '=', $gid],
                        ['video_id', '=', $video_id]
                    ])
                    ->first();
                if($copy_comment_instruction == "true") {
                    $comment_instruction = oval\Models\CommentInstruction::where('group_video_id', '=', $gv->id)->first();
                    if (empty($comment_instruction)) {
                        $comment_instruction = new oval\Models\CommentInstruction();
                        $comment_instruction->group_video_id = $gv->id;
                    }
                    $comment_instruction->description = oval\Models\CommentInstruction::where('group_video_id', '=', $copy_origin->id)->first()->description;
                    $comment_instruction->save();
                }

                if($copy_points == "true") {
                    $points = oval\Models\Point::where('group_video_id', '=', $gv->id)->get();
                    if($points->count() > 0) {
                        //delete them
                    }
                    $copy_points = oval\Models\Point::where('group_video_id', '=', $copy_origin->id)->get();
                    foreach ($copy_points as $cp) {
                        $p = new oval\Models\Point();
                        $p->group_video_id = $gv->id;
                        $p->description = $cp->description;
                        $p->save();
                    }
                }

                if ($copy_quiz == "true") {
                    //todo: implement after editing quiz
                }
            }
        }
    }//end function

    /**
     * Method called from route /save_feedback
     *
     * This method receives comment_id, confidence_level and answers(array) as parameters,
     * and saves the confidence level and answers for comment.
     *
     * @param Request $req Request contains comment_id, confidence_level, answers(array with keys [point_id, answer])
     * @return void
     */
    public function save_feedback(Request $req)
    {
        $comment_id = intval($req->comment_id);
        $level = intval($req->confidence_level);
        $answers = $req->answers;
        foreach ($answers as $a) {
            $feedback = new oval\Models\Feedback();
            $feedback->comment_id = $comment_id;
            $feedback->point_id= $a['point_id'];
            $feedback->answer = $a['answer'];
            $feedback->save();
        }
        $confidence_level = new oval\Models\ConfidenceLevel();
        $confidence_level->comment_id = $comment_id;
        $confidence_level->level = $level;
        $confidence_level->save();
    }

    /**
     * Method called from route /get_videos_for_course
     *
     * This method returns videos which are assigned
     * for the course whose id is passed in as parameter
     *
     * @param Request $req Request contains course_id
     * @return array array with key [videos] whose value contains collection of Video objects
     */
    public function get_videos_for_course(Request $req)
    {
        $course = oval\Models\Course::find(intval($req->course_id));
        $videos = $course->videos();
        return compact('videos');
    }

    /**
     * Method called from /check_if_course_wide_points
     *
     * This method is used to check if the video for this course has course wide points.
     *
     * @param Request $req Request contains course_id, video_id
     * @return array Array with key is_course_wide. The value is true if it is course wide, false if not.
     */
    public function check_if_course_wide_points(Request $req)
    {
        $course = oval\Models\Course::find(intval($req->course_id));
        $video = oval\Models\Video::find(intval($req->video_id));
        $is_course_wide = false;
        $default_group = $course->defaultGroup();
        $group_video = oval\Models\GroupVideo::where([
                            ['group_id', '=', $default_group->id],
                            ['video_id', '=', $video->id]
                        ])
                        ->first();
        if (!empty($group_video->points)) {
            $point1 = $group_video->points->first();
            if (!empty($point1)) {
                if ($point1->is_course_wide) {
                    $is_course_wide = true;
                }
            }
        }
        return compact('is_course_wide');
    }


    /**
     * Method called from route /add_trackings
     *
     * This method saves trackings passed in as parameter
     * @author Harry
     *
     * @param Request $req Request contains
     * 								group_video_id,
     * 								data (array of array with keys [event, target, info, event_time])
     * @return void
     */
    public function add_trackings(Request $req)
    {
        $records = $req->data;
        foreach ($records as $record) {
            $tracking = new oval\Models\Tracking();
            $tracking->group_video_id = intval($req->group_video_id);
            $tracking->user_id = Auth::user()->id;
            $tracking->event = $record['event'];
            $tracking->target = $record['target'];
            $tracking->info = $record['info'];
            $tracking->event_time = date("Y-m-d H:i:s", (int)($record['event_time'] / 1000));
            $result = $tracking->save();
        }
    }

    /**
     * Method called from route /get_nominated_students_ids
     *
     * This method returns students to make the annotation/comment available for.
     *
     * @param Request $req Request contains
     * 								item (string "comment" or "annotation"),
     * 								item_id
     * @return array Array with key "nominated" with value containing array of User objects
     */
    public function get_nominated_students_ids(Request $req)
    {
        $item = $req->item; //"comment" or "annotation"
        $item_id = intval($req->item_id);
        $nominated = [];
        if ($item == "annotation") {
            $annotation = oval\Models\Annotation::find($item_id);
            $nominated = json_decode($annotation->visible_to);
        } elseif ($item == "comment") {
            $comment = oval\Models\Comment::find($item_id);
            $nominated = json_decode($comment->visible_to);
        }
        return compact('nominated');
    }
    /*------ end quiz ajax function ------*/

    /*------ analysis ajax function ------*/

    public function get_student_view(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ The portion/percentage of video watched, first & last time played ------*/
            $latest_end_record = DB::table('trackings')
                                 ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                                 ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                                 ->select('trackings.*', 'videos.duration')
                                 ->where([
                                    ['group_video_id', '=', $group_video->id],
                                    ['user_id', '=', $user->id],
                                    ['event', '=', 'Ended']
                                 ])
                                 ->orderBy('event_time', 'desc')
                                 ->first();

            if(empty($latest_end_record)) {

                /*------ user did not finish video, calculate portion ------*/
                $latest_portion_record = DB::table('trackings')
                                         ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                                         ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                                         ->select('trackings.user_id', 'trackings.event', 'trackings.info', 'videos.duration')
                                         ->where([
                                            ['group_video_id', '=', $group_video->id],
                                            ['user_id', '=', $user->id],
                                            ['event', '=', 'Paused']
                                         ])
                                         ->orderBy('event_time', 'desc')
                                         ->first();

                if(!empty($latest_portion_record)) {
                    $portion = (float)($latest_portion_record->info)/(float)($latest_portion_record->duration);
                } else {
                    $portion = 0;
                }

            } else {
                $portion = 1;
            }

            $portion = 	number_format($portion, 4);

            $play_record = DB::table('trackings')
                          ->select('user_id', 'event', 'event_time')
                          ->where([
                             ['group_video_id', '=', $group_video->id],
                             ['user_id', '=', $user->id],
                             ['event', '=', 'Play']
                          ])
                          ->orderBy('event_time', 'desc')
                          ->get();

            if(count($play_record) > 0) {
                $last_play  = $play_record[0]->event_time;
                $first_play = $play_record[count($play_record)-1]->event_time;
            } else {
                $first_play = 'Never played';
                $last_play = 'Never played';
            }

            // /*------ general comments viewed ---------*/
            $comment_view = DB::table('trackings')
                            ->select('user_id', 'event', 'event_time')
                            ->where([
                                ['group_video_id', '=', $group_video->id],
                                ['user_id', '=', $user->id],
                                ['event', '=', 'View']
                            ])
                            ->count();
            /*------ annotations viewed ------*/
            $annotations_view = DB::table('trackings')
                                ->select('user_id', 'event', 'info', 'event_time')
                                ->where([
                                    ['group_video_id', '=', $group_video->id],
                                    ['user_id', '=', $user->id],
                                    ['event', '=', 'click'],
                                    ['info', '=', 'View an annotation']
                                ])
                                ->orderBy('event_time', 'desc')
                                ->get();

            $annotations_close = DB::table('trackings')
                                 ->select('user_id', 'event', 'info', 'event_time')
                                 ->where([
                                    ['group_video_id', '=', $group_video->id],
                                    ['user_id', '=', $user->id],
                                    ['event', '=', 'click'],
                                    ['info', '=', 'Close annotation preview']
                                 ])
                                 ->orderBy('event_time', 'desc')
                                 ->get();
            $total = 0;
            $annotations_num = count($annotations_close);

            for ($i = 0; $i < $annotations_num; $i++) {
                $total += (strtotime($annotations_view[$i]->event_time) - strtotime($annotations_close[$i]->event_time));
            }

            if($annotations_num > 0) {
                $annotations_average_time = $total/$annotations_num;
            } else {
                $annotations_average_time = 0;
            }


            // /*------ if annotations download ------*/
            $annotations_download = DB::table('trackings')
                                    ->select('user_id', 'event', 'info', 'event_time')
                                    ->where([
                                        ['group_video_id', '=', $group_video->id],
                                        ['user_id', '=', $user->id],
                                        ['event', '=', 'click'],
                                        ['info', '=', 'Download Annotations']
                                    ])
                                    ->first();

            if(count($annotations_download) > 0) {
                $annotations_download_status = "Downloaded";
            } else {
                $annotations_download_status = "Never download";
            }

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'portion', 'first_play', 'last_play', 'comment_view', 'annotations_num', 'annotations_average_time', 'annotations_download_status'));

        }

        return $result_arr;

    }

    public function get_key_point(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ get key info ------*/
            $key_info = DB::table('feedbacks')
                        ->join('points', 'feedbacks.point_id', '=', 'points.id')
                        ->join('comments', 'feedbacks.comment_id', '=', 'comments.id')
                        ->join('confidence_levels', 'feedbacks.comment_id', '=', 'confidence_levels.comment_id')
                        ->select('feedbacks.comment_id', 'comments.description as comments_description', 'points.description as points_description', 'status', 'confidence_levels.level')
                        ->where([
                            ['comments.user_id', '=', $user->id],
                            ['comments.group_video_id', '=', $group_video->id],
                            ['comments.status', '=', 'current']
                        ])
                        ->get();
            if ($key_info->count() >0) {
                array_push($result_arr, compact('surname', 'first_name', 'student_id', 'key_info'));
            }
        }

        return $result_arr;
    }

    public function get_quiz_question(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ get video finish duration ------*/

            /*------ The portion/percentage of video watched, first & last time played ------*/
            $latest_end_record = DB::table('trackings')
                                    ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                                    ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                                    ->select('trackings.*', 'videos.duration')
                                    ->where([
                                        ['group_video_id', '=', intval($req->group_video_id)],
                                        ['user_id', '=', $user->id],
                                        ['event', '=', 'Ended']
                                    ])
                                    ->orderBy('event_time', 'desc')
                                    ->first();

            if(empty($latest_end_record)) {

                /*------ user did not finish video, calculate portion ------*/
                $latest_portion_record = DB::table('trackings')
                                        ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                                        ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                                        ->select('trackings.user_id', 'trackings.event', 'trackings.info', 'videos.duration')
                                        ->where([
                                            ['group_video_id', '=', intval($req->group_video_id)],
                                            ['user_id', '=', $user->id],
                                            ['event', '=', 'Paused']
                                        ])
                                        ->orderBy('event_time', 'desc')
                                        ->first();

                if(!empty($latest_portion_record)) {
                    $portion = (float)($latest_portion_record->info)/(float)($latest_portion_record->duration);
                } else {
                    $portion = 0;
                }

            } else {
                $portion = 1;
            }

            $portion = 	number_format($portion, 4);

            /*------ get quiz result ------*/
            $quiz_result = DB::table('quiz_result')
                           ->join('videos', 'videos.identifier', '=', 'quiz_result.identifier')
                           ->join('group_videos', 'group_videos.video_id', '=', 'videos.id')
                           ->select('quiz_data')
                           ->where([
                               ['user_id', '=', $user->id],
                               ['group_videos.id', '=', intval($req->group_video_id)]
                           ])
                           ->get();

            $score = 0;
            $total_answer_num = 0;

            for($i = 0; $i < count($quiz_result); $i++) {
                $obj = json_decode($quiz_result[$i]->quiz_data);

                for($j = 0; $j < count($obj->items); $j++) {
                    if(strcmp($obj->items[$j]->type, 'multiple_choice') == 0) {

                        $is_equal = strcmp((string)($obj->items[$j]->ans[0]), (string)($obj->items[$j]->user_ans));

                        if($is_equal == 0) {
                            $score++;
                        }

                    }


                }

                $total_answer_num +=  count(json_decode($quiz_result[$i]->quiz_data)->items);
            }

            if($total_answer_num > 0) {
                $score_ratio = $score/$total_answer_num;
            } else {
                $score_ratio = 0;
            }

            $score_ratio = number_format($score_ratio, 4);

            /*------ get quiz attempt number ------*/
            $answer_attempt = array();

            for($i = 0; $i < count($quiz_result); $i++) {
                $obj = json_decode($quiz_result[$i]->quiz_data);

                if(count($answer_attempt) > 0) {

                    $trigger = 1;

                    for($j = 0; $j < count($answer_attempt); $j++) {
                        if(strcmp($answer_attempt[$j]['name'], $obj->name) == 0) {
                            $answer_attempt[$j]['counter'] += 1;
                            $trigger = 0;
                        }
                    }

                    if($trigger == 1) {
                        $temp = array('name'=>$obj->name, 'counter'=>1);
                        array_push($answer_attempt, $temp);
                    }

                } else {
                    $temp = array('name'=>$obj->name, 'counter'=>1);
                    array_push($answer_attempt, $temp);
                }


            }

            /*------ get quiz list ------*/
            $quiz_list = DB::table('quiz_creation')
                        ->join('videos', 'videos.identifier', '=', 'quiz_creation.identifier')
                        ->join('group_videos', 'group_videos.video_id', '=', 'videos.id')
                        ->select('quiz_data')
                        ->where([
                            ['group_videos.id', '=', intval($req->group_video_id)]
                        ])
                        ->orderBy('quiz_creation.created_at', 'desc')
                        ->first();

            $quiz_name_list = array();
            if ($quiz_list) {
                $list = json_decode($quiz_list->quiz_data);

                for($i = 0; $i < count($list); $i++) {
                    $temp = array('name'=>$list[$i]->name);
                    array_push($quiz_name_list, $temp);
                }
            }

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'score_ratio', 'portion', 'answer_attempt', 'quiz_name_list'));
        }


        return $result_arr;
    }

    public function get_quiz_visable_status(Request $req)
    {

        $videoid_arr = explode(',', $req->videoid);

        $result_arr = [];

        for ($x = 0; $x < count($videoid_arr); $x++) {

            /*------ get quiz list ------*/
            $quiz_list = DB::table('quiz_creation')
                ->join('videos', 'quiz_creation.identifier', '=', 'videos.identifier')
                ->select('videos.id as video_id', 'quiz_creation.identifier as identifier', 'quiz_creation.visable')
                ->where([
                    ['videos.id', '=', $videoid_arr[$x]]
                ])
                ->first();

            if ($quiz_list !== null && count((array)$quiz_list) > 0) {
                array_push($result_arr, $quiz_list);
            } else {
                array_push($result_arr, "no quiz");
            }

        }



        return $result_arr;
    }

    public function get_all_student_record(Request $req)
    {

        $user_arr = explode(',', $req->user_id);

        $result_arr = [];

        for($x = 0; $x < count($user_arr); $x++) {

            /*------ get user surname, first name, student ID ------*/
            $user_info = DB::table('users')
            ->select('first_name', 'last_name', 'email')
            ->where([
                ['id', '=', $user_arr[$x]]
            ])
            ->first();

            $surname = $user_info->first_name;
            $first_name = $user_info->last_name;
            $student_id = $user_info->email;

            /*------ get all attempt record ------*/
            $student_record_list = DB::table('quiz_result')
                        ->join('videos', 'videos.identifier', '=', 'quiz_result.identifier')
                        ->join('group_videos', 'group_videos.video_id', '=', 'videos.id')
                        ->select('quiz_result.quiz_data', 'quiz_result.created_at')
                        ->where([
                            ['group_videos.id', '=', $req->group_video_id],
                            ['quiz_result.user_id', '=', $user_arr[$x]]
                        ])
                        ->orderBy('quiz_result.created_at', 'desc')
                        ->get();

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'student_record_list', 'student_record_list'));

        }

        return $result_arr;

    }

    /*------ end analysis ajax funciton ------*/


    /**
     * Method called from route /edit_visibility
     *
     * This method saves visibility setting for the GroupVideo.
     * If the GroupVideo is set to not visible, instructors can still see the page
     * with message letting them know it is not visible for students.
     *
     * @param Request $req Contains group_video_id, visibility
     * @return void
     */
    public function edit_visibility(Request $req)
    {
        $group_video_id = intval($req->group_video_id);
        $vis = intval($req->visibility);

        $group_video = oval\Models\GroupVideo::find($group_video_id);
        $group_video->hide = $vis;
        $group_video->save();
    }

    /**
     * Method called from route /edit_video_order
     *
     * This method sets the value for "order" of GroupVideo
     *
     * @param Request $req Contains group_video_ids - array of ids in the order to display.
     */
    public function edit_video_order(Request $req)
    {
        $group_video_ids = $req->group_video_ids;
        $i = 1;
        foreach ($group_video_ids as $gv_id) {
            $group_video = oval\Models\GroupVideo::find($gv_id);
            $group_video->order = $i;
            $group_video->save();
            $i++;
        }
    }

    /**
     * Method called from route /edit_text_analysis_visibility
     *
     * This method saves the visibility of content analysis.
     * When GroupVideo's show_analysis is set to false, it is not displayed.
     *
     * @param Request $req Contains group_video_id, visibility
     * @return void
     */
    public function edit_text_analysis_visibility(Request $req)
    {
        $group_video_id = intval($req->group_video_id);
        $show = intval($req->visibility);

        $group_video = oval\Models\GroupVideo::find($group_video_id);
        $group_video->show_analysis = $show;
        $group_video->save();
    }

    /**
     * Method called from route /check_student_activity
     *
     * This method finds if there is any student activity associated with the GroupVideo whose id passed in.
     * TODO:: Check if there are quiz answers
     *
     * @param Request $req Contains group_video_id
     * @return array Array with keys [group_video_id, has_activity] - has_activity's value is boolean
     */
    public function check_student_activity(Request $req)
    {
        $group_video_id = intval($req->group_video_id);
        $group_video = oval\Models\GroupVideo::find($group_video_id);

        $has_quiz_answers = false;//todo: implement this

        $has_activity = false;
        if (count($group_video->annotations)>0 || count($group_video->comments)>0 || $has_quiz_answers==true) {
            $has_activity = true;
        }
        return compact('group_video_id', 'has_activity');
    }

    /**
     * Method called from route /archive_group_video
     *
     * This method marks GroupVideo as "archived"
     *
     * @param Request $req Contains group_video_id
     * @return array Array with key "result", value is true if saved successfully, false if not.
     */
    public function archive_group_video(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $group_video->status = "archived";
        $result = $group_video->save();
        return compact('result');
    }

    public function delete_keywords(Request $req)
    {
        $words = $req->words;
        $video_id = intval($req->video_id);
        $deletes = oval\Models\Keyword::whereIn('keyword', $words)
                            ->where('videoId', '=', $video_id)
                            ->pluck('id')
                            ->all();
        oval\Models\Keyword::destroy($deletes);

        return $deletes;
    }

    /**
     * Method called from route /get_groups_with_video
     *
     * This method receives video_id and returns groups that has access to this video,
     * along with whether the groups have contents that can be copied (comment instruction, points, quiz)
     *
     * @param Request $req Contains video_id
     * @return collection Collection of Group objects
     */
    public function get_groups_with_video(Request $req)
    {
        $video_id = intval($req->video_id);
        $the_groups = oval\Models\Group::whereIn("id", function ($q) use ($video_id) {
            $q->select('group_id')
                ->from('group_videos')
                ->where('video_id', '=', $video_id)
                ->get();
        })
                        ->get();
        $groups = collect();

        foreach($the_groups as $g) {
            $group_video = oval\Models\GroupVideo::where([
                                ['video_id', '=', $video_id],
                                ['group_id', '=', $g->id]
                            ])
                            ->first();
            $points = $group_video->relatedPoints();
            $quiz = oval\Models\QuizCreation::where('identifier', '=', oval\Models\Video::find($video_id)->identifier)
                        ->first();

            $group = [
                        "course_id"=>$g->course->id,
                        "course_name"=>$g->course->name,
                        "id"=>$g->id,
                        "name"=>$g->name,
                        "has_comment_instruction"=>empty($comment_instruction) ? false : true,
                        "has_points"=>$points->count()>0 ? true : false,
                        "has_quiz"=>empty($quiz) ? false : true,
                        "group_video_id"=>$group_video->id,
                        "course"=>$group_video->course()->name,
                        "def_group"=>$group_video->course()->defaultGroup()->name,
                        "def_group_comment_inst"=>$group_video->course()->defaultGroup()->comment_instruction
            ];
            $groups->push($group);
        }


        $groups = $groups->groupBy('course_id');
        return $groups;
    }

    /**
     * Method called from route /get_video_info
     *
     * Takes video_id as parameter and returns thumbnail url and title of the video
     * @param Request $req Contains video_id
     * @return array Array containing thumbnail_url and title
     */
    public function get_video_info(Request $req)
    {
        $video_id = intval($req->video_id);
        $video = oval\Models\Video::find($video_id);
        $thumbnail_url = $video->thumbnail_url;
        $title = $video->title;
        return compact('thumbnail_url', 'title');
    }
}//end class
