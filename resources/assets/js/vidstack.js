

import { PlyrLayout, VidstackPlayer } from 'vidstack/global/player';

let quiz_meta = [];
let is_visible = true;
let flag = false; //true for modal fired, false for not fired


async function main() {
    const groupVideo = window.Oval.currentGroupVideo;
    await setupPlayer(groupVideo);
    loadQuiz(groupVideo.id);
}
main();

async function setupPlayer(groupVideo) {
    const player = await VidstackPlayer.create({
        target: '#target',
        src: `youtube/${window.video_identifier}`,
        poster: `https://img.youtube.com/vi/${window.video_identifier}/maxresdefault.jpg`,
        viewType: 'video',
        layout: new PlyrLayout({
            controls: buildPlayerControl(groupVideo),
        }),
    });
    bindUtilityToWindowObject(player);

    player.addEventListener('started', () => onVideoStart(player));
    player.addEventListener('play', () => track('Play'));
    player.addEventListener('pause', () => {
        if (player.pauseFromJs !== true && !groupVideo.controls.play) {
            player.play();
        }
        player.pauseFromJs = false;
        track('Paused')
    });
    player.addEventListener('ended', () => {
        track('Ended')
    });
    $('.plyr__progress').on('click', () => track('Buffering'))
    document.addEventListener("cuechange", (e) => track('Cued'));
    document.addEventListener('fullscreenchange', (e) => {
        if (document.fullscreenElement != null && !groupVideo.controls.fullscreen) {
            document.exitFullscreen();
        }
    });
}

function onVideoStart(player) {
    $('.vds-blocker').on('click', function (e) {
        if (player.state.paused) {
            player.play();
        } else {
            player.pause();
        }
    });

    $('.plyr__control[data-plyr="speed"]').on('click', (e) => {
        const speed = e.currentTarget.attributes['value'].value;
        player.playbackRate = parseInt(speed);
    });
    // player.loadModule("captions");
    showKeywordsOnTimeChange();
    checkQuiz();
}

function track(action) {
    window.saveTracking({
        event: action,
        target: null, info: null,
        video_time: window.exactCurrentVideoTime(),
        event_time: Date.now()
    });
}

function bindUtilityToWindowObject(player) {
    window.currentVideoTime = () => {
        return Math.floor(player.state.currentTime);
    }
    window.exactCurrentVideoTime = () => {
        return player.state.currentTime;
    }
    window.getVideoState = (state) => {
        return player.state[state];
    }
    window.pauseVideo = () => {
        player.pauseFromJs = true; // flag added to control the player
        player.pause();
    }
    window.playVideo = () => {
        player.play();
    }
    window.goTo = (time) => {
        player.currentTime = time;
    }
}

function showKeywordsOnTimeChange() {
    setInterval(() => {
        if (window.getVideoState('playing')) {
            $.each(window.current_keywords, function (i, val) {
                if (window.exactCurrentVideoTime() > i) {
                    $("#current-keywords").text(val);
                }
            });
        }
    }, 1000);
};

function buildPlayerControl(groupVideo) {
    const controls = ['play-large'];
    if (groupVideo.controls.play) {
        controls.push('play');
    }
    if (groupVideo.controls.progress) {
        controls.push('progress');
    }
    controls.push('current-time');
    if (groupVideo.controls.volume) {
        controls.push('volume');
    }
    if (groupVideo.controls.captions) {
        controls.push('captions');
    }
    if (groupVideo.controls.speed) {
        controls.push('settings');
    }
    if (groupVideo.controls.fullscreen) {
        controls.push('fullscreen');
    }

    return controls;
}

// quiz section

function loadQuiz(groupVideoId) {
    $.ajax({
        type: "GET",
        url: "/group_videos/" + groupVideoId + "/quiz",
        success: function (res) {
            if (res.quiz != null) {
                quiz_meta = JSON.parse(res.quiz.quiz_data);
                switch (parseInt(res.quiz.visable)) {
                    case 0:
                        is_visible = false;
                        break;
                    case 1:
                        is_visible = true;
                        break;
                    default:
                        break;
                }
            }

        },
        error: function (request, status, error) {
            alert("error get quiz - " + error);
        }
    });
}

function checkQuiz() {
    var intervalID_youtubeplayer = window.setInterval(
        function () {

            if (quiz_meta.length > 0 && is_visible) {

                var current_video_time = parseInt(window.currentVideoTime().toFixed(0));
                var trigger = true;
                var meta_position = 0;

                for (var i = 0; i < quiz_meta.length; i++) {
                    if (trigger && current_video_time <= parseInt(quiz_meta[i].stop)) {
                        meta_position = i;
                        trigger = false
                    }
                }

                if (meta_position > 0) {
                    var quiz_stop = parseInt(quiz_meta[meta_position].stop);
                } else {
                    var quiz_stop = parseInt(quiz_meta[0].stop);
                }

                if (current_video_time === quiz_stop) {
                    setTimeout(function () {
                        showQuiz(quiz_meta[meta_position]);
                    }, 1000);
                }

            }

        },
        1000
    )
}

function showQuiz(data) {
    window.pauseVideo();
    if (document.fullscreenElement != null) {
        document.exitFullscreen().then(() => {
            showQuizModal(data);
        }).catch(() => {
            alert("unable to exit fullscreen.");
        });
    } else {
        showQuizModal(data);
    }
}


function showQuizModal(data, cb) {
    if (!flag) {
        $("#quiz_modal").off("hidden.bs.modal");
        $(".client_question_list_wrap").empty();

        for (var i = 0; i < data.items.length; i++) {
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
                    for (var j = 0; j < data.items[i].list.length; j++) {
                        var choice = "<li class='list__item'>" +
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
            flag = false;
        });

        flag = true;

        /*------ submit result ------*/
        $("#submit_result").off();
        $("#submit_result").on("click", function () {
            /*------ collect result data ------*/
            // var is_all_correct = false;
            // var feedback_arr = [];

            for (var i = 0; i < data.items.length; i++) {
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
            $.ajax({
                type: "POST",
                url: "/quiz_results",
                data: {
                    group_video_id: group_video_id,  		//stirng
                    media_type: 'youtube',		    	//string
                    quiz_data: data            			//obj
                },
                success: function (res) {
                    if (res.result === "success") {

                        // if(!is_all_correct){

                        var feedback_arr = showFeedbackHint(data);

                        if (feedback_arr.length > 0) {
                            $("#quiz_modal").modal('hide');
                            showFeedbackModal(feedback_arr);
                        } else {

                            setTimeout(function () {
                                $("#quiz_modal").modal("hide");
                            }, 1000);

                        }

                        // }

                    }
                },
                error: function (request, status, error) {
                    alert("error submit quiz result - " + error);
                }
            });


        });

    }

    //return cb();
}

// feedback modal
function showFeedbackHint(quiz_data) {

    var return_obj = [];

    for (var i = 0; i < quiz_data.items.length; i++) {
        var user_ans = quiz_data.items[i].user_ans;
        var title = quiz_data.items[i].title;
        var feedback_position = 0;

        if (quiz_data.items[i].type === "multiple_choice") {

            /*------ type is multiple_choice ------*/

            // if (user_ans == quiz_data.items[i].ans[0]) {

            // 	/*------ correct answer ------*/
            // } else {

            // 	/*------ compare feedback ------*/
            // 	for (var j = 0; j < quiz_data.items[i].list.length; j++) {
            // 		if (user_ans === quiz_data.items[i].list[j] && user_ans != quiz_data.items[i].ans[0]) {
            // 			feedback_position = j;
            // 		}
            // 	}

            // 	/*------ structure return obj ------*/
            // 	var temp = {
            // 		"question": "",
            // 		"answer": "",
            // 		"feedback": ""
            // 	};

            // 	temp.question = title;
            // 	temp.answer = user_ans;
            // 	temp.feedback = quiz_data.items[i].feedback[feedback_position];

            // 	return_obj.push(temp);
            // }

            /*------ compare feedback ------*/
            for (var j = 0; j < quiz_data.items[i].list.length; j++) {
                if (user_ans === quiz_data.items[i].list[j]) {
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

            if (quiz_data.items[i].ans[0] === quiz_data.items[i].user_ans) {
                temp.is_correct = true;
            } else {
                temp.is_correct = false;
            }

            return_obj.push(temp);

        } else {

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

function showFeedbackModal(feedback_array) {

    $("#feedback_dialog_content_table_head").nextAll('tr').remove();

    for (var i = 0; i < feedback_array.length; i++) {

        var $tr = $("<tr></tr>");

        for (var item in feedback_array[i]) {

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

    $("#feedback_dialog").modal('show');

    $("#feedback_dialog").on("hidden.bs.modal", function () {
        if (player.getPlayerState() === 2) {
            window.playVideo();
        }
    });
}

/*------ string process functions ------*/
function encodeText(txt) {
    return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
/*------ string process functions ------*/