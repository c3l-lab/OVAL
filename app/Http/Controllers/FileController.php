<?php

namespace oval\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use oval;

/**
 * This class handles file related request called from web routes when php forms submit
 */
class FileController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Method called from /upload_transcript
     *
     * This is called when user clicks "upload" button on upload transcript modal
     * in video-management page.
     *
     * @param Request $req Request contains video_id, file
     * @return Illuminate\Http\RedirectResponse redirects to video-management page
     */
    public function upload_transcript (Request $req) {
		$video_id = intval($req->video_id);
        $file = $req->file;
		$path = $file->store('transcripts');
		$srt = trim(Storage::get($path));

        $text = "[";
        $items = preg_split("/\r\n\r\n|\n\n|\r\r/", $srt);
        foreach ($items as $item) {
            $lines = preg_split("/\r\n|\n|\r/", $item);
            for ($i=0; $i<count($lines); $i++) {
                if ($i == 0) {
                    $text .= '"{';
                }
                elseif ($i == 1) {
                    $start_to_end = explode(" ", $lines[$i]);
                    $start_parts = explode(":", $start_to_end[0]);
                    $start = intval($start_parts[0])*3600 + intval($start_parts[1])*60 + floatval(str_replace(',', '.', $start_parts[2]));
                    $end_parts = explode(":", $start_to_end[2]);
                    $end = intval($end_parts[0])*3600 + intval($end_parts[1])*60 + floatval(str_replace(',', '.', $end_parts[2]));
                    $text .= '\"start\": '.$start.', \"end\": '.$end.', \"transcript\": \"';
                }
                else {
                    $text .= $lines[$i];
                }
                if ($i == count($lines)-1) {
                    $text .= '\"}",';
                }
            }
        }
        $text = rtrim($text, ",").']';

        $video = oval\Models\Video::find($video_id);

        $transcript = oval\Models\Transcript::find($video_id);
        if (empty($transcript)) {
            $transcript = new oval\Models\Transcript;
        }
        $transcript->video_id = $video_id;
        $transcript->transcript = $text;
        $transcript->save();

        Storage::delete($path);

		return redirect('video-management');
	}
}
