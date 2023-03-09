@extends('layouts.app')

@section('title', '{{ $title }}')


@section('content')
	<div class="container">
		<div class="page-title"><i class="fa fa-exclamation-circle"></i>{{ $title }}</div>
			
		<div class="text-content">
			<h3>{{ $title }}</h3>
            <p class="error-message">{{ $message }}</p>
		</div><!-- .text-content" -->
	</div>
@endsection


@section('javascript')

	<script type="text/javascript">
		$("#menu-button").click();
	</script>
@endsection