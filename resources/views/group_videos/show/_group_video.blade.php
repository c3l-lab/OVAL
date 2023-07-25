@yield('content')

@if ($user->isInstructorOf($course) && $group_video->hide == 1)
    <div class="msg">
        THIS VIDEO IS SET TO "HIDDEN" AND IS NOT VISIBLE TO STUDENTS.<br />
        Set it to "visible" in <a href="/video-management">Video Management page</a> to make it available for student
        use.
    </div>
@endif

@if (false)
    @if ($has_quiz)
        <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">×</span></button>
            <i class="fa fa-exclamation-circle"></i>&nbsp;
            If the video pauses while viewing in full screen, it is due to a quiz question appearing that you are asked
            to answer.
            When this happens, please click the <strong>Esc</strong> key to come out of full screen mode to see the quiz
            question.
        </div>
    @endif
@endif

<div id="left-side" class="{{ $group_video->show_comments ? "col-md-8" : "col-md-12" }}">
    <div id="video" class="video-width">
        <div id="player"></div>
    </div><!-- .video -->

    <div class="video-width">
    @if ($group_video->show_annotations)
        @include('group_videos.show._annotations', [
            'group_video' => $group_video,
            'video' => $video,
        ])
    @else
        @include('group_videos.show._annotation_buttons', [
            'group_video' => $group_video,
        ])
    @endif
    </div>

</div><!-- left column .col-md-8 -->

@if ($group_video->show_comments)
    @include('group_videos.show._right_side', [
        'group_video' => $group_video,
        'video' => $video,
    ])
@endif
