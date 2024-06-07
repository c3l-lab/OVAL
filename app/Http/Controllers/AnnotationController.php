<?php

namespace oval\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use oval\Models\Annotation;
use oval\Models\Comment;
use oval\Models\Course;
use oval\Models\GroupVideo;
use oval\Models\Tag;
use oval\Models\User;
use oval\Models\QuizCreation;
use oval\Models\QuizResult;

class AnnotationController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $course = Course::find(intval($request->course_id));
        $video_id = intval($request->video_id);
        $group_id = intval($request->group_id);
        $group_video_id = GroupVideo::where([
            ['group_id', '=', $group_id],
            ['video_id', '=', $video_id]
        ])
            ->first()
            ->id;
        $all_annotations = Annotation::groupVideoAnnotations($group_video_id, $user->id);
        $annotations = [];

        $structured_annotation_ids = $all_annotations
            ->filter(function ($e) {
                return $e->is_structured_annotation && is_numeric($e->description);
            })
            ->map(function ($e) {
                return (int) $e->description;
            });
        $structured_annotations = QuizCreation::whereIn('id', $structured_annotation_ids)->get(['id', 'quiz_data']);
        $structured_annotation_answers = QuizResult::where('user_id', $user->id)
            ->where('media_type', config('constants.ANNOTATION_QUIZ_MEDIA_TYPE'))
            ->where('group_video_id', $group_video_id)
            ->first();

        foreach ($all_annotations as $a) {
            $author = User::find($a->user_id);
            if (empty($author)) {
                $instructor = false;
                $mine = false;
                $name = "Unknown User";
            } else {
                $instructor = $author->isInstructorOf($course);
                $mine = $a->user_id == $user->id ? true : false;
                $name = $author->fullName();
            }

            $date = empty($a->updated_at) ? null : $a->updated_at->format('g:iA d M, Y');

            if($a->is_structured_annotation && is_numeric($a->description)) {
                $structured_annotation = $structured_annotations->first(function ($e) use ($a) {
                    return $e->id == (int) $a->description;
                });

                if($structured_annotation !== null) {
                    $a->description = $structured_annotation->quiz_data;
                } else {
                    $a->description = '';
                }
            }

            $annotations[] = [
                "id" => $a->id,
                "start_time" => $a->start_time,
                "name" => $name,
                "date" => $date,
                "description" => $a->description,
                "tags" => $a->tags->pluck('tag'),
                "mine" => $mine,
                "privacy" => $a->privacy,
                "by_instructor" => $instructor,
                "is_structured_annotation" => $a->is_structured_annotation
            ];
        }

        return [
            "annotations" => $annotations, 
            "structured_annotation_answers" => data_get($structured_annotation_answers, "quiz_data", null)
        ];
    }

    public function store(Request $request)
    {
        $annotation = Annotation::firstOrNew([
            'group_video_id' => intval($request->group_video_id),
            'user_id' => \Auth::user()->id,
            'start_time' => $request->start_time,
        ]);
        $annotation->group_video_id = intval($request->group_video_id);
        $annotation->user_id = \Auth::user()->id;
        $annotation->start_time = $request->start_time;

        if($request->is_structured_annotation) {
            $annotation->is_structured_annotation = $request->is_structured_annotation === 'true';
        }
   
        $annotation->description = $this->add_annotation_description($request);
        $annotation->privacy = $request->privacy;
        $annotation->visible_to = json_encode(convertStringArrayToIntArray($request->nominated_students_ids));
        $annotation->save();

        $tags = $request->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = Tag::firstOrCreate(['tag' => $t]);
            $annotation->tags()->attach($tag);
        }
        $result = $annotation->save();

        //track
        $record = [
            'event' => 'click',
            'target' => '#save',
            'info' => 'Save annotation',
            'event_time' => $annotation->created_at,
            'ref_id' => $annotation->id,
            'ref_type' => 'annotation',
            'video_time' => data_get($request, 'video_time', null),
        ];
        $this->track($annotation->group_video_id, $record);

        return ['result' => $result];
    }

    public function update(Request $request, int $id)
    {
        $old = Annotation::findOrFail($id);
        if (!empty($old)) {
            $old->status = "archived";
            $old->save();

            if($old->is_structured_annotation && is_numeric($old->description)) {
                QuizCreation::destroy((int) $old->description);
            }
        }
        $annotation = new Annotation();
        $annotation->group_video_id = $old->group_video_id;
        $annotation->user_id = \Auth::user()->id;
        $annotation->start_time = $request->start_time;

        if($request->is_structured_annotation) {
            $annotation->is_structured_annotation = $request->is_structured_annotation === 'true';
        }

        $annotation->description = $this->add_annotation_description($request);
        $annotation->privacy = $request->privacy;
        $annotation->visible_to = json_encode(convertStringArrayToIntArray($request->nominated_students_ids));
        $annotation->save();

        $tags = $request->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = Tag::firstOrCreate(['tag' => $t]);
            $annotation->tags()->attach($tag);
        }
        $result = $annotation->save();

        //track
        $record = [
            'event' => 'click',
            'target' => '.edit-annotation-button',
            'info' => 'Edit annotation:'.$old->id,
            'event_time' => $annotation->created_at,
            'ref_id' => $annotation->id,
            'ref_type' => 'annotation',
            'video_time' => data_get($request, 'video_time', null),
        ];
        $this->track($annotation->group_video_id, $record);
                
        return compact('result');
    }

    public function destroy(Request $request, int $id)
    {
        $annotation = Annotation::findOrFail($id);

        if($annotation->is_structured_annotation && is_numeric($annotation->description)) {
            QuizCreation::destroy((int) $annotation->description);
        }

        $annotation->status = "deleted";
        $annotation->save();

        //track
        $video_time_str = $request->query('video_time', null);
        $video_time = (!is_null($video_time_str) && is_numeric($video_time_str)) ? (double)$video_time_str : null;
        $record = [
            'event' => 'click',
            'target' => '#delete',
            'info' => 'Delete annotation',
            'event_time' => $annotation->created_at,
            'ref_id' => $annotation->id,
            'ref_type' => 'annotation',
            'video_time' => $video_time
        ];
        $this->track($annotation->group_video_id, $record);
    }

    public function submit_structured_annotation_answer(Request $request) {
        $quiz_ans = new QuizResult();
        $quiz_ans->user_id = $request->user()->id;
        $quiz_ans->group_video_id = $request->group_video_id;
        $quiz_ans->media_type = config('constants.ANNOTATION_QUIZ_MEDIA_TYPE');
        $quiz_ans->quiz_data = json_encode($request->result);
        $quiz_ans->save();

        return ['result' => 'success'];
    }

    public function download(Request $request)
    {
        $user = \Auth::user();
        $group_video_id = intval($request->group_video_id);
        $annotations = null;
        $comments = Comment::groupVideoComments($group_video_id, $user->id);
        $annotations = Annotation::groupVideoAnnotations($group_video_id, $user->id);

        $structured_annotation_ids = $annotations
        ->filter(function ($e) {
            return $e->is_structured_annotation && is_numeric($e->description);
        })
        ->map(function ($e) {
            return (int) $e->description;
        });
        $structured_annotations = QuizCreation::whereIn('id', $structured_annotation_ids)->get(['id', 'quiz_data']);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($annotations, $comments, $structured_annotations) {
            $file_handle = fopen('php://output', 'w');

            $headings = array('type', 'name', 'start time', 'description', 'tags', 'visibility');
            fputcsv($file_handle, $headings);

            $type = "annotation";
            $visibility = "";
            if (count($annotations) > 0) {
                foreach ($annotations as $a) {
                    $name = $a['name'];
                    $start = formatTime($a['start_time']);

                    if($a->is_structured_annotation && is_numeric($a->description)) {
                        $structured_annotation = $structured_annotations->first(function ($e) use ($a) {
                            return $e->id == (int) $a->description;
                        });
        
                        if($structured_annotation !== null) {
                            $a['description'] = $structured_annotation->quiz_data;
                        } else {
                            $a['description'] = htmlspecialchars_decode($a['description'], ENT_QUOTES);
                        }
                    }

                    $desc = $a['description'];
                    $tags = $a['tags'];
                    $tag = "";
                    foreach ($tags as $t) {
                        $tag .= "'" . htmlspecialchars_decode($t->tag, ENT_QUOTES) . "', ";
                    }
                    $tag = substr($tag, 0, -2);
                    if ($a['privacy'] == "private") {
                        $visibility = "Private";
                    } elseif ($a['privacy'] == "all") {
                        $visibility = "All students in course";
                    } elseif ($a['privacy'] == "nominated") {
                        $visibility = "Nominated studnets";
                    }
                    $row = array($type, $name, $start, $desc, $tag, $visibility);
                    fputcsv($file_handle, $row);
                }
            }

            $type = "comment";
            $start = "";
            if (count($comments) > 0) {
                foreach ($comments as $c) {
                    $name = $c['name'];
                    $desc = htmlspecialchars_decode($c['description'], ENT_QUOTES);
                    $tags = $c['tags'];
                    $tag = "";
                    foreach ($tags as $t) {
                        $tag .= "'" . htmlspecialchars_decode($t, ENT_QUOTES) . "', ";
                    }
                    $tag = substr($tag, 0, -2);
                    if ($c['privacy'] == "private") {
                        $visibility = "Private";
                    } elseif ($c['privacy'] == "all") {
                        $visiblity = "All students in course";
                    } elseif ($c['privacy'] == "nominated") {
                        $visibility = "Nominated studnets";
                    }
                    $row = array($type, $name, $start, $desc, $tag, $visibility);
                    fputcsv($file_handle, $row);
                }
            }

            fclose($file_handle);
        });

        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=annotations.csv');
        $response->headers->set('Expires', '0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    public function tag(Request $request)
    {
        $user = \Auth::user();
        $group_video = GroupVideo::find(intval($request->group_video_id));
        $course = $group_video->course();
        $tag = $request->tag;
        $annotations = Annotation::where([
            ['status', '=', 'current'],
            ['group_video_id', '=', $group_video->id]
        ])
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tag', '=', $tag);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        $retval = [];
        foreach ($annotations as $a) {
            $u = User::find($a->user_id);
            $mine = $user->id == $u->id ? true : false;
            $date = empty($a->updated_at) ? null : $a->updated_at->format('g:iA d M, Y');
            $instructor = $u->isInstructorOf($course);
            $retval[] = [
                "id" => $a->id,
                "start_time" => formatTime($a->start_time),
                "user_id" => $u->id,
                "name" => $u->fullName(),
                "description" => $a->description,
                "tags" => $a->tags->pluck('tag'),
                "is_mine" => $mine,
                "privacy" => $a->privacy,
                "updated_at" => $date,
                "by_instructor" => $instructor
            ];
        }
        return $retval;
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

            /*------ get num of annotation, avg length ------*/
            $annotation_info = \DB::table('annotations')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                ])
                ->get();

            if (count($annotation_info) > 0) {
                $annotation_num = count($annotation_info);

                $annotation_length_total = 0;
                for ($i = 0; $i < $annotation_num; $i++) {
                    $annotation_length_total += str_word_count($annotation_info[$i]->description, 0);
                }

                $annotation_average_length = ceil($annotation_length_total / $annotation_num);
            } else {
                $annotation_num = 0;
                $annotation_average_length = 0;
            }

            /*------ get Number of annotations edited, Number of annotations viewed, Average time spent viewing each annotation ------*/
            $annotation_edited_num = \DB::table('trackings')
                ->select('event', 'info', 'event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'click'],
                    ['info', '=', 'Edit annotation']
                ])
                ->count();

            $annotation_viewed_num = \DB::table('trackings')
                ->select('event', 'info', 'event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'click'],
                    ['info', '=', 'View an annotation']
                ])
                ->count();

            $annotations_view = \DB::table('trackings')
                ->select('user_id', 'event', 'info', 'event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'click'],
                    ['info', '=', 'View an annotation']
                ])
                ->orderBy('event_time', 'desc')
                ->get();

            $annotations_close = \DB::table('trackings')
                ->select('user_id', 'event', 'info', 'event_time')
                ->where([
                    ['group_video_id', '=', $group_video->id],
                    ['user_id', '=', $user->id],
                    ['event', '=', 'click'],
                    ['info', '=', 'Close annotation preview']
                ])
                ->orderBy('event_time', 'desc')
                ->get();
            $annotations_total = 0;
            $annotations_num = count($annotations_close);

            for ($i = 0; $i < $annotations_num; $i++) {
                $annotations_total += (strtotime($annotations_view[$i]->event_time) - strtotime($annotations_close[$i]->event_time));
            }

            if ($annotations_num > 0) {
                $annotations_average_time = $annotations_total / $annotations_num;
            } else {
                $annotations_average_time = 0;
            }

            array_push(
                $result_arr,
                compact(
                    'surname',
                    'first_name',
                    'student_id',
                    'annotation_num',
                    'annotation_average_length',
                    'annotation_edited_num',
                    'annotation_viewed_num',
                    'annotations_average_time'
                )
            );

        }

        return $result_arr;

    }

    public function detail(Request $request)
    {
        $fileName = "annotations_detail.csv";

        $annotations = Annotation::with('author', 'tags')->where([
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

        $columns = array('Video ID', 'User email', 'Time in Video', 'Annotation', 'Tags', 'Created', 'Updated');

        $callback = function () use ($annotations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($annotations as $annotation) {
                $row['Video ID']  = $annotation->group_video_id;
                $row['User email'] = isset($annotation->author) ? $annotation->author->email : '';
                $row['Time in Video'] = $annotation->start_time;
                $row['Annotation']  = $annotation->description;
                $row['Tags'] = join(",", array_map(function ($tag) {
                    return $tag['tag'];
                }, $annotation->tags->toArray()));
                $row['Created']  = $annotation->created_at;
                $row['Updated']  = $annotation->updated_at;

                fputcsv($file, array(
                    $row['Video ID'],
                    $row['User email'],
                    $row['Time in Video'],
                    $row['Annotation'],
                    $row['Tags'],
                    $row['Created'],
                    $row['Updated']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // helpers
    private function add_annotation_description($request){
        $description = '';
        json_decode($request->description);
        if($request->is_structured_annotation && json_last_error() === JSON_ERROR_NONE) {
            $quiz = new QuizCreation();
            $quiz->creator_id = \Auth::user()->id;
            $quiz->group_video_id = $request->group_video_id;
            $quiz->media_type = config('constants.ANNOTATION_QUIZ_MEDIA_TYPE');
            $quiz->quiz_data = $request->description;
            $quiz->visable = 1;
            $quiz->save();

            $description = $quiz->id;
        } else {
            $description = htmlspecialchars($request->description, ENT_QUOTES);
        }

        return $description;
    }
}
