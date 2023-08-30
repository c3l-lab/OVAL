@extends('layouts.app')

@section('title', 'Analytics')


@section('content')
    <div class="container-fluid">
        @if (empty($courses))
            <p>There are no courses associated with your account</p>
        @else
            <div class="page-title"><i class="fa fa-list-alt" aria-hidden="true"></i>
                VIDEO USAGE ANALYTICS</div>
            <div class="search-bar filter-bar container-fluid">
                <div class="row">
                    <div class="col-md-2 col-xs-12 admin-bar-text pull-left">Filter videos</div>

                    <div class="btn-group col-md-5 col-xs-12">
                        <a class="btn dropdown-button dropdown-left non-functional-button disabled" href="#">Course</a>
                        <a class="btn dropdown-button dropdown-center" data-toggle="dropdown" href="#"
                            id="course-name" href=""></a>
                        <a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="fa fa-caret-down"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($courses as $c)
                                <li><a
                                        href="{{ route('analytics.index', ['course_id' => $c->id]) }}">{{ $c->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div><!-- .btn-grp -->
                    <div class="btn-group col-md-5 col-xs-12 pull-left">
                        <a class="btn dropdown-button dropdown-left non-functional-button disabled" href="#">Group</a>
                        <a class="btn dropdown-button dropdown-center" data-toggle="dropdown" id="group-name"
                            href="#"></a>
                        <a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="fa fa-caret-down"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if (count($course->groups) == 0)
                                <li>NO GROUPS</li>
                            @else
                                @foreach ($course->groups as $g)
                                    <li><a
                                            href="{{ route('analytics.index', ['course_id' => $course->id, 'group_id' => $g->id]) }}">{{ $g->name }}</a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div><!-- .btn-group -->

                </div><!-- .row -->
            </div><!-- .search-bar -->

            <div class="admin-page-section">
                <table class="table table-striped">
                    <thead>
                        <tr style="border-bottom:1px solid var(--table-border-color)">
                            <th>ID</th>
                            <th>Video</th>
                            <th>Student Views</th>
                            <th>Annotations</th>
                            <th>Comments</th>
                            <th>Key Points</th>
                            <!-- <th>User Actions</th> -->
                            <th>Quiz Questions</th>
                            <th>Tracking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group->group_videos() as $gv)
                            <tr>
                                <td>
                                    {{ $gv->id }}
                                </td>
                                <td class="img-cel">
                                    <a href="/group_videos/{{ $gv->id }}">
                                        <img class="video-thumbnail" src="{{ $gv->video()->thumbnail_url }}">
                                        <br />
                                        {{ $gv->video()->title }}
                                    </a>
                                </td>
                                <td>
                                    Views: {{ $gv->numViews() }}<br />

                                    <span class="tooltip_class"
                                        data-tt="Course Enrolment : Number of unique viewers as a percentage ">Course
                                        engagement: {{ $gv->percentageUsersViewed() }}%</span><br />

                                    <button class="student_views analytics_btn" userlist={{ $gv->memberList() }}
                                        groupvideoid={{ $gv->id }}> View Details <i class="fa fa-info-circle"
                                            aria-hidden="true"></i></button><br />
                                </td>
                                <td>
                                    Total: {{ count($gv->annotations) }}<br />

                                    Average per user: {{ ceil($gv->aveAnnotationsPerUser()) }}<br />

                                    <button class='annotations_column analytics_btn' userlist={{ $gv->memberList() }}
                                        groupvideoid={{ $gv->id }}> View Details <i class="fa fa-info-circle"
                                            aria-hidden="true"></i></button><br />

                                </td>
                                <td>
																	Total: {{ count($gv->comments ) }}<br />

																	Average per user: {{ ceil($gv->aveCommentsPerUser()) }}<br />

																	<button
																		class='comment_column analytics_btn'
																		userlist={{ $gv->memberList() }}
																		groupvideoid={{ $gv->id }}
																	>
																			View Details <i class="fa fa-info-circle" aria-hidden="true"></i>
																	</button>
                                </td>
                                <td>
                                    @if (count($gv->points) != 0)
                                        Total: {{ count($gv->points) }}<br /><br />
                                    @else
                                        N/A<br /><br />
                                    @endif

                                    <button class='key_point analytics_btn' userlist={{ $gv->memberList() }}
                                        groupvideoid={{ $gv->id }}> View Details <i class="fa fa-info-circle"
                                            aria-hidden="true"></i></button><br />
                                </td>
                                <td>

                                    <br /><br />

                                    <button class='quiz_question analytics_btn' data-userlist="{{ $gv->memberList() }}"
                                        data-groupvideoid="{{ $gv->id }}">
                                        View Details <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                    <br />

                                </td>
                                <td>
                                    <br /><br />
                                    <a href="{{ route('trackings.export', ['group_video_id' => $gv->id]) }}">
                                        <button class="analytics_btn">Download</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- .admin-page-section -->


        @endif
    </div><!-- container-fluid -->
@endsection


@section('javascript')
    <!-- <script type="text/javascript" src="{{ asset('js/plugin/d3.min.js') }}"></script> -->
    <script type="text/javascript" src="{{ asset('js/plugin/jquery.tooltip.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/analytics.js') }}"></script>
@endsection


@section('modal')
    @include('parts.analytics_modal')
@endsection
