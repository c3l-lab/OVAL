<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\QuizResult;

class QuizResultController extends Controller
{
    public function store(Request $request)
    {
        $quiz_ans = new QuizResult();
        $quiz_ans->user_id = intval($request->user_id);
        $quiz_ans->identifier = (string)($request->identifier);
        $quiz_ans->media_type = (string)($request->media_type);
        $quiz_ans->quiz_data = json_encode($request->quiz_data);
        $quiz_ans->save();

        return ['result' => 'success'];
    }
}
