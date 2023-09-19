<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="modalLabel">ASSIGN VIDEO TO GROUP</h4>
				<div class="row">
					<div id="modal-video-title" class="col-xs-9"></div>
					<div class="col-xs-3">
						<img id="modal-video-thumbnail" src="" class="video-thumbnail">
					</div>
				</div><!-- row -->


			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">
				<form id="assign-video-to-group-form" role="form" data-toggle="validator">
					<div class="row">
						<div class="col-xs-12">
							<span class="semi-bold">Assign to:</span>
							<div id="assign-to-course" class="btn-group col-xs-12">
								<button type="button" class="btn dropdown-button dropdown-left">Course</button>
								<button type="button" class="btn dropdown-button dropdown-center" id="modal-course-name" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"></button>
								<button type="button" class="btn dropdown-button dropdown-toggle" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
									<span class="fa fa-caret-down"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" id="course-dropdown">
									@if (count($user->coursesTeaching())==0)
									<li>NO COURSES</li>
									@else
									@foreach ($user->coursesTeaching() as $c)
									<li id="{{$c->id}}">{{ $c->name }}</li>
									@endforeach
									@endif
								</ul><!-- .dropdown-menu -->
							</div><!-- btn-group -->
						</div><!-- col-xs-12 -->
					</div><!-- row -->

					<div class="row space-top">
						<div class="col-xs-12">
							<div class="col-xs-12 form-group">
								<label for="modal-group-list">Available groups</label>
								<select multiple id="modal-group-list" class="form-control" required>
								</select>
								<div class="help-block with-errors"></div>
								<div class="instruction"><strong>Note:</strong> Hold down ctrl to select multiple.</div>
							</div><!-- col-xs-12 -->
						</div><!-- colxs-12 -->
					</div><!-- row -->

					<div class="row space-top">
						<div class="col-xs-12">
							<span class="space-right">Would you like to copy existing contents?</span>
							<span class="">
								<label class="radio-inline">
									<input type="radio" name="copy-contents" id="copy-contents-yes" value="true">Yes
								</label>
								<label class="radio-inline">
									<input type="radio" name="copy-contents" id="copy-contents-no" value="false" checked>No
								</label>
							</span>
						</div><!-- col-xs-12 -->
						<div id="copy-from" class="col-xs-12 space-top-10">
							<div class="col-xs-8">
								<div class="row">
									<div class="col-xs-12 form-group">
										<label for="copy-from-course">Course</label>
										<select id="copy-from-course" class="form-control">
										</select>
									</div><!-- col-xs-12 -->
									<div class="col-xs-12 form-group">
										<label for="copy-from-group">Group</label>
										<select id="copy-from-group" class="form-control">
										</select>
									</div><!-- col-xs-12 -->
								</div><!-- row -->
							</div><!-- col-xs-8 -->
							<div class="col-xs-4" id="copy-options">
								<fieldset>
								<legend>Options:</legend>
								<div class="checkbox" id="copy-comment-instruction-checkbox" >
									<label>
										<input type="checkbox" value="" id="comment-instruction-cb">
										Comment Instruction
									</label>
								</div><!-- checkbox -->
								<div class="checkbox" id="copy-points-checkbox">
									<label>
										<input type="checkbox" value="" id="points-cb">
										Key Points
									</label>
								</div><!-- checkbox -->
								<div class="checkbox" id="copy-quiz-checkbox">
									<label>
										<input type="checkbox" value="" id="quiz-cb">
										Quiz
									</label>
								</div><!-- checkbox -->
								</fieldset>
							</div><!-- col-xs-4 -->
						</div><!-- #copy-from -->
					</div><!-- row -->


					<div class="form-buttons">
						<button type="button" class="btn btn-link center-block" id="assign-to-group"><i class="fa fa-save"></i></button>
					</div><!-- form-buttons -->

				</form>

			</div><!-- .modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->


<div class="modal fade" id="modal-points-form" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="modal-points-title">Edit Points</h4>
			</div><!-- .modal-header -->


			<div class="modal-body container-fluid">
				<form>




					<div class="row form-group">
						<div class="col-xs-9">
							<div class="row">
								<div class="col-xs-2 bold">Video:</div>
								<div class="col-xs-10" id="points-form-video-title"></div>
							</div><!-- row -->
							@if (config("settings.course_wide.point")==1)
							<div class="row">
								<div class="col-xs-2 bold">Course:</div>
								<div class="col-xs-10" id="points-form-course-name"></div>
							</div><!-- row -->
							@else
							<div class="row">
								<div class="col-xs-2 bold">Course:</div>
								<div class="col-xs-10" id="points-form-course-name"></div>
							</div><!-- row -->
							<div class="row">
								<div class="col-xs-2 bold">Group:</div>
								<div class="col-xs-10" id="points-form-course-name"></div>
							</div><!-- row -->
							@endif

						</div><!-- col -->
						<div class="col-xs-3">
							<img id="points-form-thumbnail-img" src="" class="video-thumbnail">
						</div><!-- col -->
					</div><!-- row -->



					<div class="row form-group space-top-30">
						<div class="col-xs-12">
							<fieldset class="space-bottom">
								<label for="modal-point-instruction">Instruction for students:</label>
								<input id="modal-point-instruction" class="form-control" placeholder="Enter instruction for students..." type="text">
							</fieldset>
							<fieldset id="modal-points">
								<label>Points:</label>
								<div class="row">
									<div class="col-xs-10">
										<input class="form-control new-point" type="text" placeholder="Enter Video Key Point text...">
									</div><!-- col-xs-10 -->
									<div class="col-xs-2">
										<button class="modal-delete-point btn-sm outline-button full-width" type="button" title="delete">
											<span class="hidden-xs">Delete</span>
											<i class="fa fa-minus-circle"></i>
										</button>
									</div><!-- col-xs-2 -->
								</div><!-- row -->
							</fieldset>
							<button id="modal-another-point" class="btn-sm outline-button" type="button" title="Add another point">
								Add another point
								<i class="fa fa-plus-circle"></i>
							</button>
						</div><!-- points-controls -->
					</div><!-- row -->



					<div class="form-buttons">
						<button type="button" class="btn btn-link" id="save-points"><i class="fa fa-save"></i></button>
						<button type="button" class="btn btn-link pull-right" id="delete-points"><i class="fa fa-trash"></i></button>
					</div><!-- form-group -->

				</form>

			</div><!-- .modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->


<!-- Quiz Creation Modal content-->
<div class="modal fade" id="quiz_create_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width:70%;">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title">SET QUIZ</h4>
			</div><!-- .modal-header -->



			<div class="modal-body container-fluid">
				<div class="player_wrap">
					<div id="player">
					</div>
					<br>
					<p class="quiz_hint"><i class="fa fa-info-circle" aria-hidden="true"></i> Pause video to create new quiz</p>
				</div>

				<div class="player_wrap">
					<div id="jwplayer">
					</div>
					<br>
					<p class="quiz_hint"><i class="fa fa-info-circle" aria-hidden="true"></i> Pause video to create new quiz</p>
				</div>

				<!-- <button class="btn btn-default" data-toggle="modal" href="#stack3">Launch modal</button> -->

				<div class="container-fluid create_new_quiz_wrap" style="display:none;">
					<h2><i class="fa fa-plus-square" aria-hidden="true"></i> <span id='create_quiz_time_title'>CREATE NEW QUIZ @</span> <span id='create_quiz_time'></span></h2>

					<div class="container-fluid col-xs-4 question_warp">
						<ul>
							<div><span class="title">Question List</span><i class="fa fa-caret-down" aria-hidden="true" id="quiz_list_toggle_btn"></i></div>
							<ul>
								<!-- <li>Question 01<i class="fa fa-trash-o" aria-hidden="true"></i></li>
								<li>Question 20<i class="fa fa-trash-o" aria-hidden="true"></i></li> -->
							</ul>
						</ul>
					</div>

					<div class="container-fluid col-xs-8 create_block_warp">
						<ul  class="nav nav-pills">
							<li class="active">
								<a  href="#1a" data-toggle="tab">MULTIPLE CHOICE</a>
							</li>
							<li>
								<a href="#2a" data-toggle="tab">Short Answer Question</a>
							</li>
						</ul>

						<div class="tab-content clearfix">
							<div class="tab-pane active" id="1a">
								<h4>Please input your Question</h4>
								<textarea class="quiz_text_area" rows="2"></textarea>
								<h4>Please add choice options <br>(input then press enter)
								</h4>

								<table class="quiz_options_wrap" id="quiz_options_wrap">
									<tbody>
										<!-- <tr>
											<td>&nbsp;&nbsp; A : &nbsp;&nbsp; </td>
											<td><input type="text" value=""></input></td>
										</tr> -->
									</tbody>
								</table>

								<button class="quiz_options_btn" id="quiz_options_add">Add option <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
								<button class="quiz_options_btn" id="quiz_options_remove">Remove option <i class="fa fa-minus-circle" aria-hidden="true"></i></button>

								<br />

								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="1a_btn" blockType="multiple_choice">
									Add to question list <i class="fa fa-plus" aria-hidden="true"></i>
								</button>
								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="1a_edit_btn" blockType="text" style="display:none;">
									Finish & Add to list <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="1a_cancel_btn" blockType="text" style="display:none;">
									Cancle Edit <i class="fa fa-ban" aria-hidden="true"></i>
								</button>
							</div>
							<div class="tab-pane" id="2a">
								<h4>Please input your Question</h4>
								<textarea class="quiz_text_area" rows="2" id="quiz_text_area_question"></textarea>
								<h4>Please input your default Answer</h4>
								<textarea class="quiz_text_area" rows="2" id="quiz_text_area_answer"></textarea>
								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="2a_btn" blockType="text">
									Add to question list <i class="fa fa-plus" aria-hidden="true"></i>
								</button>
								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="2a_edit_btn" blockType="text" style="display:none;">
									Finish & Add to list <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
								<button type="button" class="btn btn-link pull-left add_question_list_btn" id="2a_cancel_btn" blockType="text" style="display:none;">
									Cancle Edit <i class="fa fa-ban" aria-hidden="true"></i>
								</button>
							</div>
						</div>

					</div>

				</div>

				<div class="container-fluid">
					<div class="question_preview_wrap" style="display:none;">
						<h2>Question preview</h2>
						<table>
							<tr>
								<th>Question type: </th>
								<th id="question_preview_type"></th>
							</tr>
							<tr>
								<th>Question: </th>
								<th id="question_preview_question"></th>
							</tr>
							<tr>
								<th>Choice options: </th>
								<th id="question_preview_option"></th>
							</tr>
							<tr>
								<th>Answer: </th>
								<th id="question_preview_answer"></th>
							</tr>
						</table>
					</div>
				</div>


				<div class="container-fluid">
					<div class="button_warp quiz_save_btn_wrap" style="display:none;">
						<button type="button" class="btn btn-link center-block quiz_save_btn" id="quiz_save_btn" >
						</button>
					</div>
				</div>


				<div class="container-fluid quiz_warp">
					<h2><i class="fa fa-list" aria-hidden="true"></i> CURRENT QUIZ LIST </h2>
					<ul>
						<div><span class="title"> Quiz List &nbsp;&nbsp;</span><i class="fa fa-caret-down" aria-hidden="true" id="question_list_toggle_btn"></i></div>
						<ul>

						</ul>
					</ul>
				</div>

				<div class="container-fluid">
					<div class="quiz_preview_wrap" style="display:none;">
						<h2>Quiz list preview</h2>
						<table>
							<tr>
								<th>Quiz Name: </th>
								<th id="quiz_preview_name"></th>
							</tr>
							<tr>
								<th>Quiz stop time point: </th>
								<th id="quiz_preview_stop"></th>
							</tr>
							<tr>
								<th>Quiz details: </th>
								<th>
									<table border="1" cellpadding="20" id="quiz_preview_details">

									</table>
								</th>
							</tr>
						</table>
					</div>
				</div>

				<div class="container-fluid">
					<div class="button_warp">
						<button type="button" class="btn btn-link center-block quiz_submit_btn" id="quiz_submit_btn">
							SUBMIT & SAVE TO SERVER <i class="fa fa-upload" aria-hidden="true"></i>
						</button>
					</div>
				</div>

			</div><!-- .modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->

<div id="pop_out_dialog" class="modal fade" tabindex="-1" style="display: none;">
	<div class="modal-dialog" role="document" style="width:80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" aria-hidden="true" id="close_to_use">×</button>
				<h4 class="modal-title">Oval Notification</h4>
			</div>

			<div>
				<button type="button" class="btn btn-link center-block quiz_submit_btn" id="skip_tutorial">Skip Tutorial <i class="fa fa-ban" aria-hidden="true"></i></button>
			</div>

			<div class="modal-body" id="pop_out_dialog_body">

			</div>
			<div>
				<button type="button" id="close_to_use" class="btn btn-link center-block quiz_submit_btn" >
					OK, start to use &nbsp;&nbsp;&nbsp;<i class="fa fa-play" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
</div>
<!-- End Quiz Creation Modal content-->

<div id="alert_dialog" class="modal fade" tabindex="-1" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Oval Notification</h4>
			</div>
			<div class="modal-body" id="alert_dialog_content">

			</div>
			<div>
				<button type="button" class="btn btn-link center-block quiz_submit_btn" onclick="$('#alert_dialog').modal('hide');">
					OK, I got it. &nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<div id="confirm_dialog" class="modal fade" tabindex="-1" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Oval Notification</h4>
			</div>
			<div class="modal-body" id="confirm_dialog_content">

			</div>
			<div class="modal-body">
				<button type="button" class="btn btn-link center-block" id="confirm_delete" onclick="$('#confirm_dialog').modal('hide');">
					Continue
				</button>
				<button type="button" class="btn btn-link center-block" onclick="$('#confirm_dialog').modal('hide');">
					Cancle
				</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="visibility-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="modalLabel">EDIT VIDEO VISIBILITY</h4>
			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">
				<form id="visibility-form">
					<input type="hidden" id="group-video-id">
					<div class="form-group">
						<div>Set visibility to</div>
						<label class="radio-inline">
							<input type="radio" name="visibility-radio" id="visible-radio" value="0">Visible
						</label>
						<label class="radio-inline">
							<input type="radio" name="visibility-radio" id="hidden-radio" value="1">Hidden
						</label>
					</div><!-- form-group -->
					<div class="form-buttons">
						<button type="submit" class="btn btn-link center-block" id="save-visibility"><i class="fa fa-save" aria-hidden="true"></i></button>
					</div><!-- form-buttons -->
				</form>
			</div><!-- modal-body -->
		</div><!-- modal-content -->
	</div><!-- modal-dialog -->
</div><!-- #visibility-form -->

	<div class="modal fade" id="order-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-close" aria-hidden="true"></i>
					</button>
					<h4 class="modal-title" id="modalLabel">EDIT VIDEO LIST</h4>
				</div><!-- .modal-header -->

				<div class="modal-body container-fluid">
					<div>
						Edit the order videos appear in the list.
					</div>
					<form id="order-form">
						<ul id="video-order-list" class="sortable-list">
							@foreach ($group_videos->sortBy('order') as $gv)
							<li data-id="{{$gv->id}}">{{$gv->video()->title}}</li>
							@endforeach
						</ul>
						<div class="form-buttons">
							<button type="submit" class="btn btn-link center-block" id="save-order"><i class="fa fa-save" aria-hidden="true"></i></button>
						</div><!-- form-buttons -->
					</form>
				</div><!-- modal-body -->
			</div><!-- modal-content -->
		</div><!-- modal-dialog -->
	</div><!-- #order-form -->

<div class="modal fade" id="text-analysis-modal" tabindex="-1" role="dialog" aria-labelledby="text-analysis-title" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="text-analysis-title">EDIT VISIBILITY OF RELATED CONTENTS</h4>
			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">
				<form id="text-analysis-form">
					<input type="hidden" class="group-video-id">
					<div class="form-group">
						<div>Set visibility to</div>
						<label class="radio-inline">
							<input type="radio" name="analysis-vis-radio" id="show-radio" value="1">Visible
						</label>
						<label class="radio-inline">
							<input type="radio" name="analysis-vis-radio" id="hide-radio" value="0">Hidden
						</label>
					</div><!-- form-group -->
					<div class="form-buttons">
						<button type="submit" class="btn btn-link center-block" id="save-analysis-vis"><i class="fa fa-save" aria-hidden="true"></i></button>
					</div><!-- form-buttons -->
				</form>
			</div><!-- modal-body -->
		</div><!-- modal-content -->
	</div><!-- modal-dialog -->
</div><!-- #visibility-form -->

<div class="modal fade" id="transcript-form" tabindex="-1" role="dialog" aria-labelledby="transcript-modal-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="transcript-modal-title">UPLOAD TRANSCRIPT</h4>
			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">
				<form id="upload-transcript-form" method="POST" action="{{ route('transcripts.store') }}" enctype="multipart/form-data" role="form" data-toggle="validator">
					{{ csrf_field() }}
					<input type="hidden" name="video_id" />
					<div class="form-group">
						<label for="transcript-file">Please select file to upload...</label>
						<input type="file" id="transcript-file" name="file" data-filetype="srt" data-required-error="Please select a file in .srt format" required>
						<div class="help-block with-errors"></div>
					</div><!-- form-group -->

					<div class="instruction space-top">
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
						If a transcript file already exists for this video, it will be overwritten.
					</div>
					<div class="form-buttons">
						<button type="submit" class="btn btn-link center-block" id="upload">
							<i class="fa fa-upload" aria-hidden="true"></i>
						</button>
					</div><!-- form-buttons -->
				</form>

			</div><!-- modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->


<div class="modal fade" id="edit-keywords-modal" tabindex="-1" role="dialog" aria-labelledby="edit-keywords-modal-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="edit-keywords-modal-title">EDIT KEYWORDS</h4>
			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">
				<form id="edit-keywords-form">
					<div class="form-group">
						<table class="col-sm-10 col-sm-offset-1">
							<thead>
								<tr>
									<th>Word / Phrase</th>
									<th class="text-center">Delete</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div><!--form-group-->

					<div class="form-buttons">
						<button type="submit" class="btn btn-link center-block" id="update-keywords">
							<i class="fa fa-save" aria-hidden="true"></i>
						</button>
					</div><!-- form-buttons -->
				</form>

			</div><!-- modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->


<div class="modal fade" id="modal-assigned-group-list" tabindex="-1" role="dialog" aria-labelledby="assigned-groups-modal-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="assigned-groups-modal-title">ASSIGNED GROUPS</h4>
			</div><!-- .modal-header -->

			<div class="modal-body container-fluid">

				<div class="row">
					<div class="btn-group col-xs-12">
						<button type="button" class="btn dropdown-button dropdown-left">Course</button>
						<button type="button" class="btn dropdown-button dropdown-center" id="assigned-groups-course-name" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"></button>
						<button type="button" class="btn dropdown-button dropdown-toggle" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
							<span class="fa fa-caret-down"></span>
							<span class="sr-only">Toggle Dropdown</span>
						</button>
						<ul class="dropdown-menu" id="assigned-groups-course-ul">
							@if (count($user->coursesTeaching())==0)
							<li>NO COURSES</li>
							@else
							@foreach ($user->coursesTeaching() as $c)
							<li data-id="{{$c->id}}">{{ $c->name }}</li>
							@endforeach
							@endif
						</ul><!-- .dropdown-menu -->
					</div><!-- btn-group -->
				</div><!-- row -->

				<table id="assigned-group-table" class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-6">Group Name</th>
							<th class="col-xs-2 text-center">Comment Instruction</th>
							<th class="col-xs-2 text-center">Points</th>
							<th class="col-xs-2 text-center">Quiz</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>


			</div><!-- modal-body -->
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- .#modal-form -->

@include('group_videos.index._controls_setting_modal')
