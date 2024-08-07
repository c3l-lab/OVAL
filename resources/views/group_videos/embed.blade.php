@extends('layouts.raw')

@section('additional-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/multi-select.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/player.css') }}">
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
@include('group_videos.show._request_gaze')

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/home.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    {{-- <script type="text/javascript" src="{{ asset('js/player.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('js/vidstack.js') }}"></script>
    @if ($group_video->enable_eye_tracking)
        <script src="{{ asset('js/plugin/webgazer.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/eye-tracking.js') }}"></script>
    @endif
    @if (count($video->keywords) > 0)
        <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
    @endif
@endsection


@section('modal')
    @include('parts.home-modal')
@endsection
