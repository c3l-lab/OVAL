var tag = document.createElement('script');
tag.src = helix_js_host + "/jwplayer.js";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

window.onload = function () {
	setup_jwplayer();
	load_quiz(video_identifier);
}
function isJWPlayerLoaded() {
	return typeof (jwplayer) == 'function';
}
var timer6852 = null;
var timerCounter6852 = 0;
function setup_jwplayer() {
	if (!isJWPlayerLoaded()) {
		timerCounter6852 = window.setTimeout(function () {
			timerCounter6852++;
			if (timerCounter6852 < 10) {
				setup_jwplayer();
			} else {
				window.clearTimeout(timer6852);
			}
		}, 1000);
		return;
	}
	var file = video_identifier + "_lo.mp4";
	var helix_host = helix_server_host.split('://').pop();
	jwplayer("player").setup({
		"id": "player",
		"width": "100%",
		"provider": "rtmp",
		"streamer": "rtmp://" + helix_host + "/flash/",
		"file": file,
		"image": thumbnail_url,
		"modes": [
			{
				type: "html5",
				config: {
					"file": helix_server_host+"/flash/" + file,
					"provider": "video",
				}
			},
		],
		// "plugins": {
		// 	"captions": {
		// 		"files": transcript_path
		// 	}
		// }
	});

	jwplayer().onPlay(function () {
		saveTracking({ event: "Play", target: null, info: jwplayer().getPosition(), event_time: Date.now() });
	});
	jwplayer().onPause(function () {
		saveTracking({ event: "Pause", target: null, info: jwplayer().getPosition(), event_time: Date.now() });
	});
	jwplayer().onComplete(function () {
		saveTracking({ event: "End", target: null, info: jwplayer().getPosition(), event_time: Date.now() });
		$("#current-keywords").html("&nbsp;");
	});
	jwplayer("player").onTime(function (e) {
		$.each(current_keywords, function (i, val) {
			if (e.position > i) {
				$("#current-keywords").text(val);
			}
		})
	});
}

function pauseVideo() {
	jwplayer("player").pause(true);
}

function playVideo(){
	jwplayer("player").play()
}

function goTo(time) {
	jwplayer("player").seek(time);
}

function currentVideoTime() {
	return jwplayer("player").getPosition();
}

/*------ quiz client plugin for helix ------*/

var quiz_meta = [];
var is_visable = true;

var flag = false; //true for modal fired, false for not fired

var intervalID_youtubeplayer = window.setInterval(
	function () {

		if(quiz_meta.length > 0 && is_visable){

			var current_video_time = parseInt(currentVideoTime().toFixed(0));
			var trigger = true;
			var meta_position = 0;

			for(var i=0; i< quiz_meta.length; i++){
				if(trigger  && current_video_time <= parseInt(quiz_meta[i].stop)){
					meta_position = i;
					trigger = false
				}
			}

			if(meta_position > 0){
				var quiz_stop = parseInt(quiz_meta[meta_position].stop);
			}else{
				var quiz_stop = parseInt(quiz_meta[0].stop);
			}

			if(current_video_time === quiz_stop){
				setTimeout(function() {
					show_quiz(quiz_meta[meta_position]);   
				}, 1000); 
			}

		}

	},
	1000
)

function load_quiz(video_identifier){
	$.ajax({
		type: "GET",
		url: "/get_quiz",
		data: {
			identifier: video_identifier
		},
		success: function(res) {

			console.log(res)

			if(res.quiz != null){
				quiz_meta = JSON.parse(res.quiz.quiz_data);
				switch (parseInt(res.quiz.visable)) {
					case 0:
						is_visable = false;
						break;
					case 1:
						is_visable = true;
						break;
					default:
						break;
				}
			}
			
		},
		error: function(request, status, error) {
			alert("error get quiz - "+error);	
		}
	});
}

function show_quiz(data) {
	pauseVideo();
	show_quiz_modal(data)
}

function show_quiz_modal(data){
	if(!flag){
		$("#quiz_modal").off("hidden.bs.modal");
		$(".client_question_list_wrap").empty();

		for(var i = 0; i< data.items.length; i++){
			switch (data.items[i].type) {
				case "text":
					var $li = $("<li></li>");
					var div = "<h3> <i class='fa fa-question-circle-o' aria-hidden='true'></i> Question " + (parseInt(i) + 1) + " </h3>" +
								"<h4><strong>" + data.items[i].title + "</strong></h4>" +
								"<textarea name='' id='quiz_q" + (parseInt(i) + 1) + "' cols='20' rows='3' placeholder='Please input your answer'></textarea>"
					$li.append(div);
					$(".client_question_list_wrap").append($li);
					break;

				case "multiple_choice":  
					var $li = $("<li></li>");                     
					var div = "<h3> <i class='fa fa-question-circle-o' aria-hidden='true'></i> Question " + (parseInt(i) + 1) + " </h3>" +
								"<h4><strong>" + data.items[i].title + "</strong></h4>";

					var choice_list = $("<ul class='list'></ul>");
					for(var j = 0; j < data.items[i].list.length; j++){
						var choice = "<li class='list__item'>"+
										"<input type='radio' class='radio-btn' name='choise_" + (parseInt(i) + 1) + "' id='choise_" + (parseInt(i) + 1) + "_" + (parseInt(j) + 1) + "'  />" +
										"<label for='choise_" + (parseInt(i) + 1) + "_" + (parseInt(j) + 1) + "' class='label'>" + data.items[i].list[j] + "</label>" +
										"</li>";
						choice_list.append(choice);
					}
							
					$li.append(div);
					$li.append(choice_list);

					$(".client_question_list_wrap").append($li);

					break;
				default:
					break;
			}
		}

		$("#quiz_modal").modal({
			backdrop: 'static',
			keyboard: false
		})

		$("#quiz_modal").on("hidden.bs.modal", function () {
			playVideo();
			flag = false;
		});

		flag = true;

		/*------ submit result ------*/
		$("#submit_result").off();
		$("#submit_result").on("click", function(){
			/*------ collect result data ------*/
			// var is_all_correct = false;
			// var feedback_arr = [];

			for(var i = 0; i< data.items.length; i++){
				switch (data.items[i].type) {
					case "text":
						var user_ans = $("#quiz_q" + (parseInt(i) + 1)).val();
						data.items[i]["user_ans"] = encodeText(user_ans);

						break;
					case "multiple_choice":
						var user_ans = $(".client_question_list_wrap input[name='" + "choise_" + (parseInt(i) + 1) + "']:checked").siblings("label").text();
						data.items[i]["user_ans"] = user_ans;

						// if(data.items[i].ans[0] === user_ans){
						// 	is_all_correct = true;

						// }else{
						// 	is_all_correct = false;
						// 	feedback_arr.push(i);
						// }

						break;
					default:
						break;
				}
			}

			// console.log(is_all_correct);

			/*------ send data to server ------*/
			var user_id = $(".greetings").attr('userid');

			$.ajax({
				type: "POST",
				url: "/submit_quiz_result",
				data: {
					user_id: user_id,					//int
					identifier: video_identifier,  		//stirng
					media_type: 'helix',		    	//string
					quiz_data: data            			//obj
				},
				success: function(res) {
					if(res.result === "success"){

						// if(!is_all_correct){

						var feedback_arr = show_feedback_hint(data);

						if(feedback_arr.length > 0) {
							show_feedback_modal(feedback_arr);
						}else{

							setTimeout(function() {
								$("#quiz_modal").modal("hide");
							}, 1000);

						}

						// }

					}
				},
				error: function(request, status, error) {
					alert("error submit quiz result - " + error);
				}
			});


		});

	}

}

function alert_modal_client(message){
	$("#alert_dialog_content").empty();

	var content = "<h3>" + message + "</h3>";
	$("#alert_dialog_content").append(content);

	$("#alert_dialog").modal({
		backdrop: 'static',
		keyboard: false
	});

}

function show_feedback_hint(quiz_data){

	var return_obj = [];

	for(var i =0; i < quiz_data.items.length; i++){
		var user_ans = quiz_data.items[i].user_ans;
		var title = quiz_data.items[i].title;
		var feedback_position = 0;

		if(quiz_data.items[i].type === "multiple_choice"){

			/*------ type is multiple_choice ------*/

			/*------ compare feedback ------*/
			for (var j = 0; j < quiz_data.items[i].list.length; j++) {
				if (user_ans === quiz_data.items[i].list[j] && user_ans != quiz_data.items[i].ans[0]) {
					feedback_position = j;
				}
			}

			/*------ structure return obj ------*/
			var temp = {
				"type": "Multiple Choice",
				"question": "",
				"answer": "",
				"is_correct": false,
				"feedback": ""
			};

			temp.question = title;
			temp.answer = user_ans;
			temp.feedback = quiz_data.items[i].feedback[feedback_position];

			if(quiz_data.items[i].ans[0] === quiz_data.items[i].user_ans){
				temp.is_correct = true;
			}else{
				temp.is_correct = false;
			}


			return_obj.push(temp);

		}else{

			/*------ type is short answer ------*/
			var temp = {
				"type": "Short Answer",
				"question": "",
				"answer": "",
				"is_correct": "",
				"feedback": ""
			};

			temp.question = title;
			temp.answer = user_ans;
			temp.feedback = quiz_data.items[i].ans;

			return_obj.push(temp);
		}



	}

	return return_obj;

}

function show_feedback_modal(feedback_array){

	$("#feedback_dialog_content_table_head").nextAll('tr').remove();

	for(var i = 0; i< feedback_array.length; i++){

		var $tr = $("<tr></tr>");
		
		for(var item in feedback_array[i]){

			if (item === "is_correct") {

				if (typeof (feedback_array[i][item]) === "boolean") {
					switch (feedback_array[i][item]) {
						case true:
							var th = "<td style='text-align:center;'>" + "<img src='../../img/tick.png' alt='' style='width:32px; height:auto;'>" + "</td>";
							break;
						case false:
							var th = "<td style='text-align:center;'>" + "<img src='../../img/cancel.png' alt='' style='width:32px; height:auto;'>" + "</td>";
							break;
						default:
							break;
					}

				} else {
					var th = "<td>" + "please check example answer" + "</td>";
				}

				$tr.append(th);

			} else {
				var th = "<td>" + feedback_array[i][item] + "</td>";
				$tr.append(th);
			}

		}

		$("#feedback_dialog_content table tbody").append($tr);
		
	}	
	
	

	$("#feedback_dialog").modal();
}

/*------ end quiz client plugin for helix ------*/

/*------ string process functions ------*/
function encodeText(txt) {
	return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
/*------ string process functions ------*/
