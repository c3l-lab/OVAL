<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\QuizResult;

class QuizResultController extends Controller
{
    public function store(Request $request)
    {
        $quiz_ans = new QuizResult();
        $quiz_ans->user_id = $request->user()->id;
        $quiz_ans->group_video_id = $request->group_video_id;
        $quiz_ans->media_type = (string)($request->media_type);
        $quiz_ans->quiz_data = json_encode($request->quiz_data);
        $quiz_ans->save();

        return ['result' => 'success'];
    }

    public function report(Request $request)
    {
        $user_arr = empty($request->user_id) ? [] : explode(',', $request->user_id);

        $result_arr = [];

        for($x = 0; $x < count($user_arr); $x++) {

            /*------ get user surname, first name, student ID ------*/
            $user_info = \DB::table('users')
            ->select('first_name', 'last_name', 'email')
            ->where([
                ['id', '=', $user_arr[$x]]
            ])
            ->first();

            $surname = $user_info->first_name;
            $first_name = $user_info->last_name;
            $student_id = $user_info->email;

            /*------ get all attempt record ------*/
            $student_record_list = \DB::table('quiz_result')
                        ->select('quiz_result.quiz_data', 'quiz_result.created_at')
                        ->where([
                            ['group_video_id', '=', $request->group_video_id],
                            ['quiz_result.user_id', '=', $user_arr[$x]]
                        ])
                        ->orderBy('quiz_result.created_at', 'desc')
                        ->get();

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'student_record_list', 'student_record_list'));

        }

        return $result_arr;
    }
}
