@extends('layouts.app')

@section('title', 'Detailed Analytics on Points')


@section('content')
<div class="container-fluid">
	<div class="page-title"><i class="fa fa-list-alt" aria-hidden="true"></i>
ANALYTICS ON POINTS</div>
	
	<div class="admin-page-section-header">
		<h2>{{$group_video->video()->title}}</h2>
	</div><!-- admin-page-section-header -->
	<div class="admin-page-section">
		<div class="row space-bottom">
			<div class="col-xs-4">
				<img class="medium-img" src="{{$group_video->video()->thumbnail_url}}">
			</div><!-- col-xs-2 -->
			<div class="col-xs-8">
				<h4>Points for this video</h4>
				<ol>
				@foreach ($group_video->points as $p)
					<li>{{$p->description}}</li>
				@endforeach
				</ol>
			</div><!-- col-xs-10 -->
		</div><!-- row -->
		
		<table class="table table-striped">
			<thead>
				<tr style="border-bottom:1px solid black">
					<th>Point Description</th>
					<th>Number of users included this point in summary</th>
				</tr>
			</thead>
			<tbody>	
				@foreach ($group_video->points as $p)
				<tr>
					<td>{{$p->description}}</td>
					<td>{{$p->numYes()}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div><!-- admin-page-section -->
</div><!-- container-fluid -->
@endsection

