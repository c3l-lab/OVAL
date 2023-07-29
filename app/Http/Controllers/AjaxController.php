<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use oval;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use oval\Classes\YoutubeDataHelper;
use oval\Jobs\AnalyzeTranscript;
use oval\Models\Video;
use GuzzleHttp;

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

    //----------- Private Utility Functions -------------------------------------------------------------

    /**
     * Private utility method to format time from seconds to 00:00:00 format
     *
     * @param integer $seconds
     * @return string time in string format of 00:00:00
     */
    private function formatTime($seconds)
    {
        $hours = floor($seconds  / (60 * 60));
        $rest = floor($seconds  % (60 * 60));
        $minutes = floor($rest / 60);
        $rest = floor($rest % 60);
        $seconds = floor($rest);
        $millis = floor($rest);
        $time = $this->doubleDigits($hours) . ":" . $this->doubleDigits($minutes) . ":" . $this->doubleDigits($seconds);
        return $time;
    }

    /**
     * Private utility function to convert number used in time to be double digit (00)
     *
     * @param number $value
     * @return string With 0 at start if single digit number
     */
    private function doubleDigits($value)
    {
        $value = (string) $value;
        if ($value <= 9) {
            $value = "0" . $value;
        }
        return $value;
    }

    /**
     * Private utility method to convert array of string (integer in string format) to array of int
     *
     * When parameter from AJAX request has array of integer in the JSON, convert the array of string to array integer.
     *
     * @param array $stringArray array containing integer values in string format
     * @return array array containing integers
     */
    private function convertStringArrayToIntArray($stringArray)
    {
        $intArray = null;
        if (!empty($stringArray)) {
            $intArray = [];
            foreach($stringArray as $str) {
                $int = intval($str);
                $intArray[] = $int;
            }
        }
        return $intArray;
    }

    /**
     * Private utility method to convert time duration from ISO8601 (00H00M00S) to seconds.
     *
     * @param string $ISO8601 time in format 00H00M00S
     * @return string the duration in seconds
     */
    private function ISO8601ToSeconds($ISO8601)
    {
        preg_match('/\d{1,2}[H]/', $ISO8601, $hours);
        preg_match('/\d{1,2}[M]/', $ISO8601, $minutes);
        preg_match('/\d{1,2}[S]/', $ISO8601, $seconds);

        $duration = [
            'hours'   => $hours ? $hours[0] : 0,
            'minutes' => $minutes ? $minutes[0] : 0,
            'seconds' => $seconds ? $seconds[0] : 0,
        ];

        $hours   = intval(substr($duration['hours'], 0, -1));
        $minutes = intval(substr($duration['minutes'], 0, -1));
        $seconds = intval(substr($duration['seconds'], 0, -1));

        $toltalSeconds = ($hours * 60 * 60) + ($minutes * 60) + $seconds;

        return $toltalSeconds;
    }



    //-----------------------------------------------------------------------------------------



    /**
     * Private function to return annotations for group_video
     *
     * This method fetches current annotations for group_video
     * whose ID passed in, and filter the privacy setting of annotation
     * using the user_id passed in.
     *
     * @param int $user_id
     * @param int $group_video_id
     * @return collection collection of Annotation objects visible to the user
     */
    private function get_all_annotations($user_id, $group_video_id)
    {
        $all_annotations = oval\Models\Annotation::where([
                                ['group_video_id', "=", $group_video_id],
                                ['status', '=', 'current']
                            ])
                            ->get();
        foreach ($all_annotations as $key=>$a) {
            $visible = false;
            $privacy = $a->privacy;
            $mine = $a->user_id==$user_id ? true : false;
            $visible = ($mine || $privacy=="all") ? true : false;
            if ($privacy == "nominated") {
                $audience = json_decode($a->visible_to);
                if (!empty($audience)) {
                    if (in_array($user_id, $audience)) {
                        $visible = true;
                    }
                }
            }
            if(!$visible) {
                $all_annotations->forget($key);
            }
        }
        return $all_annotations;
    }

    /**
     * Methtod called from route /get_annotations to fetch annotations to display on home page
     *
     * The request object should contain course_id, video_id, group_id.
     * @uses get_all_annotations() to fetch annotations.
     * The array returned is formatted ready for display.
     *
     * @param Request $req
     * @return array array of array with keys [id, start_time, name, date, description, tas, miine, privacy, by_instructor]
     */
    public function get_annotations(Request $req)
    {
        $user = Auth::user();
        $course = oval\Models\Course::find(intval($req->course_id));
        $video_id = intval($req->video_id);
        $group_id = intval($req->group_id);
        $group_video_id = oval\Models\GroupVideo::where([
                                ['group_id', '=', $group_id],
                                ['video_id', '=', $video_id]
                            ])
                            ->first()
                            ->id;
        $all_annotations = $this->get_all_annotations($user->id, $group_video_id);
        $annotations = [];

        foreach ($all_annotations as $a) {
            $author = oval\Models\User::find($a->user_id);
            if (empty($author)) {
                $instructor = false;
                $mine = false;
                $name = "Unknown User";
            } else {
                $instructor = $author->isInstructorOf($course);
                $mine = $a->user_id==$user->id ? true : false;
                $name = $author->fullName();
            }

            $date = empty($a->updated_at) ? null : $a->updated_at->format('g:iA d M, Y');

            $annotations[] = [
                "id" => $a->id,
                "start_time" => $a->start_time,
                "name" => $name,
                "date" => $date,
                "description" => $a->description,
                "tags" => $a->tags->pluck('tag'),
                "mine" => $mine,
                "privacy" => $a->privacy,
                "by_instructor" => $instructor
            ];
        }
        return $annotations;
    }

    /**
     * Method called from route /add_annotation to add annotation
     *
     * The method inserts a new annotation and returns the result.
     *
     * @param Request $req Request contains group_video_id, start_time, description, privacy, nominated_students_ids
     * @return array Array with key [result] containing boolean value - true if successfully inserted, false if not.
     */
    public function add_annotation(Request $req)
    {
        $annotation = oval\Models\Annotation::firstOrNew([
            'group_video_id' => intval($req->group_video_id),
            'user_id' => Auth::user()->id,
            'start_time' => $req->start_time,
        ]);
        $annotation->group_video_id = intval($req->group_video_id);
        $annotation->user_id = Auth::user()->id;
        $annotation->start_time = $req->start_time;
        $annotation->description = htmlspecialchars($req->description, ENT_QUOTES);
        $annotation->privacy = $req->privacy;
        $annotation->visible_to = json_encode($this->convertStringArrayToIntArray($req->nominated_students_ids));
        $annotation->save();

        $tags = $req->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = oval\Models\Tag::firstOrCreate(['tag'=>$t]);
            $annotation->tags()->attach($tag);
        }
        $result = $annotation->save();

        return ['result'=>$result];
    }

    /**
     * Method called from route /edit_annotation
     *
     * This method marks old record as "archived"
     * and inserts a new one with the values passed in and returns the result.
     *
     * @param Request $req Request contains annotation_id, start_time, description, privacy, nominated_students_ids, tags
     * @return array Array with key [result] containing boolean value - true if successfully updated, false if not.
     */
    public function edit_annotation(Request $req)
    {
        $old = oval\Models\Annotation::findOrFail(intVal($req->annotation_id));
        if(!empty($old)) {
            $old->status = "archived";
            $old->save();
        }
        $annotation = new oval\Models\Annotation();
        $annotation->group_video_id = $old->group_video_id;
        $annotation->user_id = Auth::user()->id;
        $annotation->start_time = $req->start_time;
        $annotation->description = htmlspecialchars($req->description, ENT_QUOTES);
        $annotation->privacy = $req->privacy;
        $annotation->visible_to = json_encode($this->convertStringArrayToIntArray($req->nominated_students_ids));
        $annotation->save();

        $tags = $req->tags;
        foreach ($tags as $t) {
            $t = htmlspecialchars($t, ENT_QUOTES);
            $tag = oval\Models\Tag::firstOrCreate(['tag'=>$t]);
            $annotation->tags()->attach($tag);
        }
        $result = $annotation->save();
        return compact('result');
    }

    /**
     * Method called from route /delete_annotation
     *
     * This method marks the annotation's status as "deleted"
     *
     * @param Request $req Request contains annotation_id
     * @return void
     */
    public function delete_annotation(Request $req)
    {
        $annotation = oval\Models\Annotation::findOrFail(intval($req->annotation_id));
        $annotation->status = "deleted";
        $annotation->save();
    }

    /**
     * Private method to process Youtube video's text analysis
     * for AnalysisRequest object passed in as parameter.
     * TODO: move this somewhere ... This was copied from another controller.
     *
     * @param oval\Models\AnalysisRequest $analysis_request
     * @return void
     */
    private function process_youtube_text_analysis(oval\Models\AnalysisRequest $analysis_request)
    {
        //--exit if this video already as result--
        $requests = $analysis_request->requestsForSameVideo();
        foreach ($requests as $r) {
            if ($r->status == "processed") {
                return;
            }
        }

        $video = $analysis_request->video;

        // Change status to 'processing'
        oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processing']);

        $user_ids = $analysis_request->requestorsIds();
        array_push($user_ids, Auth::user()->id);

        $caption_text = $this->download_youtube_caption($video);
        $text = "";
        $video = $video->fresh();

        if(!empty($caption_text)) {
            $text = $caption_text;
        } elseif(!empty($video->transcript)) {
            $transcript_json = json_decode($video->transcript->transcript);
            foreach ($transcript_json as $t) {
                $obj = json_decode($t);
                $text .= $obj->transcript." ";
            }
        } else {
            // Change status to 'processed'
            oval\Models\AnalysisRequest::where(array('video_id' => $video->id))->update(['status' => 'processed']);
            return "no transcript";
        }

        // Send analyse transcript job to queue
        $this->dispatch(new AnalyzeTranscript([
            'videoId'    => $video->id,
            'transcript' => $text,
            'userIds'    => $user_ids
        ]));
    }

    /**
     * Private method to get caption of a Youtube video.
     *
     * It downloads Youtube video's caption via Youtube data API
     * using YoutubeDataHelper, or publicly available caption and return the caption text.
     * TODO: move this somewhere - this was copied from another controller.
     * @param Video $video
     * @return string caption text
     */
    private function download_youtube_caption(oval\Models\Video $video)
    {
        $text = "";
        $transcript = $video->transcript;
        if (empty($transcript)) {
            $transcript = new oval\Models\Transcript();
            $transcript->video_id = $video->id;
        }

        $langs = config('youtube.transcript_lang');
        $credentials = oval\Models\GoogleCredential::all();
        $track_id = null;
        $caption_array = null;
        if (!empty($credentials) && count($credentials)>0) {
            foreach ($credentials as $cred) {
                $helper = new YoutubeDataHelper($cred->client_id, $cred->client_secret);
                $helper->handle_access_token_refresh($cred);

                $track_id = $helper->get_caption_track_id($video->identifier);
                if(!empty($track_id)) {
                    $caption_array = $helper->download_caption($track_id);
                }
                if (!empty($caption_array)) {
                    break;
                }
            }
        }
        if (empty($caption_array)) {
            $response = "";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_PROXY, 'proxy.example.com:8080');
            foreach ($langs as $l) {
                curl_setopt($ch, CURLOPT_URL, 'http://video.google.com/timedtext?lang='.$l.'&v='.$video->identifier);
                $response = curl_exec($ch);
                if (!empty($response)) {
                    $cc = simplexml_load_string($response);
                    $caption_array = [];
                    $text = "";
                    foreach ($cc->text as $item) {
                        $line = "{";
                        $time = 0;
                        foreach ($item->attributes() as $key=>$val) {
                            if ($key == "start") {
                                $time = floatval($val);
                                $line .= '"start":'.$time.', ';
                            } elseif ($key == "dur") {
                                $time += floatval($val);
                                $line .= '"end":'.$time.', ';
                                $time = 0;
                            }
                        }
                        $text .= $item;
                        $line .= '"transcript":"'.$item.'"}';
                        $caption_array[] = $line;
                    }
                    break;
                }
            }
        }
        if (!empty($caption_array)) {
            $transcript->transcript = json_encode($caption_array);
            $transcript->save();
        }
        return $text;
    }

    /**
     * Method called from route /get_groups
     *
     * This method fetches groups that belong to the course whose id is passed in.
     *
     * @param Request $req Request contains course_id
     * @return array Array with key [groups] containing collection of group objects
     */
    public function get_groups(Request $req)
    {
        $course_id = $req->course_id;
        $groups = oval\Models\Course::find($course_id)->groups;
        return compact('groups');
    }

    /**
     * Method called from route /get_group_info_for_video
     *
     * This method returns collection of groups the user is teaching,
     * that belong to the course (all_groups),
     * and collection of ids of groups that have this video assigned to (assigned_groups_ids).
     *
     * @param Request $req The request contains course_id, vidveo_id, and user_id
     * @return array with keys [all_groups, assigned_groups_ids]
     */
    public function get_group_info_for_video(Request $req)
    {
        $course_id = intval($req->course_id);
        $video_id = intval($req->video_id);
        $user_id = intval($req->user_id);
        $all_groups = oval\Models\Course::find($course_id)->groups;
        $assigned_groups = oval\Models\Video::find($video_id)
                            ->groups;
        $unassigned_groups = $all_groups->reject(function ($val) use ($assigned_groups) {
            return $assigned_groups->contains($val);
        });
        return compact('unassigned_groups');
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
     * Method called from route /download_annotations
     *
     * This method fetches all comments and annotations visible for the user logged in,
     * and constructs csv file containing these values, then return it as body of Response
     *
     * @param Request $req Request contains group_video_id, course_id
     * @return StreamedResponse
     */
    public function download_annotations(Request $req)
    {
        $user = Auth::user();
        $group_video_id = intval($req->group_video_id);
        $course_id = intval($req->course_id);
        $annotations = null;
        $comments = \oval\Models\Comment::group_video_comments($group_video_id, $user->id);
        $annotations = $this->get_all_annotations($user->id, $group_video_id);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($annotations, $comments) {
            $file_handle = fopen('php://output', 'w');

            $headings = array('type', 'name', 'start time', 'description', 'tags', 'visibility');
            fputcsv($file_handle, $headings);

            $type = "annotation";
            $visibility = "";
            if (count($annotations)>0) {
                foreach ($annotations as $a) {
                    $name = $a['name'];
                    $start = $this->formatTime($a['start_time']);
                    $desc = htmlspecialchars_decode($a['description'], ENT_QUOTES);
                    $tags = $a['tags'];
                    $tag = "";
                    foreach($tags as $t) {
                        $tag .= "'".htmlspecialchars_decode($t->tag, ENT_QUOTES)."', ";
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
                    foreach($tags as $t) {
                        $tag .= "'".htmlspecialchars_decode($t, ENT_QUOTES)."', ";
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
     * Method called from route /get_groups_for_video
     *
     * This method returns groups that have the video with id passed in is assigned to
     *
     * @param Request $req Request contains video_id
     * @return array Array with key "groups" - containing collection of Group objects
     */
    public function get_groups_for_video(Request $req)
    {
        $video = oval\Models\Video::find(intval($req->video_id));
        $groups = $video->groups;
        return compact('groups');
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
     * Method called from route /add_analysis_request
     *
     * This method saves an AnalysisRequest for the video_id and user_id passed in.
     *
     * @param Request $req Request contains video_id and user_id
     * @return array Array with key "msg" which contains text to display when the request is processed
     */
    public function add_analysis_request(Request $req)
    {
        $video_id = intval($req->video_id);
        $user_id = intval($req->user_id);
        $msg = "";
        $ar = oval\Models\AnalysisRequest::where([
                    ['video_id', '=', $video_id],
                    ['user_id', '=', $user_id]
                ])
                ->first();
        if (!empty($ar)) {
            $msg = "Request for this video already exists. Please wait for OVAL administrator to approve it.";
        } else {
            $ar = new oval\Models\AnalysisRequest();
            $ar->video_id = $video_id;
            $ar->user_id = $user_id;
            $res = $ar->save();
            if ($res) {
                $msg = "Request has been sent to OVAL administrator. Please wait for approval.";
            } else {
                $msg = "There was an error. Please try again later.";
            }
        }
        return compact('msg');
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

    /**
     * Method called from route /edit_comment_instruction
     *
     * This method fetches existing CommentInsruction or creates a new one,
     * then saves the values passed in as parameter.
     *
     * @param Requestt $req Request contains group_video_id, and description
     * @return string description The description of CommentInstruction
     */
    public function edit_comment_instruction(Request $req)
    {
        $group_video_id = intval($req->group_video_id);
        $comment_instruction = oval\Models\CommentInstruction::where('group_video_id', '=', $group_video_id)
                                ->first();
        if (empty($comment_instruction)) {
            $comment_instruction = new oval\Models\CommentInstruction();
        }
        $comment_instruction->group_video_id = $group_video_id;
        $comment_instruction->description = htmlspecialchars($req->description, ENT_QUOTES);
        $comment_instruction->save();
        return $comment_instruction->description;
    }

    /**
     * Method called from route /delete_comment_instruction
     *
     * This method deletes CommentInstruction for the group_video_id passed in.
     *
     * @param Request $req Request contains group_video_id
     * @return void
     */
    public function delete_comment_instruction(Request $req)
    {
        $group_video_id = intval($req->group_video_id);
        $comment_instruction = oval\Models\CommentInstruction::where('group_video_id', '=', $group_video_id);
        $comment_instruction->delete();
    }

    /**
     * Method called from route /get_annotations_for_tag
     *
     * This method returns annotations with tag passed in as parameter.
     *
     * @param Request $req Request contains course_id, tag
     * @return array Array of array with keys [id, start_time, user_id, name, description, tags, is_mine, privacy, updated_at, by_instructor]

     */
    public function get_annotations_for_tag(Request $req)
    {
        $user = Auth::user();
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $course = $group_video->course();
        $tag = $req->tag;
        $annotations = oval\Models\Annotation::where([
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
            $u = oval\Models\User::find($a->user_id);
            $mine = $user->id == $u->id ? true : false;
            $date = empty($a->updated_at) ? null : $a->updated_at->format('g:iA d M, Y');
            $instructor = $u->isInstructorOf($course);
            $retval[] = [
                "id"=>$a->id,
                "start_time"=>$this->formatTime($a->start_time),
                "user_id"=>$u->id,
                "name"=>$u->fullName(),
                "description"=>$a->description,
                "tags"=>$a->tags->pluck('tag'),
                "is_mine"=>$mine,
                "privacy"=>$a->privacy,
                "updated_at"=>$date,
                "by_instructor"=>$instructor
            ];
        }
        return $retval;
    }

    /*------ quiz ajax function ------*/
    public function get_quiz(Request $req)
    {
        $quiz = oval\Models\quiz_creation::where('identifier', $req->identifier)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        return compact("quiz");
    }

    public function store_quiz(Request $req)
    {

        $quiz = new oval\Models\quiz_creation();
        $quiz->creator_id = intval($req->creator_id);
        $quiz->identifier = (string)($req->identifier);
        $quiz->media_type = (string)($req->media_type);
        $quiz->quiz_data = json_encode($req->quiz_data);
        $quiz->visable = 1;
        $result = $quiz->save();

        return ['result' => 'success'];
    }

    public function submit_ans(Request $req)
    {

        $quiz_ans = new oval\Models\quiz_result();
        $quiz_ans->user_id = intval($req->user_id);
        $quiz_ans->identifier = (string)($req->identifier);
        $quiz_ans->media_type = (string)($req->media_type);
        $quiz_ans->quiz_data = json_encode($req->quiz_data);
        $result = $quiz_ans->save();

        return ['result' => 'success'];

    }

    public function change_quiz_visable(Request $req)
    {

        DB::table('quiz_creation')
            ->join('videos', 'videos.identifier', '=', 'quiz_creation.identifier')
            ->where('videos.id', '=', $req->videoid)
            ->update(['visable' => $req->visable]);

        return ['result' => 'success'];
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

    public function get_annotations_column(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ get num of annotation, avg length ------*/
            $annotation_info = DB::table('annotations')
                               ->where([
                                    ['group_video_id', '=', $group_video->id],
                                    ['user_id', '=', $user->id],
                               ])
                               ->get();

            if(count($annotation_info) > 0) {
                $annotation_num = count($annotation_info);

                $annotation_length_total = 0;
                for ($i = 0; $i < $annotation_num; $i++) {
                    $annotation_length_total += str_word_count($annotation_info[$i]->description, 0);
                }

                $annotation_average_length = ceil($annotation_length_total/$annotation_num);
            } else {
                $annotation_num = 0;
                $annotation_average_length = 0;
            }

            /*------ get Number of annotations edited, Number of annotations viewed, Average time spent viewing each annotation ------*/
            $annotation_edited_num = DB::table('trackings')
                                        ->select('event', 'info', 'event_time')
                                        ->where([
                                            ['group_video_id', '=', $group_video->id],
                                            ['user_id', '=', $user->id],
                                            ['event', '=', 'click'],
                                            ['info', '=', 'Edit annotation']
                                        ])
                                        ->count();

            $annotation_viewed_num = DB::table('trackings')
                                        ->select('event', 'info', 'event_time')
                                        ->where([
                                            ['group_video_id', '=', $group_video->id],
                                            ['user_id', '=', $user->id],
                                            ['event', '=', 'click'],
                                            ['info', '=', 'View an annotation']
                                        ])
                                        ->count();

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
            $annotations_total = 0;
            $annotations_num = count($annotations_close);

            for ($i = 0; $i < $annotations_num; $i++) {
                $annotations_total += (strtotime($annotations_view[$i]->event_time) - strtotime($annotations_close[$i]->event_time));
            }

            if($annotations_num > 0) {
                $annotations_average_time = $annotations_total/$annotations_num;
            } else {
                $annotations_average_time = 0;
            }

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'annotation_num', 'annotation_average_length', 'annotation_edited_num', 'annotation_viewed_num', 'annotations_average_time'));

        }

        return $result_arr;

    }

    public function get_comment_column(Request $req)
    {
        $group_video = oval\Models\GroupVideo::find(intval($req->group_video_id));
        $users = $group_video->usersWhoAccessed();

        $result_arr = [];

        foreach ($users as $user) {
            $surname = $user->last_name;
            $first_name = $user->first_name;
            $student_id = $user->email;

            /*------ Number of comments, Average Comment Length (word count) ------*/
            $comment_info = DB::table('comments')
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

            if($comment_num > 0) {
                $comment_average_length = ceil($comment_total/$comment_num);
            } else {
                $comment_average_length = 0;
            }

            /*------ Number of comments edited, Number of comments viewed, Average time spent viewing each comment------*/
            $comment_edited_num = DB::table('trackings')
                                  ->select('user_id', 'event', 'info', 'event_time')
                                  ->where([
                                        ['group_video_id', '=', $group_video->id],
                                        ['user_id', '=', $user->id],
                                        ['event', '=', 'click'],
                                        ['info', '=', 'Edit comment']
                                  ])
                                  ->count();

            $comment_view_info = DB::table('trackings')
                                 ->select('event_time')
                                 ->where([
                                    ['group_video_id', '=', $group_video->id],
                                    ['user_id', '=', $user->id],
                                    ['event', '=', 'View']
                                 ])
                                 ->orderBy('event_time', 'desc')
                                 ->get();

            $comment_viewed_num = count($comment_view_info);

            if(floor($comment_viewed_num/2) > 0) {
                $comment_viewed_length = 0;

                for($i = 0; $i < floor($comment_viewed_num/2); $i = $i + 2) {

                    $comment_viewed_length += (strtotime($comment_view_info[$i]->event_time) - strtotime($comment_view_info[$i+1]->event_time));

                }

                $comment_average_time = $comment_viewed_length/floor($comment_viewed_num/2);

            } else {
                $comment_average_time = 0;
            }

            array_push($result_arr, compact('surname', 'first_name', 'student_id', 'comment_num', 'comment_average_length', 'comment_edited_num', 'comment_viewed_num', 'comment_average_time'));

        }

        return $result_arr;
    }

    // public function get_video_point_answer (Request $req){
    // 	// $user_arr = explode(',', $req->user_id);

    // 	// $result_arr = [];

    // 	// for($x = 0; $x < count($user_arr); $x++){
    // 	// 	/*------ get user surname, first name, student ID ------*/
    // 	// 	$user_info = DB::table('users')
    // 	// 				->select('first_name', 'last_name', 'email')
    // 	// 				->where([
    // 	// 					['id', '=', $user_arr[$x]]
    // 	// 				])
    // 	// 				->first();

    // 	//     $surname = $user_info->first_name;
    // 	//    	$first_name = $user_info->last_name;
    // 	// 	$student_id = $user_info->email;

    // 	// 	/*------ get video point anser ------*/
    // 	// 	$feedback = DB::table('feedbacks')
    // 	// 				->select('point_id', 'answer')
    // 	// 				->orderby('point_id')
    // 	// 				->orderby('created_at','desc')
    // 	// 				->get();

    // 	// 	array_push($result_arr, compact('feedback'));

    // 	// }

    // 	// return $result_arr;
    // }

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
            $quiz = oval\Models\quiz_creation::where('identifier', '=', oval\Models\Video::find($video_id)->identifier)
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
