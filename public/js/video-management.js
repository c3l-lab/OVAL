var modal_course_id;
var modal_group_id;
var modal_course_name;
var modal_group_name;
var modal_video_id;
var modal_groups;


function saveVideo (v_id, u_id, media_type, point_instruction, points, c_id, request_analysis) {
	$.ajax({
		type: "POST",
		url: "/videos",
		data: {video_id:v_id, media_type:media_type, point_instruction:point_instruction, points:points, course_id:c_id, request_analysis:request_analysis},
		success: function(data) {
			$("#loading-hud").hide();
			if (data.error) {
				alert(data.error);
				return;
			}

			if(data.course_id){
				var protocol = window.location.protocol;
				var host = window.location.host;
				window.location.href = protocol+"//"+host+"/group_videos/?course_id="+c_id+"?"+Math.random()+"#v-"+data.video_id;
			}
			else {
				window.location.hash = 'unassigned';
				window.location.reload(true);
			}

		},
		error: function(req, status, error) {
			$("#loading-hud").hide();
			console.log("error /add_video - "+error);	/////
			alert("Oops, something went wrong...");	////////
		}
	});
}

function modalGetGroups() {
	$("#modal-course-name").text(modal_course_name);
	$.ajax({
		type: "GET",
		url: "/groups/unassigned",
		data: {course_id: modal_course_id, video_id: modal_video_id, user_id: user_id},
		success: function(data) {
			var groups = data.unassigned_groups;
			var list = $("#modal-group-list");
			list.html("");
			if (groups.length == 0) {
				list.append("<option disabled>No available groups</option>");
			}
			else {
				$.each(groups, function(i, group) {
					list.append('<option value="'+group.id+'">'+group.name+'</option>');
				});
				list.focus();
			}
		},
		error: function(req, status, err) {
			console.log("error get_groups - "+status+": "+err);	///////
		},
		async: false
	});
}

function assignVideoToGroups(group_ids, copy_from_group_id, copy_comment_instruction, copy_points, copy_quiz) {
	if (group_ids.length == 0) {
		alert("Please select groups to assign this video to");/////
		return;
	}
	$.ajax({
		type: "POST",
		url: "/videos/" + modal_video_id + "/assign",
		data: {
			group_ids: group_ids,
			course_id: modal_course_id,
			copy_from: copy_from_group_id,
			copy_comment_instruction: copy_comment_instruction,
			copy_points: copy_points,
			copy_quiz: copy_quiz
		},
		success: function () {
			alert("Video is assigned to the Group");	/////
			$("#modal-form").modal('hide');
			window.location.href = "/group_videos/?course_id="+modal_course_id+"?"+Math.random()+"#assigned";
		},
		error: function(req, status, err) {
			console.log("error save_video_group - "+status+": "+err);	///////
		},
		async: false
	});
}


function checkIfCourseWidePoints (course_id, video_id) {
	$.ajax({
			type: "POST",
			url: "/check_if_course_wide_points",
			data: {course_id: modal_course_id, video_id: modal_video_id},
			success: function(data) {
				if (data.is_course_wide) {
					$("#same-points").prop("checked", true);
					$("#points-form-groups").hide();
				}
				else {
					$("#not-same-points").prop("checked", true);
					$("#points-form-groups").show();
				}
			},
			failure: function (request, status, error) {
				console.log(status+": error checking if course-wide points: "+error);	///////
			}
		});
}

function getGroupsForVideo() {
		$.ajax({
			type: "GET",
			url: "/videos/" + modal_video_id + "/groups",
			success: function(data) {
				var groups = data.groups;
				var ul = $("#points-form-groups-dropdown");
				ul.html("");
				if (groups.length > 0) {
					// modal_group_id = groups[0].id;
					$.each(groups, function(i, group) {
						ul.append('<li id="'+group.id+'">'+group.name+'</li>');
					});
					$("#points-form-group-name").text(groups[0].name);

					fillPointsInputs();
				}
			},
			error: function(request, status, error) {
				console.log(status+": error getting groups for video: "+error);	///////
			},
		});
}

function fillPointsInputs() {
	$.ajax({
		type: "POST",
		url: "/get_points_for_group_video",
		data: {group_id: modal_group_id, video_id: modal_video_id},
		success: function(data) {
			var instruction = unescapeHtml(data.point_instruction);
			$("#modal-point-instruction").val(instruction);
			$("#modal-points .row").remove();
			var points = data.points;
			if (points.length > 0) {
				$.each(points, function (index, point) {
					var row = $('<div>', {'class':'row'}).appendTo("#modal-points");
					var col10 = $('<div>', {
									'class': 'col-xs-10',
									'html':'<input class="form-control" id="'+point.id+'" type="text" value="'+point.description+'">'
								}).appendTo(row);
					var col2 = $('<div>', {
									'class':'col-xs-2',
									'html':'<button type="button" class="modal-delete-point outline-button btn-sm full-width" title="delete"><span class="hidden-xs">Delete</span><i class="fa fa-minus-circle"></i></button>'
								}).appendTo(row);
				});
			}
			else {
				var row = $('<div>', {'class':'row'}).appendTo("#modal-points");
				var col10 = $('<div>', {
									'class': 'col-xs-10',
									'html':'<input class="form-control new-point" type="text" placeholder="Point text here...">'
								}).appendTo(row);
					var col2 = $('<div>', {
									'class':'col-xs-2',
									'html':'<button type="button" class="modal-delete-point outline-button btn-sm full-width" title="delete"><span class="hidden-xs">Delete</span><i class="fa fa-minus-circle"></i></button>'
								}).appendTo(row);
			}
		},
		error: function(request, status, error) {
			console.log(status+": error getting points: "+error);	///////
		}
	});
}

function hostname(url) {
    var match = url.match(/:\/\/(www[0-9]?\.)?(.[^\/:]+)/i);
    if ( match != null && match.length > 2 && typeof match[2] === 'string' && match[2].length > 0 ) return match[2];
}

function unescapeHtml(safe) {
	return safe ? safe.replace(/&amp;/g, '&')
		.replace(/&lt;/g, '<')
		.replace(/&gt;/g, '>')
		.replace(/&quot;/g, '"')
		.replace(/&#039;/g, "'")
		:
		"";
}

function first_key_of_array(arr) {
	for (var key in arr) {
		return key;
	}
}


$('document').ready(function(){


	var modal = $("#modal-form");
	var points_form = $("#modal-points-form");


	$("#course-name").text(course_name);
	$("#group-name").text(group_name);

	$(".msg").hide();


	$("#points-controls").hide();
	$("#text-analysis").hide();
	$("#loading-hud").hide();

	$("#course-to-assign").val(course_id);

	//--hide analysis request controls
	$("#text-analysis").hide();


	//-- show/hide corresponding fields when radio value changed --
	$("#add-video-form input").on("change", function() {
		if($("input[name=group-radio]:checked", "#add-video-form").val() === "true") {
			$("#points").show();
		}
		else {
			$("#points").hide();
		}
		if ($("input[name=points-radio]:checked", "#add-video-form").val() === "true") {
			$("#points-controls").show();
		}
		if ($("input[name=points-radio]:checked", "#add-video-form").val() === "false") {
			$("#points-controls").hide();
		}

	});//end add-video-form radio on change

	$("[name=course-radio]").val(["true"]);
	$("[name=points-radio]").val(["false"]);


	$("#another-point-button").on("click", function() {
		var row = $('<div>', {'class':'row'}).appendTo("#points-inputs");
		var col10 = $('<div>', {
						'class':'col-xs-10',
						'html':'<input class="form-control gray-textbox" type="text" placeholder="Point text here...">'
					}).appendTo(row);
		var col2 = $('<div>', {
						'class':'col-xs-2',
						'html':'<button type="button" class="delete-point outline-button btn-sm full-width" title="delete"><span class="hidden-xs">Delete</span><i class="fa fa-minus-circle"></i></button>'
					}).appendTo(row);
		$("#points-inputs :text").last().focus();
	});
	$("#points").on("click", '.delete-point', function(e) {
		$(this).parent().parent().remove();
	});

	//-- add video --
	$("#add-video-form input").on("keydown", function(e) {
		if (e.which == 13) {
			e.preventDefault();
		}
	});
	$("#add-video-button").on("click", function(e) {
		$(this).validator('validate');
		if($(this).find('.has-error').length) {
			return false;
		}
		$("#loading-hud").show();
		var video_id;
		var media_type;

		var u_id = $("#UID").val();
		var c_id = $("#CID").val();
		var g_id = $("#GID").val();

		var url = $("#video-url").val();

		var course_id = null;
		if($("[name=group-radio]:checked").val() == "true") {
			course_id = $("select[name=course-to-assign]").val();
		}

		var points = [];
		var point_instruction = null;
		if($('input[name=points-radio]:checked', '#add-video-form')) {
			point_instruction = $("#point-instruction-textbox").val();
			$('#points-inputs :text').each(function(index, item) {
				if($(item).val()!=="") {
					points.push($(item).val());
				}
			});
		}

		var req_analysis = false;
		if ($("#request-analysis").is(':checked')) {
			req_analysis = true;
		}


		//--YouTube--
		media_type = "youtube";
		var host = hostname(url);

		if (host == "youtube.com") {
			video_id = url.substr(url.lastIndexOf('watch?v=')+8, 11);
		} else if (host == "youtu.be") {
			video_id = url.substr(url.lastIndexOf('/')+1, 11);
		} else {
			alert('Please check your YouTube URL is valid. (It starts with https://www.youtube.com/ or https://youtu.be/)');
			return false;
		}

		saveVideo (video_id, u_id, media_type, point_instruction, points, course_id, req_analysis);
		$("#add-video-form").trigger("reset");
		return false;
	});//end add-video-form submit

	//-- archive/un-archive --
	$(".archive-button").on("click", function() {
		var group_video_id = $(this).data('id');

		//--check if student activity & confirm to archive
		$.ajax({
			type: "POST",
			url: "/check_student_activity",
			data: {group_video_id: group_video_id},
			success: function(data) {
				if (data.has_activity && confirm("There are student activities on this video for the group. Are you sure you would like to archive it?")) {
					$.ajax({
						type:"POST",
						url: "/group_videos/" + group_video_id + "/archive",
						success: function(data) {
							window.location.reload();
						},
						error(req, status, error) {
							console.log("error /archive_group_video - "+error);/////
						}
					});
				}
				if (!data.has_activity && confirm("Are you sure you would like to make this video unavailable to the group?")) {
					$.ajax({
						type:"delete",
						url: "/group_videos/" + group_video_id,
						success: function(data) {
							window.location.reload();
						},
						error(req, status, error) {
							console.log("error /delete_group_video - "+error);/////
						}
					});
				}
			},
			error: function(req, status, error) {
				console.log("error /check_student_activity - "+error);/////
			}
		});
	});

	modal.on("show.bs.modal", function(e) {
		var button = $(e.relatedTarget); // Button that triggered the modal
  		modal_video_id = button.data('id');
		modal_course_id = course_id;
		modal_course_name = course_name;
		// modal_group_id = group_id;
		modal_group_name = group_name;

		//--populate thumbnail and title for video--
		$.ajax({
			type: "GET",
			url: "/videos" + "/" + modal_video_id,
			success: function (data) {
				$("#modal-video-thumbnail").attr("src", data.thumbnail_url);
				$("#modal-video-title").text(data.title);
			},
			error: function (req, status, error) {
				console.log("error /get_video_info - "+error);/////
			}
		});

		$("#copy-from").hide();

		//-- populate course dropdown
		$("#course-dropdown li").first().click();
		// $("#modal-group-list").focus();
	});


	$("#course-dropdown li").on('click', function() {
		modal_course_id = $(this).attr("id");
		modal_course_name = $(this).text();
		$("#assign-video-to-group-form").trigger('reset');
		modalGetGroups();
		// $("#modal-group-list").focus();
	});


	//-- when copy-contents option changed, fetch groups that has same video & configure form
	$("input[type=radio][name=copy-contents]").on("change", function() {
		if($(this).val() === "true") {
			$("#copy-from-course").html("");
			$("#copy-from-course").prop("disabled", false);
			$("#copy-from-group").html("");
			$("#copy-from-group").prop("disabled", false);
			$("#copy-from").show();
			modal_groups = [];

			$.ajax({
				type: "GET",
				url: "/videos/" + modal_video_id + "/groups/with_contents",
				success: function(data) {
					if (data.length == 0) {
						$("#copy-from-course").append('<option>No available courses</option>');
						$("#copy-from-course").prop("disabled", "disabled");
						$("#copy-from-group").append('<option>No available groups</option>');
						$("#copy-from-group").prop("disabled", "disabled");
						$("#comment-instruction-cb").prop("disabled", true);
						$("#points-cb").prop("disabled", true);
						$("#quiz-cb").prop("disabled", true);
					}
					else {
						modal_groups = data;
						var selected_course_id = first_key_of_array(modal_groups);
						for(var key in modal_groups) {
							$("#copy-from-course").append('<option value="'+key+'">'+modal_groups[key][0].course_name+'</option>');
						}
						$("#copy-from-course").val(selected_course_id);
						$("#copy-from-course").trigger("change");
					}
				},
				error: function(request, status, error) {
					console.log("error /get_other_groups_with_video -"+error);/////
				}
			});
		}
		else {
			$("#copy-from").hide();
		}
	});

	//-- when course selected, populate group dropdown
	$("#copy-from-course").on('change', function(e) {
		var selected = $(this).find("option:selected");
		var selected_course_id = selected.val();
		var groups = modal_groups[selected_course_id];
		$("#copy-from-group").html("");
		$("#copy-from-group").prop("disabled", false);
		$.each (groups, function(i, val) {
			if(i==0) {
				$("#copy-from-group").append('<option value="'+val.id+'" selected>'+val.name+'</option>');
			}
			else {
				$("#copy-from-group").append('<option value="'+val.id+'">'+val.name+'</option>');
			}
		});
		$("#copy-from-group").val(groups[0].id);
		$("#copy-from-group").trigger("change");
	});

	//-- when group selected, configure checkboxes for option to match
	$("#copy-from-group").on('change', function(e) {
		var selected = $(this).find("option:selected");
		var selected_group_id = selected.val();
		var selected_course = $("#copy-from-course option:selected").val();
		var groups = modal_groups[selected_course];

		$.each(groups, function(i, val) {
			if (val.id == selected_group_id) {
				if (val.has_comment_instruction == true) {
					$("#copy-comment-instruction-checkbox").removeClass("disabled");
					$("#comment-instruction-cb").prop("disabled", false);
				}
				else {
					$("#copy-comment-instruction-checkbox").addClass("disabled");
					$("#comment-instruction-cb").prop("disabled", true);
				}
				if (val.has_points == true) {
					$("#copy-points-checkbox").removeClass("disabled");
					$("#points-cb").prop("disabled", false);
				}
				else {
					$("#copy-points-checkbox").addClass("disabled");
					$("#points-cb").prop("disabled", true);
				}
				if (val.has_quiz == true) {
					$("#copy-quiz-checkbox").removeClass("disabled");
					$("#quiz-cb").prop("disabled", false);
				}
				else {
					$("#copy-quiz-checkbox").addClass("disabled");
					$("#quiz-cb").prop("disabled", true);
				}
				return false;
			}
		});
	});


	modal.on("click", "#assign-to-group", function(e) {
		e.preventDefault();
		$("#assign-video-to-group-form").validator('validate');
		if($("#assign-video-to-group-form").find('.has-error').length) {
			return false;
		}

		var group_ids = [];
		$("#modal-group-list :selected").each(function() {
			group_ids.push($(this).val());
		});
		var copy = $("#copy-contents-yes").is(":checked") ? true : false;
		var copy_from_group_id = -1;
		var copy_comment_instruction = false;
		var copy_points = false;
		var copy_quiz = false;
		if (copy) {
			copy_from_group_id = $("#copy-from-group option:selected").val();
			copy_comment_instruction = $("#comment-instruction-cb").is(":checked") ? true : false;
			copy_points = $("#points-cb").is(":checked") ? true : false;
			copy_quiz = $("#quiz-cb").is(":checked") ? true : false;
		}
		assignVideoToGroups(group_ids, copy_from_group_id, copy_comment_instruction, copy_points, copy_quiz);
	});

	modal.on("hidden.bs.modal", function () {
		modal_course_id = null;
		modal_course_name = null;
		modal_group_id = null;
		modal_group_name = null;

		$("#modal-video-thumbnail").attr("src", "");
		$("#modal-video-title").text("");
		$("#assign-video-to-group-form").trigger('reset');
	});

	points_form.on("show.bs.modal", function(e) {
		var button = $(e.relatedTarget);
		modal_video_id = button.data('id');
		var title = button.data('title');
		modal_course_id = course_id;
		modal_group_id = group_id;
		$("#points-form-course-name").text(course_name);
		// $("#points-form-video-title").text(title);
		$("#points-form-group-name").text(group_name);

		//--populate thumbnail and title for video--
		$.ajax({
			type: "GET",
			url: "/videos/" . model_video_id,
			success: function (data) {
				$("#points-form-thumbnail-img").attr("src", data.thumbnail_url);
				$("#points-form-video-title").text(data.title);
			},
			error: function (req, status, error) {
				console.log("error /get_video_info - "+error);/////
			}
		});

		// checkIfCourseWidePoints(modal_course_id, modal_video_id);
		// getGroupsForVideo();
		fillPointsInputs();
	});

	points_form.on("click", "#modal-another-point", function() {
		var row = $('<div>', {'class':'row'}).appendTo("#modal-points");
		var col10 = $('<div>', {
						'class':'col-xs-10',
						'html':'<input class="form-control new-point" type="text" placeholder="Point text here...">'
					}).appendTo(row);
		var col2 = $('<div>', {
						'class':'col-xs-2',
						'html':'<button type="button" class="modal-delete-point outline-button btn-sm full-width" title="delete"><span class="hidden-xs">Delete</span><i class="fa fa-minus-circle"></i></button>'
					}).appendTo(row);
		$("#modal-points :text").last().focus();
	});

	$("input[name=course-wide-points-radio]").on("change", function() {
		if ($(this).val() === "yes") {
			$("#points-form-groups").hide();
		}
		if ($(this).val() === "no") {
			$("#points-form-groups").show();
		}
	});

	points_form.on("click", ".modal-delete-point", function() {
		$(this).parent().parent().remove();
	});

	points_form.on("click", "#points-form-course-dropdown li", function() {
		modal_course_id = $(this).attr("id");
		$("#points-form-course-name").text($(this).text());
		$.ajax({
				type: "GET",
				url: "/courses/" + modal_course_id + "/videos",
				success: function(data) {
					var videos = data.videos;
					var ul = $("#points-form-video-dropdown");
					ul.html("");
					$.each(videos, function(i, video) {
						ul.append('<li id="'+video.id+'">'+video.title+'</li>');
					});
					$("#points-form-video-dropdown li").first().click();
				},
				error: function(request, status, error) {
					console.log(status+": error getting videos for course: "+error);	///////
				},
				async: false
			});
	});

	points_form.on("click", "#points-form-video-dropdown li", function() {
		$("#points-form-video-title").text($(this).text());
		modal_video_id = $(this).attr("id");

		checkIfCourseWidePoints(modal_course_id, modal_video_id);
		getGroupsForVideo();
	});

	points_form.on("click", "#points-form-groups-dropdown li", function() {
		$("#points-form-group-name").text = $(this).text();
		modal_group_id = $(this).attr("id");
	});

	points_form.on("click", "#save-points", function() {
		var is_course_wide = false;
		if ($("input[name=course-wide-points-radio]").val() === "yes") {
			is_course_wide = true;
		}
		var point_instruction = $("#modal-point-instruction").val();
		var point_ids = [];
		var points = [];
		$('#modal-points :text').each(function(index, item) {
			if($(item).val()!=="") {
				var point_id = $(this).hasClass('new-point') ? -1 : Number($(this).attr('id'));
				point_ids.push(point_id);
				points.push($(item).val());
			}
		});

		$.ajax ({
			type: "POST",
			url: "/save_points",
			data: {course_id: modal_course_id, group_id: modal_group_id, video_id: modal_video_id, is_course_wide: is_course_wide, point_instruction: point_instruction, point_ids: point_ids, points: points},
			success: function(data) {
				alert("Points were saved");
				points_form.modal("hide");
			},
			error: function(request, status, error) {
				console.log(status+": error saving points: "+error);	///////
			}
		});
	});

	points_form.on("click", "#delete-points", function() {
		if (confirm('Are you sure to delete points and instruction for selected video for the group/course?')) {
			var is_course_wide = false;
			if ($("input[name=course-wide-points-radio]").val() === "yes") {
				is_course_wide = true;
			}
			$.ajax({
				type:"POST",
				url:"/delete_points",
				data:{video_id: modal_video_id, group_id: modal_group_id, is_course_wide: is_course_wide},
				success: function(data) {
					points_form.modal('hide');
				},
				error: function(request, status, error) {
					console.log(status+": error deleting points: "+error);	///////
				}
			});
		}
	});

	points_form.on("hidden.bs.modal", function () {
		modal_course_id = null;
		modal_group_id = null;
		modal_video_id = null;
		$("#modal-points-form :text").val('');

	});

	var visibility_modal = $("#visibility-modal");
	$(".visibility-button").on("click", function() {
		visibility_modal.find("#group-video-id").val($(this).data('id'));
		var hidden = $(this).data('hidden');
		if (hidden) {
			visibility_modal.find('#hidden-radio').prop('checked', true);
		}
		else {
			visibility_modal.find('#visible-radio').prop('checked', true);
		}
	});
	visibility_modal.find("#save-visibility").on("click", function(e) {
		e.preventDefault();
		var group_video_id = $('#group-video-id').val();
		var visibility = $('input[name="visibility-radio"]:checked').val();
		$.ajax({
			type:"POST",
			url: "/group_videos/" + group_video_id + "/toggle_visibility",
			data: { visibility:visibility },
			success: function() {
				location.reload();
			},
			error: function(requst, status, error) {
				console.log("error /edit_visibility - "+error);	/////
			}
		});
	});

	sortable(".sortable-list", {
		forcePlaceholderSize: true,
		placeholderClass: 'sortable-placeholder'
	});
	$("#order-form").on("submit", function(e) {
		e.preventDefault();
		var order = [];
		$('#video-order-list li').each(function(i) {
			order.push($(this).data('id'));
		});
		$.ajax({
			type: "POST",
			url: "/group_videos/sort",
			data: {group_video_ids: order},
			success: function() {
				$("#order-modal").modal('hide');
				location.reload();
			},
			error: function(req, status, err) {
				console.log("error /edit_video_order: "+err);	/////
			}
		});
	});

	var keywords_modal = $("#text-analysis-modal");
	$(".text-analysis-button").on("click", function() {
		keywords_modal.find(".group-video-id").val($(this).data('id'));
		var show = $(this).data('show');
		if (show) {
			keywords_modal.find('#show-radio').prop('checked', true);
		}
		else {
			keywords_modal.find('#hide-radio').prop('checked', true);
		}
	});
	keywords_modal.find("#save-analysis-vis").on("click", function(e) {
		e.preventDefault();
		var group_video_id = $('.group-video-id').val();
		var show = $('input[name="analysis-vis-radio"]:checked').val();
		$.ajax({
			type:"POST",
			url: "/group_videos/" + group_video_id + "/toggle_analysis",
			data: { visibility:show },
			success: function() {
				location.reload();
			},
			error: function(requst, status, error) {
				console.log("error /edit_text_analysis_visibility - "+error);	/////
			}
		});
	});
	$('#upload-transcript-form').validator({
		custom: {
			filetype: function($el) {
				var acceptable = $el.data('filetype').split(',');
				var filename = $('#upload-transcript-form').find("#transcript-file").val();
				var extension = filename.replace(/^.*\./, '');
				if (extension == filename) {
					extension = '';
				}
				else {
					extension = extension.toLowerCase();
				}
				if ($.inArray(extension, acceptable) == -1) {
					return "Invalid file type. Please select .srt file";
				}
			}
		}
	});
	var transcript_form = $("#transcript-form");
	transcript_form.on("show.bs.modal", function(e){
		transcript_form.find("input[name='video_id']").val($(e.relatedTarget).data('id'));
	});

	$(".request-analysis").on("click", function(e) {
		var video_id = $(this).data('id');
		$.ajax({
			type: "POST",
			url: "/analysis_requests",
			data: {video_id:video_id, user_id:user_id},
			success: function(data) {
				$(".msg").text(data.msg);
				$(".msg").show();
				$("html, body").animate({
					scrollTop:0
				}, 200);
			},
			error: function(request, status, error) {
				console.log("error request-analysis: "+error);	//////
			}
		});
	});

	var edit_keywords_modal = $("#edit-keywords-modal");
	edit_keywords_modal.on("show.bs.modal", function(e){
		$("#update-keywords").attr('data-vid', $(e.relatedTarget).data('vid'));

		var keywords = $(e.relatedTarget).data('keywords');

		var sorted_keywords_arr = $.map(keywords, function (value, index) {
			return value;
		}).sort();

		var sorted_keywords = {};
		for(var i = 0; i < sorted_keywords_arr.length; i++){
			sorted_keywords[i] = sorted_keywords_arr[i];
		}

		$("#edit-keywords-modal table tbody").empty();

		$.each(sorted_keywords, function(i, kw) {
			$("#edit-keywords-modal table tbody").append('<tr><td>'+kw+'</td><td><button type="button" class="btn btn-link center-block delete-keyword" data-word="'+kw+'"><i class="fa fa-minus-circle"></i></button></td></tr>');
		});
	});

	edit_keywords_modal.on("click", '.delete-keyword', function(e) {
		if($(this).hasClass('red')) {
			$(this).removeClass('red');
		}
		else {
			$(this).addClass('red');
		}
	});

	edit_keywords_modal.on("click", "#update-keywords", function(e) {
		e.preventDefault();
		var delete_these = [];
		var video_id = $(this).data('vid');
		$.each($("#edit-keywords-modal table tbody .btn.red"), function(i, val) {
			delete_these.push($(this).data('word'));
		});
		$.ajax({
			type: "POST",
			url: "/delete_keywords",
			data: {words: delete_these, video_id: video_id},
			error: function(request, status, error){
				console.log("error /delete_keywords - "+error);//////
			},
			success:function(request, status, XHR){
				edit_keywords_modal.modal('hide');
				window.location.reload();
			}
		});
	});
	edit_keywords_modal.on("hide.bs.modal", function() {
		$("#edit-keywords-modal table tbody .btn.red").removeClass('red');
	});

	var groups_modal = $("#modal-assigned-group-list");
	groups_modal.on("show.bs.modal", function(e) {

		var button = $(e.relatedTarget); // Button that triggered the modal
  		modal_video_id = button.data('id');
		modal_course_id = course_id;
		modal_course_name = course_name;
		modal_groups = [];
		$.ajax({
			type: "GET",
			url: "/videos/" + modal_video_id + "/groups/with_contents",
			success: function(data) {
				modal_groups = data;
				$('#assigned-groups-course-ul li[data-id="'+modal_course_id+'"]').click();
			},
			error: function(req, status, error) {
				console.log("error /get_groups_with_video");/////
			}
		});
	});

	$("#assigned-groups-course-ul li").on("click", function() {
		modal_course_id = $(this).data("id");
		modal_course_name = $(this).text();
		$("#assigned-groups-course-name").text($(this).text());

		var table = $("#assigned-group-table tbody");
		table.html("");

		var minus = '<i class="fa fa-minus" aria-hidden="true"></i>';
		if(modal_groups[modal_course_id]) {
			var tick = '<i class="fa fa-check" aria-hidden="true"></i>';
			$.each(modal_groups[modal_course_id], function(i, val) {
				var tr = $("<tr>").appendTo(table);
				$("<td>", {
							"class":"col-xs-6",
							"html":"<a href='/group_videos/"+val.group_video_id+"'>"+val.name+"</a>"
						}).appendTo(tr);
				$("<td>", {
							"class":"col-xs-2 text-center",
							"html":val.has_comment_instruction ? tick : minus
						}).appendTo(tr);
				$("<td>", {
							"class":"col-xs-2 text-center",
							"html":val.has_points ? tick : minus
						}).appendTo(tr);
				$("<td>", {
							"class":"col-xs-2 text-center",
							"html":val.has_quiz ? tick : minus
						}).appendTo(tr);
			});
		}
		else {
			var tr = $("<tr>", {"class":"warning"}).appendTo(table);
			tr.append("<td>No assigned groups in this course</td>");
			$("<td>", {
				"class":"col-xs-2 text-center",
				"html": minus
				}).appendTo(tr);
			$("<td>", {
					"class":"col-xs-2 text-center",
					"html": minus
				}).appendTo(tr);
			$("<td>", {
				"class":"col-xs-2 text-center",
				"html": minus
				}).appendTo(tr);
		}
	});

	groups_modal.on("hidden.bs.modal", function() {
		modal_course_id = null;
		modal_course_name = null;
		modal_group_id = null;
		modal_group_name = null;
		$("#assigned-group-list-ul").html("");
	});

	var controlsSettingModal = $('#controls-setting-modal');
	controlsSettingModal.on("submit", "#controls-setting-form", function (event) {
		event.preventDefault();
		var settings = {};
		$(this).find('input[type="checkbox"]').each(function (index, element) {
			settings[$(element).attr("name")] = $(element).is(":checked");
		});
		$.ajax({
			type: "PUT",
			url: $(this).attr("action"),
			contentType: "application/json; charset=utf-8",
			data: JSON.stringify({
				controls: settings
			}),
			success: function (data) {
				if (data.success) {
					controlsSettingModal.modal('hide');
				} else {
					alert("Something went wrong. Please try again.");
				}
			},
		});
	});
});//doc ready
