<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\CommentInstruction;

class CommentInstructionController extends Controller
{
    public function store(Request $request)
    {
        $group_video_id = intval($request->group_video_id);
        $comment_instruction = CommentInstruction::where('group_video_id', '=', $group_video_id)
            ->first();
        if (empty($comment_instruction)) {
            $comment_instruction = new CommentInstruction();
        }
        $comment_instruction->group_video_id = $group_video_id;
        $comment_instruction->description = htmlspecialchars($request->description, ENT_QUOTES);
        $comment_instruction->save();
        return $comment_instruction->description;
    }

    public function destroy(Request $request, int $id)
    {
        $group_video_id = $id;
        $comment_instruction = CommentInstruction::where('group_video_id', '=', $group_video_id);
        $comment_instruction->delete();
    }
}
