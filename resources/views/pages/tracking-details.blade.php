@extends('layouts.app')

@section('title', 'Detailed Analytics of User Actions')


@section('content')
<div class="container-fluid">
	<div class="page-title"><i class="fa fa-list-alt" aria-hidden="true"></i>
ANALYTICS ON USER ACTIONS</div>
	
	<div class="admin-page-section-header">
		<h2>{{$group_video->video()->title}}</h2>
	</div><!-- admin-page-section-header -->
	<div class="admin-page-section">
		<div class="row space-bottom">
			<div class="col-xs-12">
				<img class="medium-img" src="{{$group_video->video()->thumbnail_url}}">
			</div><!-- col-xs-12 -->
		</div><!-- row -->
		
		<div class="table-responsive">
		<table class="table table-striped">
			<tbody>	
				<tr>
					<td>Number of views</td>
					<td>{{$group_video->numViews()}}</td>
				</tr>
				<tr>
					<td>Number of unique users who viewed</td>
					<td>{{$group_video->numUniqueViews()}}</td>
				</tr>
				<tr>
					<td>Average views per user</td>
					@if ($group_video->numUniqueViews() == 0)
					<td>0</td>
					@else
					<td>{{round($group_video->numViews()/(float)$group_video->numUniqueViews())}}</td>
					@endif
				</tr>
				<tr>
					<td>Number of clicks on "Download Annotations"</td>
					<td>{{$group_video->numAnnotationDownloads()}}</td>
				</tr>
				<tr>
					<td>Number of times an Annotation was viewed</td>
					<td>{{$group_video->numTimesAnnotationViewed()}}</td>
				</tr>
				
			</tbody>
		</table>
		</div><!-- table-responsive-->
	</div><!-- admin-page-section -->
</div><!-- container-fluid -->
@endsection

