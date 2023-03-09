@extends('layouts.app')

@section('title', 'Not Authorised')


@section('content')
	<div class="container-fluid">
		<div class="page-title"><i class="fa fa-ban"></i>Not Authorised</div>
			
		<div class="text-content">
			<p>This page is for OVAL Administrator only.</p>
		</div><!-- .text-content" -->
	</div>
@endsection


@section('javascript')

	<script type="text/javascript">
		$("#menu-button").click();
	</script>
@endsection