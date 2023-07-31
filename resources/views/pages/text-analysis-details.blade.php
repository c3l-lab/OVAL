@extends('layouts.app')

@section('title', 'Text Analysis Detail')

@section('content')

<div class="container-fluid">
    <div class="page-title">
            <a href="{{ route("group_videos.index", ["course_id" => $course_id, "group_id" => $group_id]) }}#assigned">
				<i class="fa fa-laptop" aria-hidden="true"></i>
				VIDEO MANAGEMENT
            </a>
            &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            TEXT ANALYSIS DETAILS
        </div>
    <div class="admin-page-info-section">
        <a href="/group_videos/{{$course_id}}/{{$group_id}}/{{$video->id}}">
        	<img class="video-thumbnail" src="{{$video->thumbnail_url}}">
        </a>
        <h4 class="inline-with-img">
        	<a href="/group_videos/{{$course_id}}/{{$group_id}}/{{$video->id}}">
        		{{$video->title}}
        	</a>
        </h4>

    </div><!-- admin-page-section -->

    <div class="admin-page-section-header">
        <h2>KEYWORDS</h2>
    </div><!-- admin-page-section-header -->
    <div class="admin-page-section">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>TEXT</th>
                        <th>RELEVANCE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysis['keywords'] as $keyword)
                        <tr>
                            <td>{{$keyword['text']}}</td>
                            <td>{{$keyword['relevance']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
    </div><!-- admin-page-section -->


    <div class="admin-page-section-header">
        <h2>ENTITIES</h2>
    </div><!-- admin-page-section-header -->
    <div class="admin-page-section">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>TYPE</th>
                        <th>TEXT</th>
                        <th>RELEVANCE</th>
                        <th>COUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysis['entities'] as $entity)
                        <tr>
                            <td>{{$entity['type']}}</td>
                            <td>{{$entity['text']}}</td>
                            <td>{{$entity['relevance']}}</td>
                            <td>{{$entity['count']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
    </div><!-- admin-page-section -->


    <div class="admin-page-section-header">
        <h2>CONCEPTS</h2>
    </div><!-- admin-page-section-header -->
    <div class="admin-page-section">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>TEXT</th>
                        <th>RELEVANCE</th>
                        <th>RESOURCE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysis['concepts'] as $concept)
                        <tr>
                            <td>{{$concept['text']}}</td>
                            <td>{{$concept['relevance']}}</td>
                            <td><a href="{{$concept['dbpedia_resource']}}" target="_blank">reference</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
    </div><!-- admin-page-section -->


    <div class="admin-page-section-header">
        <h2>CATEGORIES</h2>
    </div><!-- admin-page-section-header -->
    <div class="admin-page-section">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>SCORE</th>
                        <th>LABEL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($analysis['categories'] as $category)
                        <tr>
                            <td>{{$category['score']}}</td>
                            <td>{{$category['label']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
    </div><!-- admin-page-section -->

</div><!-- container-fluid -->
@endsection
