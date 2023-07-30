<?php

namespace oval\Http\Controllers\GroupVideo;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\GroupVideo;

class QuizController extends Controller
{
    public function result(Request $request, GroupVideo $groupVideo)
    {
        $users = $groupVideo->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ get video finish duration ------*/

            /*------ The portion/percentage of video watched, first & last time played ------*/
            $latest_end_record = \DB::table('trackings')
                ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                ->select('trackings.*', 'videos.duration')
                ->where([
                    ['group_video_id', '=', intval($request->group_video_id)],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'Ended']
                ])
                ->orderBy('event_time', 'desc')
                ->first();

            if (empty($latest_end_record)) {

                /*------ user did not finish video, calculate portion ------*/
                $latest_portion_record = \DB::table('trackings')
                    ->join('group_videos', 'group_videos.id', '=', 'trackings.group_video_id')
                    ->join('videos', 'videos.id', '=', 'group_videos.video_id')
                    ->select('trackings.user_id', 'trackings.event', 'trackings.info', 'videos.duration')
                    ->where([
                        ['group_video_id', '=', intval($request->group_video_id)],
                        ['user_id', '=', $user->id],
                        ['event', '=', 'Paused']
                    ])
                    ->orderBy('event_time', 'desc')
                    ->first();

                if (!empty($latest_portion_record)) {
                    $portion = (float) ($latest_portion_record->info) / (float) ($latest_portion_record->duration);
                } else {
                    $portion = 0;
                }

            } else {
                $portion = 1;
            }

            $portion = number_format($portion, 4);

            /*------ get quiz result ------*/
            $quiz_result = \DB::table('quiz_result')
                ->join('videos', 'videos.identifier', '=', 'quiz_result.identifier')
                ->join('group_videos', 'group_videos.video_id', '=', 'videos.id')
                ->select('quiz_data')
                ->where([
                    ['user_id', '=', $user->id],
                    ['group_videos.id', '=', intval($request->group_video_id)]
                ])
                ->get();

            $score = 0;
            $total_answer_num = 0;

            for ($i = 0; $i < count($quiz_result); $i++) {
                $obj = json_decode($quiz_result[$i]->quiz_data);

                for ($j = 0; $j < count($obj->items); $j++) {
                    if (strcmp($obj->items[$j]->type, 'multiple_choice') == 0) {

                        $is_equal = strcmp((string) ($obj->items[$j]->ans[0]), (string) ($obj->items[$j]->user_ans));

                        if ($is_equal == 0) {
                            $score++;
                        }

                    }


                }

                $total_answer_num += count(json_decode($quiz_result[$i]->quiz_data)->items);
            }

            if ($total_answer_num > 0) {
                $score_ratio = $score / $total_answer_num;
            } else {
                $score_ratio = 0;
            }

            $score_ratio = number_format($score_ratio, 4);

            /*------ get quiz attempt number ------*/
            $answer_attempt = array();

            for ($i = 0; $i < count($quiz_result); $i++) {
                $obj = json_decode($quiz_result[$i]->quiz_data);

                if (count($answer_attempt) > 0) {

                    $trigger = 1;

                    for ($j = 0; $j < count($answer_attempt); $j++) {
                        if (strcmp($answer_attempt[$j]['name'], $obj->name) == 0) {
                            $answer_attempt[$j]['counter'] += 1;
                            $trigger = 0;
                        }
                    }

                    if ($trigger == 1) {
                        $temp = array('name' => $obj->name, 'counter' => 1);
                        array_push($answer_attempt, $temp);
                    }

                } else {
                    $temp = array('name' => $obj->name, 'counter' => 1);
                    array_push($answer_attempt, $temp);
                }


            }

            /*------ get quiz list ------*/
            $quiz_list = \DB::table('quiz_creation')
                ->join('videos', 'videos.identifier', '=', 'quiz_creation.identifier')
                ->join('group_videos', 'group_videos.video_id', '=', 'videos.id')
                ->select('quiz_data')
                ->where([
                    ['group_videos.id', '=', intval($request->group_video_id)]
                ])
                ->orderBy('quiz_creation.created_at', 'desc')
                ->first();

            $quiz_name_list = array();
            if ($quiz_list) {
                $list = json_decode($quiz_list->quiz_data);

                for ($i = 0; $i < count($list); $i++) {
                    $temp = array('name' => $list[$i]->name);
                    array_push($quiz_name_list, $temp);
                }
            }

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'score_ratio', 'portion', 'answer_attempt', 'quiz_name_list'));
        }


        return $result_arr;
    }
}
