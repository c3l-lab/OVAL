@extends('layouts.app')

@section('title', '')

@section('additional-css')
<link rel="stylesheet" type="text/css" href="{{ URL::secureAsset('css/multi-select.css') }}">
@if (count($video->keywords)>0)
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css">
@endif
@endsection

@section ('selection_bar')
	@include('parts.selectionbar')
@endsection

	@section('content')
		<div class="container group-video">
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
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	<script type="text/javascript" src="{{URL::secureAsset('js/media/youtube-functions.js')}}"></script>
	@if (count($video->keywords)>0)
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
	@endif
@endsection


@section('modal')
	@include('parts.home-modal')
@endsection
