@extends('layouts.app')

@section('title', 'Video Management')


@section('additional-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/multi-select.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-tagsinput.css') }}">
@endsection

@section('content')

    <div class="container-fluid">
        <div class="page-title"><i class="fa fa-video-camera"></i>VIDEO MANAGEMENT</div>

        <div class="msg"></div>

        <div class="admin-page-section-header">
            <h2>ADD VIDEO</h2>
        </div><!-- .admin-page-section-header -->

        <div class="admin-page-section">
            <form id="add-video-form" name="add-video-form" action="" method="POST" role="form"
                data-toggle="validator">
                <fieldset class="form-group">
                    <legend>YouTube URL</legend>
                    <input class="form-control gray-textbox" type="url" id="video-url" required>
                    <div class="instruction">Please enter full URL of the YouTube video</div>
                    <div class="help-block with-errors"></div>
                </fieldset>

                <fieldset class="form-group">
                    <legend>Group</legend>
                    <span>Assign this video to the default group of</span>
                    <select id="course-to-assign" name="course-to-assign">
                        @foreach ($user->coursesTeaching() as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>?
                    <div class="radio-inline left-indent">
                        <label for="yes"><input type="radio" name="group-radio" id="yes" value="true"
                                checked>Yes</label>
                    </div><!-- .radio-inline -->
                    <div class="radio-inline left-indent">
                        <label><input type="radio" name="group-radio" id="no" value="false">No</label>
                    </div><!-- .radio-inline -->
                </fieldset>

                <fieldset class="form-group" id="points">
                    <legend>Key Points</legend>
                    <div>
                        <span>Do you want to add points to your video?</span>
                        <div class="radio-inline">
                            <label><input type="radio" name="points-radio" id="yes" value="true">Yes</label>
                        </div><!-- .radio-inline -->
                        <div class="radio-inline">
                            <label><input type="radio" name="points-radio" id="no" value="false">No</label>
                        </div><!-- .radio-inline -->
                    </div>
                    <div id="points-controls">
                        <fieldset class="space-bottom">
                            <input id="point-instruction-textbox" class="form-control gray-textbox" type="text"
                                placeholder="Instruction for students here...">
                        </fieldset>
                        <fieldset id="points-inputs">
                            <div class="row">
                                <div class="col-xs-10">
                                    <input class="form-control gray-textbox" type="text"
                                        placeholder="Point text here...">
                                </div>
                                <div class="col-xs-2">
                                    <button type="button" class="delete-point outline-button btn-sm full-width"
                                        title="delete"><span class="hidden-xs">Delete</span><i
                                            class="fa fa-minus-circle"></i></button>
                                </div>
                            </div><!-- row -->
                        </fieldset>
                        <button type="button" id="another-point-button" class="outline-button btn-sm"
                            title="Add another point">Add another point<i class="fa fa-plus-circle"></i></button>
                    </div><!-- points-controls -->
                </fieldset>

                <fieldset class="form-group" id="text-analysis">
                    <legend>Content Analysis</legend>
                    <div class="form-inline">
                        <span class="space-right">Request content analysis?</span>
                        <div class="radio-inline">
                            <label><input type="radio" name="analysis-radio" id="request-analysis"
                                    value="request-analysis">Yes</label>
                        </div><!-- .radio-inline -->
                        <div class="radio-inline">
                            <label><input type="radio" name="analysis-radio" id="no-analysis" value="no-analysis"
                                    checked>No</label>
                        </div><!-- .radio-inline -->
                    </div>
                </fieldset>

                <button type="button" id="add-video-button" class="rectangle-button" title="Save">SAVE<i
                        class="fa fa-video-camera"></i></button>
                <div id="loading-hud">
                    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </form><!-- #add-video-form -->
        </div><!-- .admin-page-section -->

        @if (count($videos_without_group) > 0)
            <div class="admin-page-section-header">
                <a id="unassigned"></a>
                <h2 id="unassigned-videos">VIDEOS UNASSIGNED TO GROUPS</h2>
            </div><!-- admin-page-section-header -->
            <div class="admin-page-section">
                <div class="table-responsive">
                    <table class="table table-striped" name="unassiged">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TITLE</th>
                                <th>DESCRIPTION</th>
                                <th>DURATION</th>
                                <th>GROUP ACCESS</th>
                                <!-- <th>SET QUIZ</th> -->
                                <th>DATE UPLOADED</th>
                                <th>DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($videos_without_group as $video)
                                <tr>
                                    <td>{{ $video->id }}</td>
                                    <td class="img-cel">
                                        <img class="video-thumbnail" src="{{ $video->thumbnail_url }}">
                                        <br />
                                        {{ $video->title }}
                                    </td>
                                    <td>{{ $video->description }}</td>
                                    <td>{{ $video->formattedDuration() }}</td>
                                    <td><button type="button" id="{{ $video->id }}"
                                            class="btn btn-link assign-grp-button" data-toggle="modal"
                                            data-target="#modal-form" data-id="{{ $video->id }}" data-type="assign"
                                            title="Assign to group"><i class="fa fa-user-plus group-icon"></i></button>
                                    </td>
                                    <!-- <td><button type="button" videoid="{{ $video->id }}" class="btn btn-link assign-grp-button" title="Set quiz"><i class="fa fa-comment-o"></i></button></td> -->
                                    <td>{{ $video->created_at }}</td>
                                    <td>
                                        <button hx-delete="{{ route('videos.destroy', ['video' => $video]) }}"
                                            hx-confirm="Are you sure you want to delete?" hx-target="closest tr"
                                            hx-swap="delete" class="btn btn-link delete-button" title="Delete">
                                            <i class="fa fa-trash-o delete-icon"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- table-responsive -->
            </div><!-- admin-page-section (Unassigned Videos) -->
        @endif

        <div class="admin-page-section-header">
            <h2>UPLOADED VIDEOS</h2>
        </div><!-- admin-page-section-header -->

        <div class="search-bar filter-bar container-fluid">
            <div class="row">
                <div class="col-md-2 col-xs-12 admin-bar-text pull-left white-text">Filter videos</div>
                <a id="filter_controls"></a>

                <form id="filter-by-class" name="filter-by-class" method="POST" action="">
                    <div class="btn-group col-md-5 col-xs-12">
                        <a class="btn dropdown-button dropdown-left" href="#">Course</a>
                        <a class="btn dropdown-button dropdown-center" data-toggle="dropdown" href="#"
                            id="course-name"></a>
                        <a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="fa fa-caret-down"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if ($user->enrolledCourses->count() == 0)
                                <li>NO COURSES</li>
                            @else
                                @foreach ($user->coursesTeaching() as $c)
                                    <li><a
                                            href="{{ route('group_videos.index', ['course_id' => $c->id]) }}">{{ $c->name }}</a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div><!-- btn-group -->
                </form><!-- filter-by-class -->

                <form id="filter-by-group" method="POST" action="">
                    <div class="btn-group col-md-5 col-xs-12">
                        <a class="btn dropdown-button dropdown-left" href="#">Group</a>
                        <a class="btn dropdown-button dropdown-center" data-toggle="dropdown" href="#"
                            id="group-name"></a>
                        <a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="fa fa-caret-down"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if ($user->groupMemberOf->count() == 0)
                                <li>NO GROUPS</li>
                            @elseif ($course)
                                @foreach ($user->groupMemberOf->where('course_id', $course->id) as $g)
                                    <li>
                                        <a
                                            href="{{ route('group_videos.index', ['course_id' => $course->id, 'group_id' => $g->id]) }}">{{ $g->name }}</a>
                                    </li>
                                @endforeach
                            @else
                                <li>Select A Course</li>
                            @endif
                        </ul>
                    </div><!-- btn-group -->
                </form><!-- filter-by-group -->

            </div><!-- .row -->
        </div><!-- .search-bar (filter bar) -->


        @if (count($group_videos) == 0)
            <div class="admin-page-section">
                No Videos in this Course and Group
            </div>
        @else
            <a id="assigned"></a>
            <div class="admin-page-section">
                <table class="table table-striped" name="assiged">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TITLE</th>
                            <th>DESCRIPTION</th>
                            <th>ANNOTATION CONFIG</th>
                            <th>PLAYER CONTROLS</th>
                            <th>VIDEO ACCESS CONTROL</th>
                            <th>CONTENT ANALYSIS</th>
                            <th>DURATION</th>
                            <th>GROUP ACCESS</th>
                            <th>KEY POINTS</th>
                            <th>EYE TRACKING</th>
                            <th>SET QUIZ</th>
                            <th>COMMENTS</th>
                            <th>DATE UPLOADED</th>
                            <th>DELETE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group_videos as $gv)
                            <tr id="v-{{ $gv->video()->id }}">
                                <td>{{ $gv->id }}</td>
                                <td class="img-cel">
                                    <a href="/group_videos/{{ $gv->id }}">
                                        <img class="video-thumbnail" src="{{ $gv->video()->thumbnail_url }}">
                                        <br />
                                        {{ $gv->video()->title }}
                                    </a>
                                </td>
                                <td>{{ $gv->video()->description }}</td>
                                <td>
                                    <button type="button" class="btn btn-link annotation-config-button"
                                        title="Annotation setting" data-group-video-id="{{ $gv->id }}"
                                        data-show-annotations="{{ $gv->show_annotations }}"
                                        data-downloadable="{{ data_get($gv->annotation_config, 'downloadable', true) }}"
                                        data-is-show-annotation-button="{{ data_get($gv->annotation_config, 'is_show_annotation_button', true) }}"
                                        data-enable-structured-annotation-quiz="{{ data_get($gv->annotation_config, 'enable_structured_annotation_quiz', true) }}"
                                        data-label="{{ data_get($gv->annotation_config, 'label', 'New Annotation') }}"
                                        data-header-name="{{ data_get($gv->annotation_config, 'header_name', 'ADD ANNOTATION') }}">
                                        <i class="fa fa-pencil-square-o group-icon"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link"
                                        hx-get="{{ route('group_videos.controls.edit', ['group_video' => $gv]) }}"
                                        hx-target="#controls-setting-modal .modal-body" hx-swap="innerHTML"
                                        hx-on="htmx:afterOnLoad: $('#controls-setting-modal').modal('show');"
                                        title="Controls setting">
                                        <i class="fa fa-pencil-square-o group-icon"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link visibility-button" data-toggle="modal"
                                        data-target="#visibility-modal" data-id="{{ $gv->id }}"
                                        data-hidden="{{ $gv->hide }}" title="Edit video visibility">
                                        @if ($gv->hide == true)
                                            Hidden
                                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                        @else
                                            Visible
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        @endif
                                    </button>
                                    <br />
                                    <button type="button" class="btn btn-link video-order-button" data-toggle="modal"
                                        data-target="#order-modal" data-id="{{ $gv->id }}" data-type=""
                                        title="Edit video order">
                                        Order:
                                        @if ($gv->order != 1000)
                                            {{ $gv->order }}
                                        @else
                                            default
                                        @endif
                                        &nbsp;
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link text-analysis-button" data-toggle="modal"
                                        data-target="#text-analysis-modal" data-id="{{ $gv->id }}"
                                        data-show="{{ $gv->show_analysis }}" title="Edit visibility of text analysis">
                                        @if ($gv->show_analysis == true)
                                            Visible
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        @else
                                            Hidden
                                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                        @endif
                                    </button>
                                    <br />

                                    @if ($gv->video()->keywords->count() > 0)
                                        <button type="button" class="btn btn-link text-analysis-button"
                                            data-toggle="modal" data-target="#edit-keywords-modal"
                                            data-vid="{{ $gv->video()->id }}"
                                            data-keywords="{{ $gv->video()->keywords_for_edits() }}"
                                            title="Edit keywords">
                                            <i class="fa fa-info-pencil" aria-hidden="true"></i>
                                            Edit
                                        </button>
                                        <br />
                                    @endif
                                </td>
                                <td>{{ $gv->video()->formattedDuration() }}</td>
                                <td>
                                    {{--								@if ($gv->allGroupsInCourseWithAccess($course->id)->count() > 0)
									@foreach ($gv->allGroupsInCourseWithAccess($course->id) as $g)
									<p>{{$g->name}}</p>
									@endforeach

--}}
                                    <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#modal-assigned-group-list" data-id="{{ $gv->video()->id }}"
                                        title="List assigned groups">
                                        Show&nbsp;<i class="fa fa-list-alt" aria-hidden="true"></i>
                                        <button type="button" class="btn btn-link group-mng-button" data-toggle="modal"
                                            data-target="#modal-form" data-id="{{ $gv->video()->id }}"
                                            data-type="manage" title="Assign to another group">
                                            Add&nbsp;<i class="fa fa-user-plus" aria-hidden="true"></i>
                                        </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link edit-points-button" data-toggle="modal"
                                        data-target="#modal-points-form" data-id="{{ $gv->video()->id }}"
                                        title="Manage points"><i class="fa fa-pencil-square-o group-icon"></i></button>
                                </td>
                                <td class="text-center">
                                    {{-- <button type="button" class="btn btn-link export-eye-tracking-btn"
                                        data-toggle="modal" data-target="#export-eye-tracking-data"
                                        data-id="{{ $gv->id }}" title="Download eye tracking data">
                                        <i class="fa fa-download"></i>
                                    </button>
                                    <br /> --}}
                                    <label class="switch" data-group-video-id="{{ $gv->id }}">
                                        <input type="checkbox" @checked($gv->enable_eye_tracking == 1)>
                                        <span class="toggle-eye-track-switch slider round"
                                            data-group-video-id={{ $gv->id }} class="btn btn-link"></span>
                                    </label>
                                </td>
                                <td>
                                    <button type="button" data-group-video-id={{ $gv->id }}
                                        class="set-quiz-btn quiz btn btn-link assign-grp-button" title="Set quiz">
                                        <i class="fa fa-comment-o"></i>
                                    </button>
                                    <br />
                                    <label class="switch quiz-switch">
                                        <input type="checkbox" @checked($gv->quiz && $gv->quiz->visable == 1)
                                            data-group-video-id="{{ $gv->id }}">
                                        <span class="slider round"></span>
                                        <br />
                                        <p>{{ $gv->quiz && $gv->quiz->visable == 1 ? 'visible' : 'hidden' }}</p>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label id="comments-switch" class="switch">
                                        <input type="checkbox" {{ $gv->show_comments ? 'checked' : '' }}
                                            data-url="{{ route('group_videos.toggle_comments', ['groupVideo' => $gv]) }}" />
                                        <span class="slider round"></span>
                                        <br />
                                        <p>visible</p>
                                    </label>
                                </td>
                                <td>{{ $gv->created_at }}</td>
                                <td>
                                    <button class="btn btn-link archive-button" data-id="{{ $gv->id }}"
                                        title="Delete">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
        @endif
    </div><!-- .admin-page-section (Videos by course and groups) -->







    </div><!-- container-fluid -->
@endsection

@section('modal')
    @include('group_videos.index._modals')
@endsection


@section('javascript')

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html5sortable/0.6.3/html.sortable.min.js">
    </script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>

    <script type="text/javascript" src="{{ asset('js/video-management.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/structured_annotation.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/plugin/oval_quiz_creation.js') }}"></script>
@endsection
