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


		<div class="container">
			@if (($user->isInstructorOf($course)) && ($group_video->hide == 1))
			<div class="msg">
				THIS VIDEO IS SET TO "HIDDEN" AND IS NOT VISIBLE TO STUDENTS.<br />
				Set it to "visible" in <a href="/video-management">Video Management page</a> to make it available for student use.
			</div>
			@endif
    		<div id="left-side" class="col-md-11 mx-auto">
    			<div id="video" class="video-width">
    				<div id="player"></div>
    			</div><!-- .video -->
				
				<div class="video-width">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="annotations">
							<div class="annotations-buttons">
								<button type="button" class="btn btn-link add-annotation" title="Add an annotation">
									New Thought Report
									<i class="fa fa-plus-circle"></i>
								</button>
							</div><!-- .annotations-buttons -->
						</div><!-- #annotations -->
					</div><!-- tab-content -->
				</div><!-- video-width -->
    		</div><!-- left column .col-md-11 -->	
		</div><!-- .container (main content area) -->
@endsection


@section('javascript')

	<script type="text/javascript" src="{{ URL::secureAsset('js/home.js') }}"></script>
	<script type="text/javascript" src="{{ URL::secureAsset('js/jquery.multi-select.js') }}"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	@if ($video->media_type == "youtube")
	<script type="text/javascript" src="{{URL::secureAsset('js/media/youtube-functions.js')}}"></script>
	@elseif ($video->media_type == "helix")
	<script type="text/javascript" src="{{URL::secureAsset('js/media/jwplayer-functions.js')}}"></script>
	@endif
	@if (count($video->keywords)>0)
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
	@endif
@endsection


@section('modal')
	@include('parts.home-modal')
@endsection
