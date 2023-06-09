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
			@if ($has_quiz)
			<div class="alert alert-warning alert-dismissible fade in space-top" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
				<i class="fa fa-exclamation-circle"></i>&nbsp;
				If the video pauses while viewing in full screen, it is due to a quiz question appearing that you are asked to answer. 
				When this happens, please click the <strong>Esc</strong> key to come out of full screen mode to see the quiz question.
			</div>
			@endif
    		<div id="left-side" class="col-md-8">
    			<div id="video" class="video-width">
    				<div id="player"></div>
    			</div><!-- .video -->
				
				<div class="video-width">
					<ul class="nav nav-tabs content-tabs" role="tablist">
						<li role="presentation" class="active col-xs-6"><a href="#annotations" aria-controls="annotations" role="tab" data-toggle="tab">ANNOTATIONS</a></li>
						@if (($group_video->show_analysis == true) && (count($video->keywords)>0))
						<li role="presentation" class="col-xs-6"><a href="#content-analysis" aria-controls="current topics" role="tab" data-toggle="tab">CURRENT TOPICS</a></li>
						@endif
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="annotations">
							<div class="annotations-buttons">
								<button type="button" class="btn btn-link download-comments" title="Download annotations and comments">
									Download
									<i class="fa fa-download"></i>
								</button>
								<button type="button" class="btn btn-link add-annotation" title="Add an annotation">
									New Annotation
									<i class="fa fa-plus-circle"></i>
								</button>
							</div><!-- .annotations-buttons -->

							<div class="">
								<canvas id="trends"></canvas>
								<div id="annotations-list"></div>
							</div><!-- .horizontal-scroll -->

							<div id="annotation-filter" data-toggle="buttons">
								<label class="btn active" title="Show all Annotations">
									<input type="radio" name="filter" value="4" checked><i class="fa fa-eye"></i>All
								</label>
								<label class="btn" title="Show only Annotations by me">
									<input type="radio" name="filter" value="1"><i class="fa fa-dot-circle-o"></i>Mine
								</label>
								<label class="btn" title="Show only Annotations by Student">
									<input type="radio" name="filter" value="3"><i class="fa fa-circle-o"></i>Students
								</label>
								<label class="btn" title="Show only Annotations by Instructors">
									<input type="radio" name="filter" value="2"><i class="fa fa-circle"></i>Instructors
								</label>
							</div><!-- #annotation-filter -->
						</div><!-- #annotations -->
						
						@if (($group_video->show_analysis == true) && (count($video->keywords)>0))
						<div role="tabpanel" class="tab-pane" id="content-analysis">
							<h4>Current topic</h4>
							<div class="panel panel-default">
								<div class="panel-body" id="current-keywords">
									&nbsp;
								</div><!-- panel-body -->
							</div><!-- panel -->
							<div class="content-analysis-body">
								<h4>List of topics covered in this video</h4>
								<div class="keyword-ul-box vertical-scroll" id="keyword-list">
									<ul id="keyword-ul">	
									</ul>
									<div class="no-keyword-msg"></div>
								</div><!-- keyword-ul-box -->
							</div><!-- content-analysis-body -->
						</div><!-- #content-analysis -->
						@endif
					</div><!-- tab-content -->
				</div>


    		</div><!-- left column .col-md-8 -->

			<div id="right-side" class="col-md-4">
				<ul class="nav nav-tabs content-tabs" role="tablist">

					@if (($group_video->show_analysis == true) && (count($video->keywords)>0))
					<li role="presentation" class="active col-xs-6"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab">GENERAL COMMENTS</a></li>
					<li role="presentation" class="col-xs-6"><a href="#related-videos" aria-controls="related-videos" role="tab" data-toggle="tab">RECOMMENDED RESOURCES</a></li>
					@else
					<li role="presentation" class="active"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab">GENERAL COMMENTS</a></li>
					@endif
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="comments">
						<div class="header">
							<button type="button" class="btn btn-link add-comment" title="Add a comment">
								New Comment	
								<i class="fa fa-plus-circle"></i>
							</button><!-- .add-comment -->
						</div><!-- .header -->
						<div class="comments-box vertical-scroll">
						</div><!-- .comments-box -->	
					</div><!-- #comments -->

					@if (($group_video->show_analysis == true) && (count($video->keywords)>0))
					<div role="tabpanel" class="tab-pane" id="related-videos">
						<div class="keyword-ul-box vertical-scroll" id="related-links">
							<form id="topic-search-form">
								<div class="input-group">
									<input type="search" id="topic-search-textbox" name="topic-search-textbox"  class="form-control dropdown-button" placeholder="Search for topic...">
									<span class="input-group-btn">
										<button id="topic-search-button" class="btn btn-default dropdown-button" type="button">
											<i class="fa fa-search" aria-hidden="true"></i>
										</button>
									</span>
								</div><!-- /input-group -->
								<!-- <input class="form-control gray-textbox" type="text" placeholder="Search topics..."> -->
							</form>
							<ul id="related-ul">
							</ul>
							<div id="no-links-msg">
							</div>
						</div><!-- keyword-ul-box -->
					</div><!-- #related-videos -->
				@endif
				</div><!-- tab-content -->
			</div><!-- #right-side -->
				

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
