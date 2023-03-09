@extends('layouts.app')

@section('title', 'OVAL - manage link')

@section('additional-css')
@if (count($group_videos)>0)
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css">
@endif
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fa fa-video-camera" aria-hidden="true"></i>
        MANAGE LINK FROM MOODLE
    </div>

   
    @if ($group_videos->contains('moodle_resource_id', $link_id))
    <div class="msg">
        This is linked to video "{{$group_videos->where('moodle_resource_id', $link_id)->first()->video()->title}}"
    </div>
    @endif
    

    <div class="admin-page-section-header">
        <h2>SELECT VIDEO</h2>
    </div><!-- admin-page-section-header -->

{{--    <div class="search-bar filter-bar container-fluid">
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <form id="topic-search-form">
                    <div class="input-group">
                        <input type="search" id="video-search-textbox" name="video-search-textbox" class="form-control dropdown-button" placeholder="Search for video...">
                        <span class="input-group-btn">
                            <button id="video-search-button" class="btn btn-default dropdown-button" type="button">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </span>
                    </div><!-- /input-group -->
                </form>
            </div><!-- col -->
        </div><!-- row -->
    </div><!-- search-bar -->
--}}

    <div class="admin-page-section">
    @if (count($group_videos) == 0)
        There are no videos added for this group yet.<br />
        Please add videos on <a href="/video-management" class="btn btn-link">Video Management page.</a>
    @else
        <div class="table-responsive">
            <table class="table table-striped" id="videos-table">
                <thead>
                    <th>SELECT</th>
                    <th>TITLE</th>
                    <th>DESCRIPTION</th>
                    <th>DURATION</th>
                </thead>
                <tbody>
                    @foreach ($group_videos as $gv)
                    <tr>
                        <td>
                            <div class="radio text-center">
                                <label>
                                    <input type="radio" name="select-radio" id="select-radio-{{$gv->id}}" value="{{$gv->id}}" aria-label="Select">
                                </label>
                            </div>
                        </td>
                        <td>
                            <a href="/view/{{$gv->id}}">
                                <img src="{{ $gv->video()->thumbnail_url }}" class="video-thumbnail">
                                {{ $gv->video()->title }}
                            </a>
                        </td>
                        <td>{{ $gv->video()->description }}</td>
                        <td>{{ $gv->video()->formattedDuration() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
    @endif
    </div><!-- admin-page-section -->




</div><!-- container-fluid -->
@endsection



@section('javascript')

<script type="text/javascript" src="{{ URL::secureAsset('js/admin-select-video.js') }}"></script>
@if (count($group_videos)>0)
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
@endif
@endsection