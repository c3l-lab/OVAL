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

    public function detail(Request $request)
    {
        $fileName = "quiz_results_detail.csv";

        $quizResults = QuizResult::with('author')->where([
            'group_video_id' => $request->group_video_id
        ])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Video ID', 'User email', 'Quiz Name', 'Question Name', 'User Answer', 'Created', 'Updated');

        $callback = function () use ($quizResults, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($quizResults as $quizResult) {
                $quiz = json_decode($quizResult->quiz_data);

                foreach ($quiz->items as $question) {
                    $row['Video ID']  = $quizResult->group_video_id;
                    $row['User email'] = isset($quizResult->author) ? $quizResult->author->email : '';
                    $row['Quiz Name'] = $quiz->name;
                    $row['Question Name']  = $question->title;
                    $row['User Answer'] = json_encode(empty($question->ans) ? $question->user_ans : $question->ans);
                    $row['Created']  = $quizResult->created_at;
                    $row['Updated']  = $quizResult->updated_at;

                    fputcsv($file, array(
                        $row['Video ID'],
                        $row['User email'],
                        $row['Quiz Name'],
                        $row['Question Name'],
                        $row['User Answer'],
                        $row['Created'],
                        $row['Updated']
                    ));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
