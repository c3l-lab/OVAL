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
        $comments = Comment::groupVideoComments($group_video_id, $user->id);
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
                "id" => $c->id,
                "user_id" => $u->id,
                "name" => $u->fullName(),
                "description" => $c->description,
                "tags" => $c->tags->pluck('tag'),
                "is_mine" => $mine,
                "privacy" => $c->privacy,
                "updated_at" => $date,
                "by_instructor" => $instructor
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

    public function report(Request $request)
    {
        $group_video = GroupVideo::find(intval($request->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ Number of comments, Average Comment Length (word count) ------*/
            $comment_info = \DB::table('comments')
                ->select('description')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                ])
                ->get();

            $comment_num = count($comment_info);

            $comment_total = 0;

            for ($i = 0; $i < $comment_num; $i++) {
                $comment_total += str_word_count($comment_info[$i]->description, 0);
            }

            if ($comment_num > 0) {
                $comment_average_length = ceil($comment_total / $comment_num);
            } else {
                $comment_average_length = 0;
            }

            /*------ Number of comments edited, Number of comments viewed, Average time spent viewing each comment------*/
            $comment_edited_num = \DB::table('trackings')
                ->select('user_id', 'event', 'info', 'event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'click'],
                    ['info', '=', 'Edit comment']
                ])
                ->count();

            $comment_view_info = \DB::table('trackings')
                ->select('event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'View']
                ])
                ->orderBy('event_time', 'desc')
                ->get();

            $comment_viewed_num = count($comment_view_info);

            if (floor($comment_viewed_num / 2) > 0) {
                $comment_viewed_length = 0;

                for ($i = 0; $i < floor($comment_viewed_num / 2); $i = $i + 2) {

                    $comment_viewed_length += (strtotime($comment_view_info[$i]->event_time) - strtotime($comment_view_info[$i + 1]->event_time));

                }

                $comment_average_time = $comment_viewed_length / floor($comment_viewed_num / 2);

            } else {
                $comment_average_time = 0;
            }

            array_push(
                $result_arr,
                compact(
                    'surname',
                    'first_name',
                    'student_id',
                    'comment_num',
                    'comment_average_length',
                    'comment_edited_num',
                    'comment_viewed_num',
                    'comment_average_time'
                )
            );

        }

        return $result_arr;
    }

    public function detail(Request $request)
    {
        $fileName = "comments_detail.csv";

        $comments = Comment::with('user', 'tags')->where([
            'status' => 'current',
            'group_video_id' => $request->group_video_id
        ])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Video ID', 'User email', 'Comment', 'Tags', 'Created', 'Updated');

        $callback = function () use ($comments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($comments as $comment) {
                $row['Video ID']  = $comment->group_video_id;
                $row['User email'] = isset($comment->user) ? $comment->user->email : '';
                $row['Comment']  = $comment->description;
                $row['Tags'] = join(",", array_map(function ($tag) {
                    return $tag['tag'];
                }, $comment->tags->toArray()));
                $row['Created']  = $comment->created_at;
                $row['Updated']  = $comment->updated_at;

                fputcsv($file, array(
                    $row['Video ID'],
                    $row['User email'],
                    $row['Comment'],
                    $row['Tags'],
                    $row['Created'],
                    $row['Updated']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
