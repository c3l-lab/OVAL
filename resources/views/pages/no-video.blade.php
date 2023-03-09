@extends('layouts.app')

@section('title', 'Not in Group')


@section('content')
	<div class="container">
		<div class="page-title"><i class="fa fa-exclamation-circle"></i>Error</div>

		<div class="text-content no-video-found">
			<h3>There is no video for the ID you selected.</h3>
		</div><!-- .text-content" -->
	</div>
@endsection

@section('javascript')

	<script type="text/javascript">
		$("#menu-button").click();
	</script>
@endsection