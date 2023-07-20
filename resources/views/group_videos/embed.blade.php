@extends('layouts.raw')

@section('additional-css')
    <link rel="stylesheet" type="text/css" href="{{ URL::secureAsset('css/multi-select.css') }}">
    @if (count($video->keywords) > 0)
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css">
    @endif
    <style>
        .links {
            padding: 0 8px
        }

        #left-side {
            padding: 0px;
        }

        .group-video {
            padding: 0 8px;
        }
    </style>
@endsection

@section('content')
    @if ($user->isInstructorOf($course))
        <div class="links">
            <a href="/video-management" target="_blank">Video Management</a>
        </div>
    @endif

    <div class="group-video">
        @include('group_videos.show._group_video', [
            'user' => $user,
            'course' => $course,
            'group_video' => $group_video,
            'has_quiz' => $has_quiz,
            'video' => $video,
        ])
    </div><!-- .container (main content area) -->
@endsection


@section('javascript')
    <script type="text/javascript" src="{{ URL::secureAsset('js/home.js') }}"></script>
    <script type="text/javascript" src="{{ URL::secureAsset('js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    <script type="text/javascript" src="{{ URL::secureAsset('js/media/youtube-functions.js') }}"></script>
    @if (count($video->keywords) > 0)
        <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
    @endif
@endsection


@section('modal')
    @include('parts.home-modal')
@endsection
