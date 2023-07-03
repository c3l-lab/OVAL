@extends('layouts.raw')

@section('additional-css')
    <link rel="stylesheet" type="text/css" href="{{ URL::secureAsset('css/multi-select.css') }}">
    @if (count($video->keywords) > 0)
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css">
    @endif
    <style>
      .links {
        margin-top: 16px;
      }
    </style>
@endsection

@section('content')
    @if ($user->isInstructorOf($course))
    <div class="container">
        <div class="links">
            <a href="/video-management" target="_blank">Video Management</a>
        </div>
    </div>
    @endif

    @include('group_videos.show._group_video', [
        'user' => $user,
        'course' => $course,
        'group_video' => $group_video,
        'has_quiz' => $has_quiz,
        'video' => $video,
    ])
@endsection


@section('javascript')

    <script type="text/javascript" src="{{ URL::secureAsset('js/home.js') }}"></script>
    <script type="text/javascript" src="{{ URL::secureAsset('js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    @if ($video->media_type == 'youtube')
        <script type="text/javascript" src="{{ URL::secureAsset('js/media/youtube-functions.js') }}"></script>
    @elseif ($video->media_type == 'helix')
        <script type="text/javascript" src="{{ URL::secureAsset('js/media/jwplayer-functions.js') }}"></script>
    @endif
    @if (count($video->keywords) > 0)
        <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
    @endif
@endsection


@section('modal')
    @include('parts.home-modal')
@endsection
