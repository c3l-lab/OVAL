<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use oval\Models\Comment;
use oval\Models\GroupVideo;
use oval\Models\Tag;
use oval\Models\User;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $group_video_id = $request->query('group_video_id');
        $comments = Comment::group_video_comments($group_video_id, $user->id);
        return $comments;
    }

    public function tag(Request $request)
    {
        $user = \Auth::user();
        $group_video = GroupVideo::find(intval($request->group_video_id));
        $course = $group_video->course();
        $tag = $request->tag;
        $comments = Comment::where([
                        ['status', '=', 'current'],
                        ['group_video_id', '=', $group_video->id]
                    ])
                    ->whereHas('tags', function ($q) use ($tag) {
                        $q->where('tag', '=', $tag);
                    })
                    ->orderBy('updated_at', 'desc')
                    ->get();
        $retval = [];
        foreach ($comments as $c) {
            $u = User::find($c->user_id);
            $mine = $user->id == $u->id ? true : false;
            $date = empty($c->updated_at) ? null : $c->updated_at->format('g:iA d M, Y');
            $instructor = $u->isInstructorOf($course);
            $retval[] = [
                "id"=>$c->id,
                "user_id"=>$u->id,
                "name"=>$u->fullName(),
                "description"=>$c->description,
                "tags"=>$c->tags->pluck('tag'),
                "is_mine"=>$mine,
                "privacy"=>$c->privacy,
                "updated_at"=>$date,
                "by_instructor"=>$instructor
            ];
        }
        return $retval;
    }

    public function store(Request $request, Comment $comment)
    {
        $comment = new Comment();
        $comment->group_video_id = intval($request->group_video_id);
        $comment->user_id = \Auth::user()->id;
        $comment->description = htmlspecialchars($request->description, ENT_QUOTES);
        $comment->privacy = $request->privacy;
        $comment->visible_to = json_encode(convertStringArrayToIntArray($request->nominated_students_ids));
        $comment->save();

        $tags = $request->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = Tag::firstOrCreate(['tag' => $t]);
            $comment->tags()->attach($tag);
        }
        $comment->save();

        $c = array(
            "id" => $comment->id,
            "user_id" => $comment->user->id,
            "user_fullname" => $comment->user->fullName(),
            "description" => $comment->description,
            "tags" => $comment->tags->pluck('tag'),
            "is_mine" => true,
            "privacy" => $comment->privacy,
            "updated_at" => $comment->updated_at
        );
        return $c;
    }

    public function update(Request $request, int $id)
    {
        $old = Comment::findOrFail(intVal($id));

        if (!empty($old)) {
            $old->status = "archived";
            $old->save();
        }
        $comment = new Comment();
        $comment->group_video_id = $old->group_video_id;
        $comment->user_id = \Auth::user()->id;
        $comment->description = htmlspecialchars($request->description, ENT_QUOTES);
        $comment->privacy = $request->privacy;
        $comment->visible_to = json_encode(convertStringArrayToIntArray($request->nominated_students_ids));
        $comment->parent = $old->id;
        $comment->save();

        $tags = $request->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = Tag::firstOrCreate(['tag' => $t]);
            $comment->tags()->attach($tag);
        }
        $comment->save();

        $c = array(
            "id" => $comment->id,
            "user_id" => $comment->user->id,
            "user_fullname" => $comment->user->fullName(),
            "description" => $comment->description,
            "tags" => $comment->tags->pluck('tag'),
            "is_mine" => true,
            "privacy" => $comment->privacy,
            "updated_at" => $comment->updated_at
        );
        return $c;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->status = "deleted";
        $comment->save();
    }
}
