

import { PlyrLayout, VidstackPlayer } from 'vidstack/global/player';

let quiz_meta = [];
let is_visible = true;
let flag = false;


async function main() {
    // every video bound with a unique session id
    if (!sessionStorage.getItem('v-session-id') || window.location.pathname !== sessionStorage.getItem('bounded-video')) {
        const uniqueVideoId = Date.now().toString(36) + Math.floor(Math.pow(10, 12) + Math.random() * 9 * Math.pow(10, 12)).toString(36);
        sessionStorage.setItem('v-session-id', uniqueVideoId);
        sessionStorage.setItem('bounded-video', window.location.pathname)
    }
    const uniqueVideoId = sessionStorage.getItem('v-session-id');
    if (uniqueVideoId) {
        const previousBeforeSend = $.ajaxSettings.beforeSend;
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                if (previousBeforeSend) {
                    previousBeforeSend(xhr, settings);
                }
                xhr.setRequestHeader('v-session-id', uniqueVideoId);
            }
        });

        let layout = 'V';
        if (window.Oval.currentGroupVideo.show_annotations === 1) {
            layout += 'A'
        }
        if (window.Oval.currentGroupVideo.show_comments === 1) {
            layout += 'C'
        }

        let browser = "";
        const brandsLength = navigator?.userAgentData?.brands?.length;
        if (brandsLength) {
            browser = navigator.userAgentData.brands[brandsLength - 1].brand;
        } else {
            browser = 'undetected';
        }

        const sessionData = {
            id: uniqueVideoId,
            browser,
            os: (navigator?.userAgentData?.platform || navigator?.platform) ?? 'undetected',
            doc_width: document.documentElement.scrollWidth,
            doc_height: document.documentElement.scrollWidth,
            init_screen_width: window.innerWidth,
            init_screen_height: window.innerHeight,
            layout,
            group_video_id: window.group_video_id
        };
        $.ajax({
            type: "POST",
            url: "/session-information",
            data: sessionData
        })
    }

    const groupVideo = window.Oval.currentGroupVideo;
    await setupPlayer(groupVideo);
    loadQuiz(groupVideo.id);
}
main();

async function setupPlayer(groupVideo) {
    const player = await VidstackPlayer.create({
        controlsDelay: 1000 * 60 * 60 * 24,
        hideControlsOnMouseLeave: false,
        target: '#target',
        src: `youtube/${window.video_identifier}`,
        poster: `https://img.youtube.com/vi/${window.video_identifier}/maxresdefault.jpg`,
        viewType: 'video',
        fullscreenOrientation: "none",
        layout: new PlyrLayout({
            controls: buildPlayerControl(groupVideo),
            speed: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
        }),
    });
    bindUtilityToWindowObject(player);

    player.addEventListener('started', () => onVideoStart(player));
    player.addEventListener('rate-change', (e) => track('Speed', `Change to ${e.detail}`));
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

    player.addEventListener('can-play', () => {
        window.playVideo = () => {
            player.play();
        }
    });

    $('.plyr__progress').on('click', () => track('Buffering'))
    document.addEventListener("cuechange", () => track('Cued'));
    document.addEventListener("fullscreenchange", () => {
        if (document.fullscreenElement != null && !groupVideo.controls.fullscreen) {
            document.exitFullscreen();
        }
        if (document.fullscreenElement != null) {
            track('Fullscreen', 'Enter fullscreen');
        } else {
            track('Fullscreen', 'Exit fullscreen');
        }
    });

    $(document).ready(() => {
        // fix: video cannot play programmatically
        // as mentioned on ./player.js
        // so we let user to 'interact' with the youtube frame
        $('.vds-blocker').hide();
        $('media-play-button.plyr__control--overlaid').css('pointer-events', 'none');
    })
}

function onVideoStart(player) {
    // fix: video cannot play programmatically
    // then add the overlay control again
    $('.vds-blocker').show();
    $('media-play-button.plyr__control--overlaid').css('pointer-events', 'all');

    $('.vds-blocker').on('click', function (e) {
        if (player.state.paused) {
            player.play();
        } else {
            player.pause();
        }
    });
    $('.plyr__control[data-plyr="speed"][value="1"]').css({ 'background-color': '#00b2ff', 'color': 'white' });
    $('.plyr__control[data-plyr="speed"]').on('click', function (e) {
        const speed = e.currentTarget.attributes['value'].value;
        player.playbackRate = Number(speed);
        $('.plyr__control[data-plyr="speed"]').css({ 'background-color': '', 'color': '#495463' });
        $(this).css({ 'background-color': '#00b2ff', 'color': 'white' });
    });
    // player.loadModule("captions");
    showKeywordsOnTimeChange();
    checkQuiz();
}

function track(action, info = null, ...arg) {
    window.trackings.push({
        event: action,
        target: null,
        info: info,
        video_time: window.exactCurrentVideoTime(),
        event_time: Date.now(),
        ...arg
    });

    if (window.trackings.length >= 3) {
        const tmp = window.trackings;
        window.trackings = [];

        $.ajax({
            type: "POST",
            url: "/trackings",
            data: { data: tmp, group_video_id: group_video_id },
            success: function (data) {
            },
            error: function (request, status, error) {
                console.log("Error on tracking: " + record.target)
                window.trackings.push(...tmp)
            },
        });
    }
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
        return; // avoid playing when video is not ready to play
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
                if (!quiz_meta) quiz_meta = [];
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
    var check_quiz_interval = window.setInterval(
        function () {

            if (quiz_meta.length > 0 && is_visible) {
                const current_video_time = parseInt(window.currentVideoTime().toFixed(0));
                let meta_position = null;

                for (var i = 0; i < quiz_meta.length; i++) {
                    if (current_video_time <= parseInt(quiz_meta[i].stop)) {
                        meta_position = i;
                        break;
                    }
                }

                if (meta_position == null) {
                    clearInterval(check_quiz_interval);
                    return;
                }

                const quiz_stop = parseInt(quiz_meta[meta_position].stop);
                if (current_video_time === quiz_stop) {
                    setTimeout(function () {
                        track("Quiz", "Open quiz modal");
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
            track("Quiz", "Close quiz modal");
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
                    group_video_id: group_video_id,
                    media_type: 'youtube',
                    quiz_data: data,
                    video_time: window.exactCurrentVideoTime()
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
        if (window.getVideoState('paused')) {
            window.playVideo();
        }
    });
}

/*------ string process functions ------*/
function encodeText(txt) {
    return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
/*------ string process functions ------*/