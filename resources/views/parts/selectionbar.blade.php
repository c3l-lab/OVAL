		<div class="courses-bar container-fluid">
			<div class="row">
				<div class="btn-group col-md-4 col-xs-12">
					<a class="btn dropdown-button dropdown-left non-functional-button disabled" href="#">Course</a>
					<a class="btn dropdown-button dropdown-center" id="course-name" href="#" data-toggle="dropdown"></a>
					<a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
					</a>
					<ul class="dropdown-menu">
					@if (count($user->enrolledCourses) == 0)
						<li>NO COURSES</li>
					@else
						@foreach ($user->enrolledCourses as $c)
						<li><a href="/course/{{$c->id}}">{{ $c->name }}</a></li>
						@endforeach
					@endif
				  </ul>
				</div><!-- .btn-group -->

				<div class="btn-group col-md-4 col-xs-12">
					<a class="btn dropdown-button dropdown-left non-functional-button disabled" href="#">Group</a>
					<a class="btn dropdown-button dropdown-center" id="group-name" href="#" data-toggle="dropdown"></a>
					<a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
					</a>
					<ul class="dropdown-menu">
					@if (count($user->groupMemberOf) == 0)
						<li>NO GROUPS</li>
					@else
						@foreach ($user->groupMemberOf->where('course_id', $course->id) as $g)
						<li><a href="/group/{{$g->id}}">{{ $g->name }}</a></li>
						@endforeach
					@endif
					</ul>
				</div><!-- .btn-group -->

				<div class="btn-group col-md-4 col-xs-12">
				  <a class="btn dropdown-button dropdown-left  non-functional-button disabled" href="#">Video</a>
				  <a class="btn dropdown-button dropdown-center" id="video-name" href="#" data-toggle="dropdown"></a>
				  <a class="btn dropdown-button dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
				  </a>
					<ul class="dropdown-menu">
					@if (count($user->viewableGroupVideos()) == 0)
						<li>NO VIDEOS</li> 
					@else
					
					@foreach ($group->availableGroupVideosForUser($user) as $gv)  
					<li><a href="/view/{{$gv->id}}">{{ $gv->video()->title }}</a></li>
					@endforeach
					@endif

				  </ul>
				</div><!-- .btn-group -->
			</div><!-- .row -->
		</div><!-- .courses-bar -->

		<!--div class="search-bar container-fluid"-->
			<!--form id="search-box"-->
				<!--div class="form-inline"-->
					<!--div class="input-group"-->
						<!--input id="search-term" class="form-control" type="text"-->
						<!--span class="input-group-btn"><button id="search-button" class="btn dropdown-button search-button" type="submit"--><!--i class="fa fa-search fa-fw"--><!--/i--><!--/button--><!--/span-->
					<!--/div--><!-- .search-box -->

					<!--input type="checkbox" id="content" name="content" value="true" checked="checked"--><!--label for="content"--><!--By Content--><!--/label-->
					<!--input type="checkbox" id="author" name="author" value="true"--><!--label for="author"--><!--Author--><!--/label11-->
					<!--input type="checkbox" id="tag" name="tag" value="true" checked="checked"--><!--label for="tag"--><!--Tag--><!--/label-->
					<!--input type="checkbox" id="auto" name="auto-search" value="true"--><!--label for="auto"--><!--Auto Search--><!--/label-->

				<!--/div--><!-- .form-group -->
			<!--/form--><!-- #search-form -->
		<!--/div--><!-- .search-bar -->