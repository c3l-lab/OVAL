<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'keywords'
 * 
 * The table is populated based on values in 'transcripts' table's 'analysis' column 
 * and the time values in json value of 'transcripts' table's 'transcript' column.
 */
class Keyword extends Model
{
    protected $table = "keywords";
    public $timestamps = false;

    /**
    *   One-to-Many relationship (Inverse)
    *   @return Video object 
    **/
    public function video() {
        return $this->belongsTo('oval\Video', 'videoId');
    }

    /**
     * Method to get related keywords.
     * 
     * It returns array of keywords with video url, video url for when the keyword appears, and video time.
     * 
     * @return array Array of array with keys: title, url, time_url, time
     */
    public function related() {
        $related = Keyword::where([
            ['keyword', '=', $this->keyword],
            ['type', '=', $this->type],
            ['videoId', '<>', $this->videoId],
            ['id', '<>', $this->id]
        ])->get();
        $list = null;
        if (!empty($related)) {
            foreach ($related as $r) {
                $video = Video::find($r->videoId);
                $url = "";
                if($video->media_type == "helix") {
                	$url = $video->video_url()."#t=".floor($r->startTime);
                }
                else {
                	$url = $video->video_url()."?start=".floor($r->startTime);
                }
                $list[] = ['title'=>$video->title,'url'=>$video->video_url(), 'time_url'=>$url, 'time'=>intval(floor($r->startTime))];
            }
        }
        return $list;
    }

    /**
     * Method to get time values when the keyword appears in video.
     * @return array Array
     */
    public function occurrences() {
        $occurrences = Keyword::where([
            ['keyword', '=', $this->keyword],
            ['type', '=', $this->type],
            ['videoId', '=', $this->videoId],
        ])->get();
        $list = null;
        if(!empty($occurrences)) {
            foreach ($occurrences as $o) {
                $list[] = intval(floor($o->startTime));
            }
        }
        return $list;
    }
}
