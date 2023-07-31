var video_id;

$('document').ready(function(){

	$("#course-name").text(course_name);
	$("#group-name").text(group_name);
	$(".msg").hide();

	$('form[data-toggle="validator"]').validator ({
		custom: {
			filetype: function($el) {
				var acceptable = $el.data('filetype').split(',');
				var filename = $("#transcript-file").val();
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
		video_id = $(e.relatedTarget).data('id');
		$("#video_id").val($(e.relatedTarget).data('id'));
	});
	transcript_form.find("form").on("submit", function(e) {
		transcript_form.modal('hide');
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
});
