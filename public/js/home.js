var viewAllAnnotations = true;		//should default to true because students should see instructor's and TA's comment and vice versa.

var annotations = [];
var structured_annotation_answers = [];
var comments = [];
var item;	//used to hold annotation/comment item being edited

//var sliderWidth = 550;	//set to dimension of video player
var previewLen = 200;		//val from common.inc
var previewOffsetX = -10;	//val from common.inc
var previewOffsetY = 3;		//val from common.inc

var domain = "https://oval.dev";

var previous_user_id = window.localStorage.getItem('user_id') || 0;
var trackings = [];
if (previous_user_id != user_id) {
	window.localStorage.setItem('trackings', JSON.stringify(trackings));
} else {
	trackings = JSON.parse(window.localStorage.getItem('trackings'));
}




//-----------------------------------------
//-- Utility --
//-----------------------------------------
function dateStringFromSqlTimestamp(timestamp) {
	var formattedDate = "";
	if (timestamp != undefined) {
		var months = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];

		// http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
		// Split timestamp into [ Y, M, D, h, m, s ]
		var t = timestamp.split(/[- :]/);
		// Apply each element to the Date function
		var dateObj = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
		// 		formattedDate = months[(dateObj.getMonth())] + " " + dateObj.getDate() + ", " + dateObj.getFullYear();
		// formattedDate = dateObj.getDate()+"-"+months[(dateObj.getMonth())]+"-"+ dateObj.getFullYear();
		// formattedDate = dateObj.toLocaleDateString()+" "+dateObj.toLocaleTimeString();
		var hour = dateObj.getHours();
		var ampm = hour > 12 ? "PM" : "AM"
		hour = hour > 12 ? hour - 12 : hour;
		var min = dateObj.getMinutes();
		min = min > 10 ? min : "0" + min;
		formattedDate = hour + ":" + min + ampm + " " + dateObj.getDay() + " " + months[(dateObj.getMonth())] + ", " + dateObj.getFullYear();
	}
	return formattedDate;
}

function secondsToMinutesAndSeconds(seconds) {
	var h = Math.floor(seconds / 3600);
	var m = Math.floor(seconds / 60);
	var s = Math.floor(seconds % 60);

	h = (h > 0) ? h + ":" : "";
	m = (m < 10) ? "0" + m : m;
	s = (s < 10) ? "0" + s : s;
	var retVal = h + m + ":" + s;

	return retVal;
}

function commaDelimitedToArray(commaDelimited) {
	//--remove extra spaces first
	var str = commaDelimited.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',');
	var arr = str.split(',');
	return arr;
}

//-----------------------------------------
//-- Annotations & Comments --
//-----------------------------------------
function getAllAnnotations() {
	annotations.splice(0, annotations.length);
	$.ajax({
		type: "GET",
		url: "/annotations",
		data: { course_id: course_id, group_id: group_id, video_id: video_id },
		success: function (data) {
			annotations = data.annotations.slice();
			if (data.structured_annotation_answers && data.structured_annotation_answers.length != 0) {
				structured_annotation_answers = JSON.parse(data.structured_annotation_answers);
			}
			layoutAnnotations();
			createStructuredAnnotationQuestionSheet()
			trackingInitial({ event: 'click', target: '#annotations-list .annotation-button', info: 'View an annotation' }, trackings);
		}
	});
}

function createStructuredAnnotationQuestionSheet() {
	if (structured_annotation_answers && structured_annotation_answers.length != 0) {
		const result_report = $('#structure-annotation-question-result');
		const tbody = result_report.find('tbody');

		structured_annotation_answers.forEach((e) => {
			const tr = $('<tr></tr>');
			tr.append(`<td>${e.type === 'text' ? "Short Answer" : "Multiple Choice"}</td>`);
			tr.append(`<td>${e.title}</td>`);
			tr.append(`<td>${e.user_ans}</td>`);
			if (e.user_ans === e.ans || e.user_ans === e.ans[0]) {
				tr.append(`<td style='text-align:center;'><img src='../../img/tick.png' alt='' style='width:32px; height:auto;'></td>`);
			} else {
				tr.append(`<td style='text-align:center;'><img src='../../img/cancel.png' alt='' style='width:32px; height:auto;'></td>`);
			}
			tr.append(`${e.type === "text" ? e.ans : e.feedback[e.list.indexOf(e.ans[0])]}`);
			tbody.append(tr);
		})

		result_report.show();
		return;
	}

	$('#structure-annotation-question-sheet').show();
	const $form = $('#structure-annotation-question-sheet form');
	const structured_annotations = annotations
		.filter((e) => e.is_structured_annotation === 1)
		.flatMap((e) => JSON.parse(e.description))

	structured_annotations.forEach((item, index) => {
		const $div = $('<div class="question"></div>');
		$div.append(`<h3>${index + 1}. ${item.title}</h3>`);

		if (item.type === 'multiple_choice') {
			item.list.forEach((option, idx) => {
				const optionPart = option.split(':');
				const $label = $(`<label><input type="radio" name="${index}" value="${option}">${optionPart[0]}. ${optionPart[1]}</label>`);
				$div.append($label);
			});
		} else if (item.type === 'text') {
			const $input = $(`<textarea rows="1" name="answer${index}" class="text-input">`);
			$div.append($input);
		}

		$form.append($div);

	});

	$('#structure-annotation-answer-submit').click(function () {
		const answer = $form.serializeArray();
		if (answer?.length !== structured_annotations.length) {
			showAlertDialog("You must answer all questions");
		}
		$('#structure-annotation-question-modal .modal-dialog').addClass('modal-loading');
		structured_annotations.forEach((e, idx) => {
			e['user_ans'] = answer[idx].value;
		})
		$.ajax({
			url: '/annotations/submit_structured_annotation',
			method: 'POST',
			data: { result: structured_annotations, group_video_id: window.group_video_id },
			success: function (response) {
				if (response.result === "success") {
					window.structured_annotation_answers = structured_annotations;
					$('#structure-annotation-question-sheet').hide();
					createStructuredAnnotationQuestionSheet();
				}
			},
			complete: function () {
				$('#structure-annotation-question-modal .modal-dialog').removeClass('modal-loading');
			}
		});
	});
}

function getComments() {
	comments = [];
	$.ajax({
		type: "GET",
		url: "/comments?group_video_id=" + group_video_id,
		success: function (data) {
			if (data) {
				comments = data.slice();
				$(".comments-box").html(formatComments());
				trackingInitial({ event: 'click', target: '.edit-comment-button', info: 'Edit comment' }, trackings);
			}
		},
		error: function (request, status, error) {
			console.log("home.js, getComments() ajax error: " + request.status + ", error: " + error + "<error>" + request.responseText + "</error>");
			if (status == "401") {
				window.location = "/logout";
			}
		},
		async: false
	});
}

function formatComments() {
	var html = "";	//return val
	if (comments == null || comments.length <= 0) {
		// Display a single, uneditable entry telling people to post something.
		html = "<div class=\"comment-text\">There is no comment for this video yet. You can add yours and review them later!</div>";

	} else {
		var list = "";
		$.each(comments, function (index, value) {
			var description = value.description;
			description = $("<div/>").text(unescapeHtml(description)).html();

			// var commentDate = dateStringFromSqlTimestamp(value.updated_at);
			var commentID = value.id;
			var divClass = (description.length > 100) ? " comment-summary" : "";

			html += "\n<div class=\"comment\">";
			html += "\n\t<div class=\"comment-header\">";
			if (value.is_mine) {
				html += "\n\t\t<button type=\"button\" id=\"" + commentID + "\" class=\"btn btn-link edit-comment-button\" title=\"Edit comment\"><i class=\"fa fa-pencil-square-o\"></i></button>";
			}
			html += "\n\t\t<div class=\"username\">" + value.name;
			if (value.by_instructor) {
				html += "<span class=\"instructor\">instructor</span>";
			}
			html += "</div>";
			if ((value.privacy === "private") || (value.privacy === "nominated")) {
				html += "\n\t\t<div class=\"privacy-icon\"><i class=\"fa fa-eye-slash\"></i></div>";
			}
			else if (value.privacy === "all") {
				html += "\n\t\t<div class=\"privacy-icon\"><i class=\"fa fa-eye\"></i></div>";
			}
			html += "\n\t\t<div class=\"date\">" + value.updated_at + "</div>";
			html += "\n\t\t<div class=\"tags\">";
			if (value.tags && value.tags.length > 0) {
				$.each(value.tags, function (i, v) {
					html += "\n\t\t\t<span class=\"tag comment-tag\">" + v + "</span>";
				});
			}
			html += "\n\t\t</div><!-- .tags -->";
			html += "\n\t</div><!-- .comment-header -->";
			html += "\n\t<div class=\"comment-text" + divClass + "\" id=\"comment-text-" + commentID + "\">";
			html += "\n\t\t" + description;
			html += "\n\t</div><!-- .comment-text -->";
			html += "\n</div><!-- .comment -->";
		});
	}
	return html;
}//end formatComments

function generateTrendline() {
	var canvas = document.getElementById("trends");
	if (canvas.getContext) {
		var ctx = canvas.getContext('2d');
		canvas.width = $("#annotations-list").width() - 2;
		canvas.height = 25;
		var y = 25;
		var w = $("#annotations-list").width();
		ctx.lineWidth = 3;
		ctx.strokeStyle = "#F9B200";
		ctx.beginPath();
		for (i = 0; i < annotations.length; i++) {
			var x = annotations[i].start_time / video_duration * w;
			x = (x == w) ? x - 1.5 : x;	///////dodgey fix.. so annotation at end of video shows
			ctx.moveTo(x, 0);
			ctx.lineTo(x, y);
		}
		ctx.stroke();
	}
	else {
		//--canvas isn't supported--
		console.log("canvas not supported");	///////////
	}
}//end generateTrendline

function adjustAnnotationsListDiv() {
	var annotations_list_width = $("#annotations").width();
	annotations_list_width = annotations_list_width > 550 ? annotations_list_width : 550;
	var max_y = 0;
	$(".annotation-icon").each(function () {
		max_y = Math.max($(this).position().top, max_y);
	});
	var annotations_list_height = max_y + $(".annotation-icon").height();

	annotations_list_height = (annotations_list_height > 70) ? annotations_list_height + 40 : 120;
	$("#annotations-list").height(annotations_list_height);
	// $("#annotations-list").width(annotations_list_width).height(annotations_list_height);
	$("#trends").width($("#annotations").width() - 3);
	// $("#trends").width(annotations_list_width-2);
	generateTrendline();
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

function saveTracking(record) {
	var data = [record];
	$.ajax({
		type: "POST",
		url: "/trackings",
		data: { data: data, group_video_id: group_video_id },
		success: function (data) {
		},
		error: function (request, status, error) {
			console.log("saveTracking error: " + error);	/////
		}
	});
}
// tracking events initial function

function trackingInitial(record, trackings) {
	$(record.target).on(record.event, function () {
		if (typeof record.info == 'function') {
			trackings.push({
				target: record.target,
				event: record.event,
				info: record.info(),
				video_time: window.exactCurrentVideoTime(),
				event_time: Date.now()
			});
		} else {
			trackings.push({
				target: record.target,
				event: record.event,
				info: record.info,
				video_time: window.exactCurrentVideoTime(),
				event_time: Date.now()
			});
		}

		if (trackings.length < 3) {
			window.localStorage.setItem('trackings', JSON.stringify(trackings));
		} else {
			var temp = trackings;
			trackings = [];
			$.ajax({
				type: "POST",
				url: "/trackings",
				data: { data: temp, group_video_id: group_video_id },
				success: function (data) {
					window.localStorage.setItem('trackings', JSON.stringify(trackings));
				},
				error: function (request, status, error) {
					trackings = trackings.concat(temp);
					window.localStorage.setItem('trackings', JSON.stringify(trackings));
				},
				async: false
			});
		}
	});
}

function compareY(a, b) {
	var retVal = 0;
	if (a.y > b.y) {
		retVal = 1;
	}
	else if (a.y < b.y) {
		retVal = -1;
	}
	return retVal;
}

function layoutAnnotations(mode) {
	if (!mode) {
		mode = ALL;
	}
	var anno_list = $("#annotations-list");
	if (anno_list.length == 0) {
		return;
	}

	if (annotations.length == 0) {
		var noAnnotationText = "<br/>There is no annotation for this video yet.<br/>Add annotations as you find points of interest so you can review them later!</br> ";
		anno_list.html("<div class=\"no-annotation\">" + noAnnotationText + "</div>");
	}
	else {
		var x = 0;
		var y = 0;
		var iconsize = 32;
		var placed = [];
		var actual_width = anno_list.width() - iconsize / 2;
		var paddingX = 7;

		anno_list.html("");
		$.each(annotations, function (i, a) {
			if ((mode == MINE && !a.mine) || (mode == STUDENTS && a.by_instructor) || (mode == INSTRUCTORS && !a.by_instructor)) {
				return 1;
			}

			var start_ratio = a.start_time / video_duration;
			x = Math.floor(actual_width * start_ratio) - paddingX;
			y = 0;
			$.each(placed, function (j, val) {
				if ((x >= val.x && x <= val.x + iconsize && y == val.y) || (x + iconsize >= val.x && x <= val.x + iconsize && y == val.y)) {
					y += iconsize;
				}
			});
			placed.push({ x: x, y: y });
			placed.sort(compareY);
			var style = "left:" + x + "px; top:" + y + "px;";
			var icon_tag;
			if (a.mine) {
				icon_tag = '<i class="fa fa-dot-circle-o" aria-hidden="true"></i>';
			}
			else if (a.by_instructor) {
				icon_tag = '<i class="fa fa-circle" aria-hidden="true"></i>';
			}
			else {
				icon_tag = '<i class="fa fa-circle-o" aria-hidden="true"></i>';
			}
			const annotation_node = `
				<div class="annotation-icon" style="${style}">
					<button type="button" class="btn btn-link annotation-button" data-id="${a.id}">
						${icon_tag}
					</button>
				</div>
			`;

			anno_list.append(annotation_node);
		});
		adjustAnnotationsListDiv();
	}
}

function getNominatedStudentList(itemType, itemId) {
	if (!itemId) {
		populateNominatedStudentList(null);
	}
	else {
		$.ajax({
			type: "POST",
			url: "/get_nominated_students_ids",
			data: { item: itemType, item_id: itemId },
			success: function (data) {
				populateNominatedStudentList(data.nominated);
			},
			error: function (request, status, error) {
				console.log("error get_nominated_students_ids: " + error);	///////
			}
		});
	}
}

function populateNominatedStudentList(nominated) {
	var list = $("#nominated-students-list");
	list.html("");
	list.append('<option value="" disabled></option>');
	var selectedItems = [];
	$.each(group_members, function (i, member) {
		list.append('<option value="' + member.id + '">' + member.name + '</option>');
		if (nominated && nominated.length > 0) {

			if ($.inArray(member.id, nominated) != -1) {
				selectedItems.push(member.id);
			}
		}
	});
	list.val(selectedItems);
	list.multiSelect('refresh');

}

function saveFeedbacksAndConfidenceLevel(comment) {
	item = comment;
	var level = $("select[name=confidence-level]").val();
	var confidence_level = level > 0 ? $("select[name=confidence-level]").val() : 0;
	var answers = [];
	$('input[id^="point"]').each(function () {
		var a = new Object();
		var i = $(this).attr('id');
		var regex = /point([0-9]*)-(yes)?(no)?/g;
		var point_id = regex.exec(i)[1];
		a['point_id'] = point_id;
		var b = $(this).prop('checked');
		a['answer'] = b ? 1 : 0;
		answers.push(a);
	});
	$.ajax({
		type: "POST",
		url: "/save_feedback",
		data: { comment_id: item.id, answers: answers, confidence_level: confidence_level },
		async: false
	});
}

//-----------------------------------------
//-- on ready --
//-----------------------------------------
$(document).ready(
	function () {
		var modal = $("#annotation-modal");		//DOM for modal-form
		var item_start_time = null;					//start_time used in modal-form
		var item_start_time_text = null;			//human readable start_time text used in modal-form
		var item = null;				//annotation or comment item used in modal-form
		modal.find('.anno-dynamic-content').addClass('hidden'); // control dynamic annotation content visibility

		getAllAnnotations();
		getComments();
		window.addEventListener('beforeunload', function () {
			if (window.getVideoState('started')) {
				saveTracking({
					event: "Quit",
					target: null, info: null,
					video_time: window.exactCurrentVideoTime(),
					event_time: Date.now()
				})
			}
		});
		$('#structured-annotation-quiz-btn').on("click", () => {
			if (!annotations.some(e => e.is_structured_annotation === 1)) {
				showAlertDialog("No annotation quizzes available");
				return;
			}
			$('#structure-annotation-question-modal').modal('show');
		})

		$("#course-name").text(course_name);
		$("#group-name").text(group_name);
		$("#video-name").text(unescapeHtml(video_name));

		$("#right-side").height($("#left-side").height());
		$(".comments-box").height($("#left-side").height() - $("#related-videos").height() - $("#comments .header").height());

		$(window).resize(function () {
			layoutAnnotations();
			$("#right-side").height($("#left-side").height());
			$(".comments-box").height($("#left-side").height() - $("#related-videos").height() - $("#comments .header").height());
		});

		$(".vertical-scroll").niceScroll({ cursorcolor: "#585858", cursorborder: "1px solid transparent", autohidemode: "true" });
		$(".horizontal-scroll").niceScroll({ cursorcolor: "#585858", cursorborder: "1px solid transparent", autohidemode: "true" });

		$(".navmenu").on("shown.bs.offcanvas", function () {
			$(".comments-box").niceScroll().resize();
		});
		$(".navmenu").on("hidden.bs.offcanvas", function () {
			$(".comments-box").niceScroll().resize();
		});

		$(document).on("click", ".comment-summary", function () {
			$(this).removeClass('comment-summary');
			$(this).addClass("comment-full");
		});
		$(document).on("click", ".comment-full", function () {
			$(this).removeClass("comment-full");
			$(this).addClass("comment-summary");
		});

		/*$("#search-box").on("submit", function(e) {
			e.preventDefault();
			console.log("Search");		//////
			var terms = $("#search-term").val();
			console.log("search terms = \""+terms+"\"");		////////
			var byContent = $("#content").is(":checked");
			var byAuthor = $("#author").is(":checked");
			var byTag = $("#tag").is(":checked");
			var autoSearch = $("#auto").is(":checked");
			console.log("options = content("+byContent+") autor("+byAuthor+") tag("+byTag+") auto("+autoSearch+")");		///////
			// todo: implement search method /////////////////

		}); */

		const textInputMode = modal.find('#anno-text-mode-input');
		const questionInputMode = modal.find('#anno-question-mode-input');
		const toggleInputModeSwitch = modal.find('#toggle-anno-question-mode-switch');
		toggleInputModeSwitch.on('change', function (e) {
			if (e.target.checked) {
				textInputMode.hide();
				questionInputMode.show();
			} else {
				textInputMode.show();
				questionInputMode.hide();
			}
		});

		$(".add-annotation").on("click", function (e) {
			e.preventDefault();
			item = null;
			item_start_time = currentVideoTime();	//~~

			var show = true;
			for (i = 0; i < annotations.length; i++) {
				var mine = annotations[i].mine;
				var start = annotations[i].start_time;
				if (mine && (start == item_start_time)) {
					alert("You already have annotation at " + start + " seconds. Please edit it instead.");
					show = false;
					break;
				}
			}
			if (show) {
				modal.find("#modalLabel").text(window.Oval.currentGroupVideo.annotation_config.header_name);
				item_start_time_text = secondsToMinutesAndSeconds(item_start_time);
				modal.find("#time-label").html(item_start_time_text);
				modal.find(".edit-annotation-time").show();
				modal.find(".meta-data").hide();
				modal.find(".edit-instruction").hide();
				modal.find("#annotation-instruction").hide();

				if (window.Oval.currentGroupVideo.show_annotations === 0) {
					modal.find('#annotation-visibility-form').css('display', 'none');
					modal.find("input[name='privacy-radio'][value='private']").prop("checked", true);
				} else {
					modal.find("input[name='privacy-radio'][value='all']").prop("checked", true);
				}

				modal.find("#nominated-selection").hide();
				modal.find(".anno-dynamic-content").removeClass('hidden');
				toggleInputModeSwitch.trigger('change');
				modal.modal("show");
			}
		});
		$(".add-comment").on("click", function (e) {
			e.preventDefault();
			item = null;
			item_start_time = null;
			modal.find("#modalLabel").text("ADD COMMENT");
			if (is_instructor) {
				modal.find(".edit-instruction").show();
			}
			else {
				modal.find(".edit-instruction").hide();
			}
			modal.find(".edit-annotation-time").hide();
			modal.find(".meta-data").hide();
			if (comment_instruction) {
				modal.find("#annotation-instruction").text(unescapeHtml(comment_instruction));
				modal.find("#annotation-instruction").show();
			}
			else {
				modal.find("#annotation-instruction").hide();
			}
			modal.find("input[name='privacy-radio'][value='all']").prop("checked", true);
			modal.find("#nominated-selection").hide();
			$("#annotation-modal").modal("show");
		});
		$(".play-annotation-button").on("click", function (e) {
			e.preventDefault();
			var startTime = $("#preview .time-label").prop('start-time');
			goTo(startTime);
		});
		$(".edit-annotation-button").on("click", function (e) {
			e.preventDefault();
			if ($("#preview").is(":visible")) {
				$("#preview").hide();
			}
			modal.find("#modalLabel").text("EDIT ANNOTATION");
			var annotation_id = $(this).attr("id");
			var match = $.grep(annotations, function (e) { return e.id == annotation_id; });
			item = null;
			if (match.length == 1) {
				item = match[0];
			}
			item_start_time = item.start_time;
			item_start_time_text = secondsToMinutesAndSeconds(item.start_time);
			modal.find("#time-label").html(item_start_time_text);

			if (item.is_structured_annotation) {
				try {
					window.quiz_obj.items = JSON.parse(item.description);
				} catch (error) { }
				toggleInputModeSwitch.prop("checked", true);
			} else {
				$("#annotation-description").val(unescapeHtml(item.description));
			}

			modal.find(".username").text(item.name);
			modal.find("#annotation-instruction").hide();
			modal.find(".edit-instruction").hide();
			modal.find(".anno-dynamic-content").removeClass('hidden');
			toggleInputModeSwitch.trigger('change');

			if (item.privacy == "private" || item.privacy == "nominated") {
				modal.find(".privacy-icon").html("<i class=\"fa fa-eye\"></i>");
			}
			else {
				modal.find(".privacy-icon").html("<i class=\"fa fa-eye-slash\"></i>");
			}
			if (item.privacy == "nominated") {
				getNominatedStudentList("annotation", annotation_id);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
			annotationDate = item.date;
			modal.find(".date").html(annotationDate);
			modal.find(".edit-annotation-time").show();
			modal.find(".meta-data").show();
			var tags = "";

			if (item.tags.length > 0) {
				$.each(item.tags, function (i, val) {
					tags += unescapeHtml(val) + ", ";
				});
				tags = tags.slice(0, -2);
			}
			$("#tags").val(tags);
			modal.find("input[name='privacy-radio'][value='" + item.privacy + "']").prop("checked", true);
			if (item.privacy == "nominated") {
				getNominatedStudentList("annotation", annotation_id);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
			$("#annotation-modal").modal("show");
		});
		$(".comments-box").on("click", ".edit-comment-button", function (e) {
			e.preventDefault();

			modal.find("#modalLabel").text("EDIT COMMENT");
			var comment_id = $(this).attr("id");
			var match = $.grep(comments, function (e) { return e.id == comment_id; });
			item = null;
			if (match.length == 1) {
				item = match[0];
			}
			$("#annotation-description").val(unescapeHtml(item.description));
			if (item.tags.length > 0) {
				var t = "";
				$.each(item.tags, function (i, v) {
					t += unescapeHtml(v) + ", ";
				});
				t = t.slice(0, -2)
				modal.find("#tags").val(t);
			}
			else {
				$("#tags").val("");
			}
			if (is_instructor) {
				modal.find(".edit-instruction").show();
			}
			else {
				modal.find(".edit-instruction").hide();
			}
			modal.find(".edit-annotation-time").hide();
			modal.find(".meta-data").hide();
			if (comment_instruction) {
				modal.find("#annotation-instruction").text(unescapeHtml(comment_instruction));
				modal.find("#annotation-instruction").show();
			}
			else {
				modal.find("#annotation-instruction").hide();
			}

			modal.find("input[name='privacy-radio'][value='" + item.privacy + "']").prop("checked", true);
			if (item.privacy == "nominated") {
				getNominatedStudentList("comment", comment_id);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
			$("#annotation-modal").modal("show");

		});

		$("#annotation-modal").on("show.bs.modal", function (e) {
			pauseVideo();
			$("#annotation-description").niceScroll({ cursorcolor: "#585858", cursorborder: "1px solid transparent", autohidemode: "false" });
			$("#nominated-students-list").multiSelect({
				selectableHeader: "<div class=''>Available</div>",
				selectionHeader: "<div class=''>Nominated</div>"
			});
			modal.find("#annotation-form").validator("destroy");
			modal.find("#annotation-form").validator("update");

			var save_button = modal.find("#save");
			var title = modal.find("#modalLabel").text();
			if (points.length > 0) {
				if (title === "ADD COMMENT" || title === "EDIT COMMENT") {
					save_button.addClass("modal-text-button");
					save_button.html('Next<i class="fa fa-chevron-right" aria-hidden="true"></i>');
				}
				else {
					save_button.removeClass("modal-text-button");
					save_button.html('<i class="fa fa-save" aria-hidden="true"></i>');
				}
			}

		});

		$("#annotation-modal").on("hidden.bs.modal", function () {
			if (window.getVideoState('paused')) {
				playVideo();
			}
			modal.find('.anno-dynamic-content').addClass('hidden');
			toggleInputModeSwitch.prop("checked", false);
			toggleInputModeSwitch.trigger("change");
		});


		modal.on("change", "input[name=privacy-radio]", function () {
			type = "";
			if ($("#modalLabel:contains('ANNOTATION')").length > 0) {
				type = "annotation";
			}
			else if ($("#modalLabel:contains('COMMENT')").length > 0) {
				type = "comment";
			}
			if ($(this).val() === "nominated") {
				var itemId = item ? item.id : null;
				getNominatedStudentList(type, itemId);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
			modal.find("#annotation-form").validator("destroy");
			modal.find("#annotation-form").validator("update");
		});

		modal.on("change", "input[name=privacy-radio]", function () {
			type = "";
			if ($("#modalLabel:contains('ANNOTATION')").length > 0) {
				type = "annotation";
			}
			else if ($("#modalLabel:contains('COMMENT')").length > 0) {
				type = "comment";
			}
			if ($(this).val() === "nominated") {
				getNominatedStudentList(type, item.id);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
		});

		modal.on("click", "#save", function () {
			var tags_string = $('#tags').val();
			var description = $('#annotation-description').val();
			var privacy = $('input[name="privacy-radio"]:checked', '#annotation-form').val();
			var title = $("#modalLabel").text();
			var nominated = null;
			const is_structured_annotation = modal.find('#toggle-anno-question-mode-switch').is(':checked');

			modal.find("#annotation-form").validator('validate');
			if (!is_structured_annotation && modal.find("#annotation-form").find('.has-error').length) {
				if ((modal.find("#annotation-description").data('bs.validator.errors').length > 0)
					|| ((privacy === "nominated") && modal.find("#nominated-students-list").data('bs.validator.errors').length > 0)) {
					return false;
				}
			}

			if (privacy === "nominated") {
				nominated = $("#nominated-students-list").val();
			}

			var tags = commaDelimitedToArray(tags_string);

			if (title === window.Oval.currentGroupVideo.annotation_config.header_name) {
				if (is_structured_annotation) {
					if (!window.quiz_obj?.items || window.quiz_obj.items.length === 0) {
						$("#alert_dialog_content").empty();

						var content = "<h3>" + "There is no question in th question list" + "</h3>";
						$("#alert_dialog_content").append(content);

						$("#alert_dialog").modal({
							backdrop: 'static',
							keyboard: false
						});

						return;
					}
				}

				let data = {
					group_video_id: group_video_id,
					start_time: item_start_time,
					tags: tags,
					description: is_structured_annotation ? JSON.stringify(window.quiz_obj.items) : description,
					privacy: privacy,
					nominated_students_ids: nominated,
					video_time: window.exactCurrentVideoTime(), // for tracking
				};
				data['is_structured_annotation'] = is_structured_annotation;

				$.ajax({
					type: "POST",
					url: "/annotations",
					data: data,
					success: function (data) {
						modal.modal("hide");
						getAllAnnotations();
						window.quiz_obj.items = [];
						$(".question_warp ul ul").empty();
					},
					async: false
				});
			}
			else if (title === "ADD COMMENT") {
				$.ajax({
					type: "POST",
					url: "/comments",
					beforeSend: function (request) {
						request.setRequestHeader("Authorization", "Bearer " + api_token);
					},
					data: {
						group_video_id: group_video_id,
						tags: tags,
						description: description,
						privacy: privacy,
						nominated_students_ids: nominated,
						video_time: window.exactCurrentVideoTime(), // for tracking
					},
					success: function (data) {
						if (points.length > 0) {
							modal.modal('hide');
							item = data;
							$("#feedback").modal('show');
						}
						else {
							modal.modal("hide");
						}
						getComments();
					},
					async: false
				});
			}
			else if (title === "EDIT ANNOTATION") {
				if (is_structured_annotation) {
					if (!window.quiz_obj?.items || window.quiz_obj.items.length === 0) {
						$("#alert_dialog_content").empty();

						var content = "<h3>" + "There is no question in th question list" + "</h3>";
						$("#alert_dialog_content").append(content);

						$("#alert_dialog").modal({
							backdrop: 'static',
							keyboard: false
						});

						return;
					}
				}

				let data = {
					start_time: item.start_time,
					tags: tags,
					description: is_structured_annotation ? JSON.stringify(window.quiz_obj.items) : description,
					privacy: privacy,
					nominated_students_ids: nominated,
					video_time: window.exactCurrentVideoTime(), // for tracking
				}
				data['is_structured_annotation'] = is_structured_annotation;

				$.ajax({
					type: "PUT",
					url: "/annotations/" + item.id,
					data: data,
					success: function (data) {
						modal.modal("hide");
						getAllAnnotations();
						window.quiz_obj.items = [];
						$(".question_warp ul ul").empty();
					},
					async: false
				});
			}
			else if (title === "EDIT COMMENT") {
				$.ajax({
					type: "PUT",
					url: "/comments/" + item.id,
					beforeSend: function (request) {
						request.setRequestHeader("Authorization", "Bearer " + api_token);
					},
					data: {
						tags: tags,
						description: description,
						privacy: privacy,
						nominated_students_ids: nominated,
						video_time: window.exactCurrentVideoTime(), // for tracking
					},
					success: function (data) {
						if (points.length > 0) {
							modal.modal('hide');
							item = data;
							$("#feedback").modal('show');
						}
						else {
							modal.modal("hide");
						}
						getComments();
					},
					async: false
				});
			}
		});
		modal.on("click", "#delete", function () {
			var title = $("#modalLabel").text();
			if ((title === window.Oval.currentGroupVideo.annotation_config.header_name) || (title === "ADD COMMENT")) {
				$("#annotation-modal .close").click();
				return;
			}
			else {
				if (confirm("Are you sure you want to delete?")) {
					if (title === "EDIT ANNOTATION") {
						$.ajax({
							type: "DELETE",
							url: "/annotations/" + item.id + "?video_time=" + window.exactCurrentVideoTime(),
							success: function (data) {
								modal.modal("hide");
								// getAnnotations(ALL);
								getAllAnnotations();
							},
							async: false
						});
					}
					else if (title === "EDIT COMMENT") {
						$.ajax({
							type: "DELETE",
							url: "/comments/" + item.id + "?video_time=" + window.exactCurrentVideoTime(),
							success: function (data) {
								getComments();
								modal.modal("hide");
							},
							async: false
						});
					}
				}
			}
		});
		modal.on("click", "#rewind-button", function () {
			item_start_time = (item_start_time > 1) ? item_start_time - 1 : item_start_time;
			modal.find("#time-label").html(secondsToMinutesAndSeconds(item_start_time));
		});
		modal.on("click", "#forward-button", function () {
			item_start_time = (item_start_time + 1 < video_duration) ? item_start_time + 1 : video_duration;
			modal.find("#time-label").html(secondsToMinutesAndSeconds(item_start_time));
		});
		modal.on("click", "#edit-instruction-button", function () {
			modal.modal("hide");
			$("#comment-instruction-modal").modal("show");
		});

		modal.on("hidden.bs.modal", function () {
			var scrollbar = $("#annotation-description").getNiceScroll().hide();
			var modal = $(this);
			item_start_time = null;
			item_start_time_text = null;
			modal.find("#tags").val("");
			modal.find("#annotation-description").val("");
			modal.find(".privacy-icon").html("");
			modal.find("#annotation-form").validator("destroy");
		});
		$("#comment-instruction-modal").on("show.bs.modal", function () {
			if (comment_instruction) {
				$("#comment-instruction-description").val(comment_instruction);
			}
		});
		$("#comment-instruction-modal").on("click", "#save-comment-instruction", function () {
			var description = $('#comment-instruction-description').val();
			$.ajax({
				type: "POST",
				url: "/comment_instructions",
				data: { group_video_id: group_video_id, description: description },
				success: function (data) {
					comment_instruction = data;
					$("#comment-instruction-modal").modal('hide');
				},
				erorr: function (request, status, error) {
					console.log("error edit_comment_instruction : " + error);	//////
				}
			});
		});
		$("#comment-instruction-modal").on("click", "#delete-comment-instruction", function () {
			if (!comment_instruction) {
				// $("#comment-instruction-modal .close").click();
				$("#comment-instruction-modal").modal('hide');
				return;
			}
			if (confirm("Are you sure you want to delete this instruction?")) {
				$.ajax({
					type: "DELETE",
					url: "/comment_instructions/" + group_video_id,
					success: function (data) {
						comment_instruction = null;
						$("#comment-instruction-modal").modal('hide');
					},
					erorr: function (request, status, error) {
						console.log("error delete_comment_instruction : " + error);	//////
					}
				});
			}
		});

		$("#feedback").on("show.bs.modal", function () {
			$("#point-instruction").text(unescapeHtml(point_instruction));
			var div = $("#feedback-content");
			for (i = 0; i < points.length; i++) {
				var radio_div = $('<div/>', {
					'class': '',
				}).appendTo(div);
				$('<label/>', {
					'for': 'point' + points[i].id + '-yes',
					'html': '<span class="left-indent circle-radio">' + points[i].description + '</span>'
				}).prepend(
					$('<input/>', {
						'type': 'checkbox',
						'name': 'point' + points[i].id + '-feedback',
						'id': 'point' + points[i].id + '-yes',
						'class': 'checkbox pull-right',
						'value': '1'
					})
				).appendTo(div);
			}//end for
		});
		$("#feedback").on("hide.bs.modal", function () {
			$("#feedback-content").empty();
			$("#confidence-level option:eq(0)").prop("selected", true);
		});
		$("#feedback").on("click", "#re-enter-comment", function () {
			saveFeedbacksAndConfidenceLevel(item);

			modal.find("#modalLabel").text("EDIT COMMENT");
			$("#annotation-description").val(unescapeHtml(item.description));
			var tags = "";
			if (item.tags.length > 0) {
				tags = unescapeHtml(item.tags[0]);
				var i;
				for (i = 1; i < item.tags.length; i++) {
					tags += ", " + unescapeHtml(item.tags[i]);
				}
			}
			modal.find("#tags").val(tags);

			if (is_instructor) {
				modal.find(".edit-instruction").show();
			}
			else {
				modal.find(".edit-instruction").hide();
			}
			modal.find(".edit-annotation-time").hide();
			modal.find(".meta-data").hide();
			if (comment_instruction) {
				modal.find("#annotation-instruction").text(unescapeHtml(comment_instruction));
				modal.find("#annotation-instruction").show();
			}
			else {
				modal.find("#annotation-instruction").hide();
			}

			modal.find("input[name='privacy-radio'][value='" + item.privacy + "']").prop("checked", true);
			if (item.privacy == "nominated") {
				getNominatedStudentList("comment", comment_id);
				modal.find("#nominated-selection").show();
			}
			else {
				populateNominatedStudentList(null);
				modal.find("#nominated-selection").hide();
			}
			$("#feedback").modal("hide");
			$("#annotation-modal").modal("show");
		});

		$("#feedback").on("click", "#save-points", function () {
			saveFeedbacksAndConfidenceLevel(item);
			$("#feedback").modal("hide");
			getComments();
		});
		$("#annotation-filter input").on("change", function (e) {
			var mode = $('input[name=filter]:checked').val();
			layoutAnnotations(parseInt(mode));
		});
		$('#annotations-list').on('click', '.annotation-button', function (e) {
			// 			saveTracking({event: "click", target: '.annotation-button', info: 'View an annotation', event_time: Date.now()});

			var preview = $("#preview");
			if (preview.is(':visible')) {
				preview.hide();
			}
			var annotationID = $(this).data('id');
			var matches = $.grep(annotations, function (e) { return e.id == annotationID; });
			var annotation = matches[0];
			var startTime = annotation.start_time;
			var userName = annotation.name;
			// var tags = "";
			// $.each(annotation.tags, function(i, val) {
			// 	tags += unescapeHtml(val)+",";
			// });
			// tags = tags.slice(0,-1);

			let description = "";
			if (annotation.is_structured_annotation === 1) {
				try {
					const structured_annotation = JSON.parse(annotation.description);

					const $preview = preview.find(".preview-comment");
					$preview.empty();

					structured_annotation.forEach((e, index) => {
						const content = `<div><b>type:</b> ${e.type} | <b>answer:</b> ${e.ans}</div>`;
						$preview.append(content);
						if (index !== structured_annotation.length - 1) {
							$preview.append('<hr style="height: 1px; background-color: red;">');
						}
					});
				} catch (e) {
					preview.find(".preview-comment").text("");
				}
			} else {
				preview.find(".preview-comment").text(unescapeHtml(annotation.description));
			}

			var creationDate = annotation.date;
			var privacyIcon = annotation.privacy === "all" ? "<i class=\"fa fa-eye\"></i>" : "<i class=\"fa fa-eye-slash\"></i>";

			preview.find(".time-label").prop('start-time', startTime);
			preview.find(".time-label").text(secondsToMinutesAndSeconds(startTime));
			preview.find(".privacy-icon").html(privacyIcon);
			preview.find(".username").text(userName);
			preview.find(".date").text(creationDate);
			// preview.find(".preview-tags").text(unescapeHtml(tags));
			preview.find(".preview-tags").html("");
			$.each(annotation.tags, function (i, val) {
				preview.find(".preview-tags").append('<span class="tag annotation-tag">' + val + "</span> ")
			});
			if (annotation.mine) {
				preview.find(".edit-annotation-button").attr("id", annotationID);
				preview.find(".edit-annotation-button").show();
			}
			else {
				preview.find(".edit-annotation-button").hide();
			}

			var posX = e.pageX + previewOffsetX;
			var posY = e.pageY + previewOffsetY;
			var previewWidth = preview.width();
			var windowWidth = $(window).width();
			if (posX + previewWidth > windowWidth) {
				posX = windowWidth - previewWidth;
			}
			preview.css({ 'top': posY + "px", 'left': posX + "px" });
			var $footer = $(".footer");
			if ($footer.length > 0) {
				var $footerContainer = $footer.parent();
				var windowBottom = $footerContainer.position().top + $footer.height();
				var previewBottom = posY + preview.height();
				if (windowBottom < previewBottom) {
					$(".canvas").height(previewBottom);
				}
			}
			preview.show();
		});

		$("#close-preview-button").click(function (e) {
			$("#preview").hide();
		});

		$(document).on("click", function (e) {
			if ($("#preview").is(":visible")) {
				var target = $(e.target);
				if (!target.hasClass("annotation-button") && !target.hasClass("fa-dot-circle-o") && !target.hasClass("fa-circle") && !target.hasClass("fa-circle-o")) {
					if (target.id != "preview" && !$('#preview').find(e.target).length) {
						/*------ trigger close click to record ------*/
						//$("#preview").hide();
						$("#close-preview-button").trigger("click");
					}
				}
			}
		});
		if (text_analysis && text_analysis.length > 0) {
			function populateRelatedResources(searchTermArray) {
				console.log("populateRelatedResoruces - " + searchTermArray);	/////
				console.table(searchTermArray);/////

				var related_ul = $("#related-ul");
				related_ul.html("");
				var html = "";

				var temp_url_arr = [];
				$.each(searchTermArray, function (index, searchTerm) {
					$.each(text_analysis, function (i, word) {
						if ((word.text.indexOf(searchTerm) == 0) && (word['related'])) {
							var trigger = 0;

							$.each(word['related'], function (j, related) {
								// html = '"'+word.text+'" in <span class="video-link">'+related.title+'</span> <span class="related-time">@'+secondsToMinutesAndSeconds(related.time)+'</span>';
								// $('<li/>', {
								// 	// class: 'video-link',
								// 	html: html,
								// 	"data-url": related.url,
								// 	"data-time-url": related.time_url,
								// 	"data-time": related.time
								// }).appendTo(related_ul);

								if (trigger === 0) {
									if (temp_url_arr.length === 0) {
										temp_url_arr.push(related.url);
										html = '<span class="video-link">' + related.title + '</span>';
										$('<li/>', {
											// class: 'video-link',
											html: html,
											"data-url": related.url,
											// "data-time-url": related.time_url,
											"data-time": related.time
										}).appendTo(related_ul);
									} else {
										if (temp_url_arr.indexOf(related.url) === -1) {
											temp_url_arr.push(related.url);
											html = '<span class="video-link">' + related.title + '</span>';
											$('<li/>', {
												// class: 'video-link',
												html: html,
												"data-url": related.url,
												// "data-time-url": related.time_url,
												"data-time": related.time
											}).appendTo(related_ul);
										}
									}
								}
								trigger++;
							})
						}
					});
				});

				if (related_ul.children().length == 0) {
					$("#no-links-msg").text("There are no related videos.");
					$("#no-links-msg").show();
				}
				else {
					$("#no-links-msg").hide();
				}
				$("#related-links").getNiceScroll().resize();
			}//end populateRelatedResources

			$(".no-keyword-msg").text("");
			var keyword_ul = $("#keyword-ul");
			$.each(text_analysis, function (i, word) {
				var html = word['text'];
				if (word['occurrences']) {
					$.each(word['occurrences'], function (j, val) {
						html += '<span class="related-time" data-time="' + val + '">@' + secondsToMinutesAndSeconds(val) + '</span>';
					});
					$("<li/>", {
						html: html
					}).appendTo(keyword_ul);
				}
			});
			$("#keyword-list").getNiceScroll().resize();

			populateRelatedResources(['']);


			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var target = $(e.target).attr("href");
				if (target === "#content-analysis") {
					$("#keyword-list").getNiceScroll().resize();
				}
				else if (target === "#annotations") {
					$("#keyword-list").getNiceScroll().hide();
				}
				else if (target === "#comments") {
					$(".comments-box").getNiceScroll().show();
					$("#related-links").getNiceScroll().hide();
				}
				else if (target === "#related-videos") {
					$(".comments-box").getNiceScroll().hide();
					$("#related-links").getNiceScroll().resize();
				}
			});


			$("#topic-search-textbox").autoComplete({
				minChars: 1,
				source: function (term, suggest) {
					term = term.toLowerCase();
					var choices = [];
					$.each(text_analysis, function (i, v) {
						choices.push(v.text);
					});
					var matches = [];
					for (i = 0; i < choices.length; i++)
						// if (~choices[i].toLowerCase().indexOf(term)) {
						if (choices[i].toLowerCase().indexOf(term) == 0) {
							matches.push(choices[i]);
						}
					suggest(matches);
				},
				onSelect: function (e, term, item) {
					populateRelatedResources([term]);
				},
				menuClass: 'auto-complete-list'
			});
			$("#topic-search-textbox").on("keyup", function () {
				if (!$(this).val()) {
					populateRelatedResources(['']);
				}
				else {
					populateRelatedResources([$("#topic-search-textbox").val()]);
				}
			});
			$("#topic-search-button").on("click", function () {
				populateRelatedResources([$("#topic-search-textbox").val()]);
			});
			$("#topic-search-form").on("submit", function (e) {
				e.preventDefault();
				populateRelatedResources([$("#topic-search-textbox").val()]);
			});
		}
		else {
			$(".no-keyword-msg").text("There are no keywords.");
			$("#related-links").text("There are no related videos.");
		}

		// $(".video-link").on("click", function(e) {
		// 	pauseVideo();
		// 	$("#modal-iframe").attr('src', $(this).data("url"));
		// 	$("#video-modal").modal('show');
		// });
		$("#related-videos").on("click", ".video-link", function () {
			pauseVideo();
			$("#modal-iframe").attr('src', $(this).parent().data("url"));
			$("#video-modal").modal('show');
		});
		$("#related-videos").on("click", ".related-time", function () {
			pauseVideo();
			$("#modal-iframe").attr('src', $(this).parent().data("time-url"));
			$("#video-modal").modal('show');
		});
		$("#video-modal").on('show.bs.modal', function () {

		});
		$("#video-modal").on('hide.bs.modal', function () {
			$("#modal-iframe").attr('src', '');
		});
		$("#keyword-ul").on("click", ".related-time", function () {
			var time = $(this).data("time");
			goTo(time);
		});
		$("#comments").on("click", ".comment-tag", function () {
			var tag = $(this).text();
			$.ajax({
				type: "GET",
				url: "/comments/tag?tag=" + tag + "&group_video_id=" + group_video_id,
				success: function (data) {
					var tag_modal = $("#same-tag-modal");
					tag_modal.find("#same-tag-modal-title").text('COMMENTS WITH TAG "' + tag + '"');
					var body = tag_modal.find(".modal-body");
					body.html("");
					var privacy_icon = "";
					if (data.privacy === "all") {
						privacy_icon = '<i class="fa fa-eye"></i>';
					}
					else {
						privacy_icon = '<i class="fa fa-eye-slash"></i>';
					}
					$.each(data, function (i, v) {
						var comment_div = $("<div/>", {
							'class': 'comment'
						}).appendTo(body);
						var comment_header = $("<div/>", {
							'class': 'comment-header',
						}).appendTo(comment_div);
						$("<div/>", {
							'class': 'username',
							'html': v.name
						}).appendTo(comment_header);
						$("<div/>", {
							'class': 'privacy-icon',
							'html': privacy_icon
						}).appendTo(comment_header);
						$("<div/>", {
							'class': 'date',
							'html': v.updated_at
						}).appendTo(comment_header);

						$('<div/>', {
							'class': 'comment-text',
							'html': v.description
						}).appendTo(comment_div);
					});
					tag_modal.modal("show");
				}
			});
		});
		$("#preview").on("click", ".annotation-tag", function () {
			var tag = $(this).text();
			$.ajax({
				type: "GET",
				url: "/annotations/tag?tag=" + tag + "&group_video_id=" + group_video_id,
				success: function (data) {
					var tag_modal = $("#same-tag-modal");
					tag_modal.find("#same-tag-modal-title").text('ANNOTATIONS WITH TAG "' + tag + '"');
					var body = tag_modal.find(".modal-body");
					body.html("");
					var privacy_icon = "";
					if (data.privacy === "all") {
						privacy_icon = '<i class="fa fa-eye"></i>';
					}
					else {
						privacy_icon = '<i class="fa fa-eye-slash"></i>';
					}
					$.each(data, function (i, v) {
						var comment_div = $("<div/>", {
							'class': 'comment'
						}).appendTo(body);
						var comment_header = $("<div/>", {
							'class': 'comment-header',
						}).appendTo(comment_div);
						$("<div/>", {
							'class': 'username',
							'html': v.name
						}).appendTo(comment_header);
						$("<div/>", {
							'class': 'privacy-icon',
							'html': privacy_icon
						}).appendTo(comment_header);
						$("<div/>", {
							'class': 'date',
							'html': v.updated_at
						}).appendTo(comment_header);
						$("<div/>", {
							'class': 'video-time',
							'html': v.start_time
						}).appendTo(comment_header);

						$('<div/>', {
							'class': 'comment-text',
							'html': v.description
						}).appendTo(comment_div);
					});
					$("#preview").hide();
					tag_modal.modal("show");
				}
			});
		});

		//Tracking all meaningful events
		var trackingsArr = [
			{ event: 'click', target: '.add-annotation', info: 'Add Annotation' },
			{ event: 'click', target: '.download-comments', info: 'Download Annotations' },
			{ event: 'click', target: '#rewind-button', info: 'Edit annotation time (back)' },
			{ event: 'click', target: '#forward-button', info: 'Edit annotation time (forward)' },
			{ event: 'click', target: '#private', info: function () { return 'Set ' + $("#modalLabel").text().split(' ')[1].toLowerCase() + ' to private' } },
			{ event: 'click', target: '#public', info: function () { return 'Set ' + $("#modalLabel").text().split(' ')[1].toLowerCase() + ' to public' } },
			// { event: 'click', target: '#save', info: function () { return 'Save ' + $("#modalLabel").text().split(' ')[1].toLowerCase() } },
			// { event: 'click', target: '#delete', info: function () { return 'Delete ' + $("#modalLabel").text().split(' ')[1].toLowerCase() } },
			{ event: 'click', target: '#annotation-modal .close', info: 'Close modal' },
			{ event: 'click', target: '#annotations-list .annotation-button', info: 'View an annotation' },
			{ event: 'click', target: '.play-annotation-button', info: 'Play from annotation point' },
			{ event: 'click', target: '.edit-annotation-button', info: 'Edit annotation' },
			{ event: 'click', target: '#close-preview-button', info: 'Close annotation preview' },
			{ event: 'change', target: '#annotation-filter input', info: 'Change annotation filter' },
			{ event: 'click', target: '.add-comment', info: 'Add Comment' },
			// {event: 'click', target: '.edit-comment-button', info: 'Edit comment'}
		];

		for (var i = 0; i < trackingsArr.length; i++) {
			trackingInitial(trackingsArr[i], trackings);
		}

		function showAlertDialog(msg) {
			$("#alert_dialog_content").empty();
			var content = "<h3>" + msg + "</h3>";
			$("#alert_dialog_content").append(content);
			$("#alert_dialog").modal({
				backdrop: 'static',
				keyboard: false
			});
			return;
		}
	}//function()
); //document ready
