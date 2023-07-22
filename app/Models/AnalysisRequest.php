<?php

namespace oval\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model class for table 'analysis_requests'
 */
class AnalysisRequest extends Model
{
    protected $tablename = "analysis_requests";

    /**
    *   One-to-Many relationship (inverse)
    *   @return Video object
    **/
    public function video() {
        return $this->belongsTo('oval\Models\Video');
    }

    /**
    *   One-to-Many relationship (inverse) - The user that requested analysis
    *   @return User object
    **/
    public function user() {
        return $this->belongsTo('oval\Models\User');
    }

    /**
    *   Method to obtain number of requests made for the same video as this request.
    *
    *   Used for data display in admin page
    *   @return int
    **/
    public function numberOfReqForSameVideo() {
        $num = AnalysisRequest::where('video_id', '=', $this->video_id)->count();
        return $num;
    }

    /**
    *   Method to return full names of people that requested analysis for the same video
    *
    *   Used for data display in admin page
    *   @return array of string
    **/
    public function allRequestorsNames() {
        $reqs_for_same = AnalysisRequest::distinct('user_id')->where('video_id', '=', $this->video_id)->get();
        $names = [];
        foreach ($reqs_for_same as $r) {
            $names[] = $r->user->fullName();
        }
        return $names;
    }

    /**
     * Method to fetch AnalysisRequests for same video
     * @return collection Collection of AnalysisRequest objects
     */
    public function requestsForSameVideo() {
        return AnalysisRequest::where('video_id', '=', $this->video_id)->get();
    }

    /**
     * Method to get ids of users who requested analysis for the video
     * @return array Array of int
     */
    public function requestorsIds() {
        $reqs = $this->requestsForSameVideo();
        $ids = [];
        foreach($reqs as $r) {
            if(!empty($r->user)) {
                $ids[] = $r->user->id;
            }
        }
        return $ids;
    }
}
