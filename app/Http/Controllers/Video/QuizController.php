<?php

namespace oval\Http\Controllers\Video;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\QuizCreation;

class QuizController extends Controller
{
    public function show(Request $request, string $id)
    {
        $quiz = QuizCreation::where('identifier', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'quiz' => $quiz,
        ];

    }

    public function update(Request $request, string $id)
    {
        $quiz = new QuizCreation();
        $quiz->creator_id = intval($request->creator_id);
        $quiz->identifier = (string)($id);
        $quiz->media_type = (string)($request->media_type);
        $quiz->quiz_data = json_encode($request->quiz_data);
        $quiz->visable = 1;
        $quiz->save();

        return ['result' => 'success'];
    }

    public function toggleVisible(Request $request, int $id)
    {
        \DB::table('quiz_creation')
            ->join('videos', 'videos.identifier', '=', 'quiz_creation.identifier')
            ->where('videos.id', '=', $id)
            ->update(['visable' => $request->visable]);

        return ['result' => 'success'];
    }
}
