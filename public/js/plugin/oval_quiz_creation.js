/**
 * oval quiz plugin
 * yin_gong<Max.Gong@unisa.edu.au>
 */

/*------ sample structure ------*/
/**
var set_quiz_meta = [{
    name: "demo_quiz_1",
    stop: 5, //stop @ which second
    items: [{
        title: "demo question 1 title",
        type: "text" //type: "text","multiple_choice"......
    },{
        title: "demo question 2 title",
        type: "multiple_choice",
        list: ["choice 1", "choice 2", "choice 3"]
    }]
}, {
    name: "demo_quiz_2",
    stop: 10, //stop @ which second
    items: []
}, {
    name: "demo_quiz_3",
    stop: 15, //stop @ which second
    items: []
}];
*/
/*------ end sample structure ------*/

// (function ($) {
//     "use strict";

var set_quiz_meta = [];

var temp_option = [];

/*------ create new quiz obj ------*/
var quiz_obj = {
    name: "",
    stop: 0,
    items: []
};

var is_instructor = false;
var user_id = 0;

$(document).ready(function () {

    $(document).find('table').each(function () {
        var table_type = $(this).attr('name');

        switch (table_type) {
            case 'assiged':

                $(this).find('tbody tr').each(function () {

                    var link = $(this).first().find('img').attr('src').split('/');
                    var link_tag = link[2];

                    $quizButton = $('#set-quiz-btn');

                    switch (link_tag) {
                        case "img.youtube.com":

                            var video_key = link[4];

                            /*------ store data into tag ------*/
                            $quizButton.attr("identifier", video_key);
                            $quizButton.attr("media_type", "youtube");

                            /*------ bind click event to tag ------*/
                            $quizButton.on('click', function () {
                                var identifier = $(this).attr("identifier");
                                var media_type = $(this).attr("media_type");

                                change_tag_status(identifier, media_type);

                                setTimeout(function () {

                                    /*------ show quiz set instruction ------*/
                                    $("#pop_out_dialog").modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    })

                                    create_instruction();

                                    $("#close_to_use, #start_to_use, #skip_tutorial").off();

                                    $("#close_to_use, #start_to_use, #skip_tutorial ").on("click", function () {

                                        $("#pop_out_dialog").modal('hide');

                                        $("#quiz_create_modal").modal({
                                            backdrop: 'static',
                                            keyboard: false
                                        });

                                        /*----- load quiz data from server ------*/

                                        $.ajax({
                                            type: "GET",
                                            url: "/get_quiz",
                                            data: {
                                                identifier: identifier
                                            },
                                            success: function (res) {

                                                if (res.quiz != null) {

                                                    set_quiz_meta = JSON.parse(res.quiz.quiz_data);

                                                    for (var i = 0; i < set_quiz_meta.length; i++) {
                                                        set_quiz_meta[i].stop = parseInt(set_quiz_meta[i].stop);
                                                    }

                                                    update_quiz_list(set_quiz_meta);

                                                } else {
                                                    var clear_arr = [];
                                                    update_quiz_list(clear_arr);
                                                }

                                            },
                                            error: function (request, status, error) {
                                                alert_modal("error get quiz - " + error);
                                            }
                                        });

                                        create_modal('youtube', video_key);

                                        /*----- end load quiz data from server ------*/
                                    });


                                }, 500)

                            });

                            break;
                        case "helix.example.com":

                            var video_key = link[4].split(".")[0];

                            /*------ store data into tag ------*/
                            $quizButton.attr("identifier", video_key);
                            $quizButton.attr("media_type", "helix");

                            /*------ bind click event to tag ------*/
                            $quizButton.on('click', function () {
                                var identifier = $(this).attr("identifier");
                                var media_type = $(this).attr("media_type");

                                change_tag_status(identifier, media_type);

                                setTimeout(function () {

                                    /*------ show quiz set instruction ------*/
                                    $("#pop_out_dialog").modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    })

                                    create_instruction();

                                    $("#close_to_use, #start_to_use, #skip_tutorial").off();

                                    $("#close_to_use, #start_to_use, #skip_tutorial ").on("click", function () {

                                        $("#pop_out_dialog").modal('hide');

                                        $("#quiz_create_modal").modal({
                                            backdrop: 'static',
                                            keyboard: false
                                        });

                                        /*----- load quiz data from server ------*/

                                        $.ajax({
                                            type: "GET",
                                            url: "/get_quiz",
                                            data: {
                                                identifier: identifier
                                            },
                                            success: function (res) {

                                                if (res.quiz != null) {

                                                    set_quiz_meta = JSON.parse(res.quiz.quiz_data);

                                                    for (var i = 0; i < set_quiz_meta.length; i++) {
                                                        set_quiz_meta[i].stop = parseInt(set_quiz_meta[i].stop);
                                                    }

                                                    update_quiz_list(set_quiz_meta);

                                                } else {
                                                    var clear_arr = [];
                                                    update_quiz_list(clear_arr);
                                                }

                                            },
                                            error: function (request, status, error) {
                                                alert_modal("error get quiz - " + error);
                                            }
                                        });

                                        create_modal('helix', video_key);
                                    });


                                }, 500)

                            });

                        default:
                            break;
                    }


                })

                break;
            default:
                break;
        }

    })

    /*------ set swith ------*/
    $("#quiz-switch").each(function () {
        $(this).children('input').on('change', function () {

            var videoid = $(this).attr('videoid');

            if ($(this).prop('checked')) {

                $.ajax({
                    url: "/change_quiz_visable",
                    type: "get",
                    data: {
                        visable: 1,
                        videoid: videoid
                    },
                    success: function (res) {
                        if (res.result === "success") {

                            alert_modal("status upload successfully!");
                        }
                    },
                    error: function (request, status, error) {

                        alert_modal("error submit quiz - " + error);
                    }
                })

                $(this).parent().children('p').text('visible');

            } else {

                $.ajax({
                    url: "/change_quiz_visable",
                    type: "get",
                    data: {
                        visable: 0,
                        videoid: videoid
                    },
                    success: function (res) {
                        if (res.result === "success") {

                            alert_modal("status upload successfully!");
                        }
                    },
                    error: function (request, status, error) {

                        alert_modal("error submit quiz - " + error);
                    }
                })

                $(this).parent().children('p').text('hidden');

            }

        });
    })

    $("#comments-switch,#annotations-switch").each(function () {
        $(this).children('input').on('change', function () {
            const url = $(this).data('url');
            $.ajax({
                url,
                type: "post",
                success: function (res) {
                    alert_modal("status update successfully!");
                },
                error: function (request, status, error) {
                    alert_modal("error update - " + error);
                }
            })

            $(this).parent().children('p').text('visible');
        });
    })

    /*------ judge if user is instructor, if yes show set quiz section ------*/
    user_id = $(".greetings").attr('userid');
    switch ($(".greetings").attr('isinstructor')) {
        case '1':
            is_instructor = true;
            break;
        default:
            is_instructor = false;
            break;
    }

    /*------ bind stop video function to close modal ------*/
    $('#quiz_create_modal').on('hidden.bs.modal', function () {
        pauseVideo();
    })

    /*------ list toggle button ------*/
    $("#question_list_toggle_btn").on("click", function () {
        $(this).parent().siblings('ul').toggle();
        $(this).toggleClass('fa-caret-down fa-list-ul');
    });

    $("#quiz_list_toggle_btn").on("click", function () {
        $(this).parent().siblings('ul').toggle();
        $(this).toggleClass('fa-caret-down fa-list-ul');
    });

    /*------ add question function ------*/
    $("#1a_btn").on("click", function () {

        var stop = parseInt($("#create_quiz_time").attr('stoptime'));
        var block_type = $(this).attr("blockType");
        var question = $("#1a").find('textarea').val();

        var choices = [];
        $("#quiz_options_wrap tbody tr").each(function () {
            var first_part = $(this).find('td').eq(0).text().trim().replace(" ", "");
            var second_part = $(this).find('td').eq(1).find('input[type="text"]').val();

            choices.push(encodeText(first_part + second_part));
        });

        var answer = [];
        $("#quiz_options_wrap tbody tr").each(function () {
            var is_checked = $(this).find('td').eq(2).find('input[type="radio"]').prop('checked');
            if (is_checked) {
                var first_part = $(this).find('td').eq(0).text().trim().replace(" ", "");
                var second_part = $(this).find('td').eq(1).find('input[type="text"]').val();

                answer.push(encodeText(first_part + second_part));
            }
        });

        var feedback = [];
        $("#quiz_options_wrap tbody tr").each(function () {
            var feedback_str = $(this).find('td').eq(3).find('input[type="text"]').val();
            // if(is_checked){
            //     var first_part = $(this).find('td').eq(0).text().trim().replace(" ","");
            //     var second_part = $(this).find('td').eq(1).find('input[type="text"]').val();

            //     answer.push(first_part + second_part);
            // }
            feedback.push(encodeText(feedback_str));
        });

        /*------ create new quiz obj ------*/
        quiz_obj.stop = stop;

        if (question != "" && choices != "") {
            var temp = {
                title: encodeText(question),
                type: block_type,
                list: choices,
                ans: answer,
                feedback: feedback
            };

            quiz_obj.items.push(temp)

            update_question_list(quiz_obj);

            clear();
            temp_option.length = 0;

        } else {

            alert_modal("you need to fill required form.");
        }

    });

    $("#2a_btn").on("click", function () {
        var stop = parseInt($("#create_quiz_time").attr('stoptime'));
        var block_type = $(this).attr("blockType");
        var question = $("#2a #quiz_text_area_question").val();
        var answer = $("#2a #quiz_text_area_answer").val();

        /*------ create new quiz obj ------*/
        quiz_obj.stop = stop;

        if (question != "") {
            var temp = {
                title: encodeText(question),
                type: block_type,
                ans: encodeText(answer),
            };

            quiz_obj.items.push(temp)

            update_question_list(quiz_obj);

            clear();
            temp_option.length = 0;

        } else {
            alert_modal("you need to fill required form.");
        }


    });

    /*------ bind event to submit button ------*/
    $("#quiz_save_btn").on("click", function () {
        quiz_obj.name = "Quiz @ " + seconds_to_HMS($("#create_quiz_time").attr('stoptime'));

        /*------ @22/Feb/2018: fix sorting bug ------*/

        if (quiz_obj.items.length > 0) {

            /*------ arrange set_quiz_meta ------*/

            for (var i = 0; i < set_quiz_meta.length; i++) {

                if (set_quiz_meta[i].stop == quiz_obj.stop) {

                    set_quiz_meta.splice(i, 1);

                }

            }

            set_quiz_meta.push(quiz_obj);

            set_quiz_meta.sort(function (a, b) {

                if (a.stop > b.stop) {

                    return 1

                } else {

                    return -1

                }

            });

            update_quiz_list(set_quiz_meta);

            quiz_obj = {
                name: "",
                stop: 0,
                items: []
            };

            update_question_list(quiz_obj);
            $(".create_new_quiz_wrap").hide();
            $(".question_preview_wrap").hide();
            $(".quiz_save_btn_wrap").hide();

        } else {

            alert_modal("please create new quiz, thanks");
        }

        /*------ @22/Feb/2018: end fix sorting bug @22/Feb/2018------*/

    });

    $("#quiz_submit_btn").on("click", function () {

        /*------ judge if quiz list is empty or not ------*/
        var is_quizlist_empty = $(".quiz_warp ul ul").children().length > 0 ? false : true;

        if (is_quizlist_empty) {

            alert_modal("sorry, quiz list is emtpy, please set some quiz");

        } else {

            var creator_id = parseInt(user_id);
            var identifier = $(this).attr("identifier");
            var media_type = $(this).attr("media_type");

            submit_quiz_to_server(creator_id, identifier, media_type, set_quiz_meta);

        }


    });

    /*------ pre-set video quiz visable status ------*/
    var video_id_list = "";
    $('.switch').each(function () {
        var input = $(this).children('input').attr('videoid');
        video_id_list += input + ",";
    });

    video_id_list = video_id_list.substring(0, video_id_list.length - 1);

    if (video_id_list.length > 0) {
        $.ajax({
            url: "/get_quiz_visable_status",
            type: "GET",
            data: {
                videoid: video_id_list
            },
            error: function (xhr, status, errorThrown) {
                if (status === 'error') {

                    alert_modal("database query error!")

                }
            },
            success: function (res) {

                $('.switch').each(function () {
                    var video_id = $(this).children('input').attr('videoid')
                    var input = $(this).children('input').prop('checked');

                    for (var i = 0; i < res.length; i++) {
                        if (res[i].video_id == video_id) {
                            switch (parseInt(res[i].visable)) {
                                case 0:
                                    $(this).children('input').prop('checked', false);
                                    $(this).children('p').text('hidden');
                                    break;
                                case 1:
                                    $(this).children('input').prop('checked', true);
                                    $(this).children('p').text('visible');
                                    break;
                                default:
                                    break;
                            }
                        }
                    }

                });
            }
        })
    }

    /*------ end pre-set video quiz visable status ------*/
    /*------ add/remove option ------*/

    $("#quiz_options_add").on("click", function () {

        //load data to temp_option

        switch (temp_option.length) {
            case 0:
                temp_option.push({
                    "title": "A",
                    "content": "",
                    "checked": true,
                    "feedback": ""
                });
                break;
            case 1:
                record_option();
                temp_option.push({
                    "title": "B",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 2:
                record_option();
                temp_option.push({
                    "title": "C",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 3:
                record_option();
                temp_option.push({
                    "title": "D",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 4:
                record_option();
                temp_option.push({
                    "title": "E",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 5:
                record_option();
                temp_option.push({
                    "title": "F",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 6:
                record_option();
                temp_option.push({
                    "title": "G",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 7:
                record_option();
                temp_option.push({
                    "title": "H",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 8:
                record_option();
                temp_option.push({
                    "title": "I",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 9:
                record_option();
                temp_option.push({
                    "title": "J",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 10:
                record_option();
                temp_option.push({
                    "title": "K",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 11:
                record_option();
                temp_option.push({
                    "title": "L",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 12:
                record_option();
                temp_option.push({
                    "title": "M",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            case 13:
                record_option();
                temp_option.push({
                    "title": "N",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
            default:
                record_option();
                temp_option.push({
                    "title": "Extra",
                    "content": "",
                    "checked": false,
                    "feedback": ""
                });
                break;
        }

        init_option(temp_option);
    });

    $("#quiz_options_remove").on("click", function () {

        temp_option.pop();

        init_option(temp_option);
    });

    /*------ end add/remove option ------*/

})

function create_modal(type, key) {

    /*------ load video ------*/
    switch (type) {
        case "youtube":

            $(".player_wrap").eq(0).show();
            $(".player_wrap").eq(1).hide();

            player.loadVideoById({
                "videoId": key,
                "startSeconds": 0,
                "suggestedQuality": 'small'
            });

            //pauseVideo();
            break;
        case "helix":

            $(".player_wrap").eq(0).hide();
            $(".player_wrap").eq(1).show();

            setup_jwplayer(key);

            jwplayer_pauseVideo();

            break;
        default:
            break;
    }



    /*------ drag data from server & create current quiz list ------*/

}

function update_question_list(obj) {

    var $ul = $(".question_warp").children('ul').find('ul');
    $ul.empty();

    for (var i = 0; i < obj.items.length; i++) {
        var li = "<li name='" + i + "'>" + "Question " + (i + 1) + "<i class='fa fa-trash-o' aria-hidden='true' name='" + i + "'></i><i class='fa fa-pencil-square-o' aria-hidden='true' name='" + i + "'></i><i class='fa fa-info-circle' aria-hidden='true'  name='" + i + "'></i></li>";
        $ul.append(li);
    }

    $ul.find('li').each(function () {

        $(this).find('i').eq(0).on("click", function () {

            confirm_modal("Are you sure you want to delete this quiz question?")

            $(".question_preview_wrap").hide();
            var position = parseInt($(this).attr('name'));

            $("#confirm_delete").off();
            $("#confirm_delete").on("click", function () {

                var temp_array = []

                for (var i = 0; i < obj.items.length; i++) {
                    if (i != position) {
                        temp_array.push(obj.items[i]);
                    }
                }

                obj.items.length = 0;
                obj.items = temp_array;

                update_question_list(obj);

                reset();
            });


        });

        $(this).find('i').eq(1).on("click", function () {

            var position = parseInt($(this).attr('name'));

            edit_question(obj.items[position], position);

        });

        $(this).find('i').eq(2).on("click", function () {

            $(".question_preview_wrap").show();
            var position = parseInt($(this).attr('name'));

            // show preview
            var preview_arr = [];
            preview_arr = obj.items[position]

            $("#question_preview_type").empty();
            $("#question_preview_question").empty();
            $("#question_preview_option").empty();
            $("#question_preview_answer").empty();

            // transfer type
            var type = "";
            switch (preview_arr.type) {
                case "text":
                    type = "Question & Answer";
                    break;
                case "multiple_choice":
                    type = "Multipe Choice";
                    break;
                default:
                    break;
            }

            if (preview_arr.list) {
                $("#question_preview_type").text(type);
                $("#question_preview_question").text(decodeText(preview_arr.title));
                $("#question_preview_option").text(decodeText(preview_arr.list.toString()));
                $("#question_preview_answer").text(decodeText(preview_arr.ans.toString()));
            } else {
                $("#question_preview_type").text(type);
                $("#question_preview_question").text(decodeText(preview_arr.title));
                $("#question_preview_answer").text(decodeText(preview_arr.ans));
            }

        });

    });

}

function edit_question(obj, position) {

    switch (obj.type) {
        case "multiple_choice":

            /*------ bind click event to edit button ------*/
            $(".create_block_warp .nav-pills li").eq(0).find("a").trigger("click");
            $("#1a .quiz_text_area").val("");
            $("#1a .quiz_text_area").val(decodeText(obj.title));

            /*------ process data ------*/
            var preview_data = [];
            for (var i = 0; i < obj.list.length; i++) {
                var temp = {
                    checked: false,
                    content: "",
                    feedback: "",
                    title: ""
                };

                /*------ judege if is checked ------*/
                if (obj.list[i] === obj.ans[0]) {
                    temp.checked = true;
                }

                /*------ insert other data ------*/
                temp.title = obj.list[i].split(':')[0];
                temp.content = obj.list[i].split(':')[1];
                temp.feedback = obj.feedback[i];

                preview_data.push(temp);
            }

            temp_option = preview_data;

            init_option(temp_option);

            /*------ insert position info to finish & edit button ------*/
            $("#1a_edit_btn").attr("name", position);
            $("#1a_btn").hide();
            $("#1a_edit_btn").show();
            $("#1a_cancel_btn").show();

            /*------ bind event to to finish & edit button ------*/
            $("#1a_cancel_btn").off("click");
            $("#1a_cancel_btn").on("click", function () {

                $("#1a_btn").show();
                $("#1a_edit_btn").hide();
                $("#1a_cancel_btn").hide();
                temp_option.length = 0;

                setTimeout(function () {
                    $("#1a .quiz_text_area").val("");
                    $("#quiz_options_wrap tbody").empty();
                }, 200);
            });
            $("#1a_edit_btn").off("click");
            $("#1a_edit_btn").on("click", function () {

                var temp = {
                    title: "",
                    type: "multiple_choice",
                    list: [],
                    ans: [],
                    feedback: []
                };

                temp.title = encodeText($("#1a .quiz_text_area").val());

                var choices = [];
                $("#quiz_options_wrap tbody tr").each(function () {
                    var first_part = $(this).find('td').eq(0).text().trim().replace(" ", "");
                    var second_part = $(this).find('td').eq(1).find('input[type="text"]').val();

                    choices.push(encodeText(first_part + second_part));
                });

                var answer = [];
                $("#quiz_options_wrap tbody tr").each(function () {
                    var is_checked = $(this).find('td').eq(2).find('input[type="radio"]').prop('checked');
                    if (is_checked) {
                        var first_part = $(this).find('td').eq(0).text().trim().replace(" ", "");
                        var second_part = $(this).find('td').eq(1).find('input[type="text"]').val();

                        answer.push(encodeText(first_part + second_part));
                    }
                });

                var feedback = [];
                $("#quiz_options_wrap tbody tr").each(function () {
                    var feedback_str = $(this).find('td').eq(3).find('input[type="text"]').val();
                    feedback.push(encodeText(feedback_str));
                });

                /*------ create new quiz obj ------*/

                if (choices != "") {

                    temp.list = choices;
                    temp.ans = answer;
                    temp.feedback = feedback;

                    quiz_obj.items[position] = temp;

                    update_question_list(quiz_obj);

                    clear();
                    temp_option.length = 0;

                } else {
                    alert_modal("you need to fill required form.");
                }

                $("#1a_btn").show();
                $("#1a_edit_btn").hide();
                $("#1a_cancel_btn").hide();

                setTimeout(function () {
                    $("#1a .quiz_text_area").val("");
                    $("#quiz_options_wrap tbody").empty();
                }, 200);

            });

            break;
        case "text":

            /*------ bind click event to edit button ------*/
            $(".create_block_warp .nav-pills li").eq(1).find("a").trigger("click");
            $("#2a #quiz_text_area_question").val("");
            $("#2a #quiz_text_area_question").val(decodeText(obj.title));
            $("#2a #quiz_text_area_answer").val("");
            $("#2a #quiz_text_area_answer").val(decodeText(obj.ans));

            /*------ insert position info to finish & edit button ------*/
            $("#2a_edit_btn").attr("name", position);
            $("#2a_btn").hide();
            $("#2a_edit_btn").show();
            $("#2a_cancel_btn").show();

            /*------ bind event to to finish & edit button ------*/
            $("#2a_cancel_btn").off("click");
            $("#2a_cancel_btn").on("click", function () {

                $("#2a_btn").show();
                $("#2a_edit_btn").hide();
                $("#2a_cancel_btn").hide();

                setTimeout(function () {
                    $("#2a .quiz_text_area").each(function () {
                        $(this).val("");
                    });
                }, 200);
            });
            $("#2a_edit_btn").off("click");
            $("#2a_edit_btn").on("click", function () {

                quiz_obj.items[position].title = encodeText($("#2a #quiz_text_area_question").val());
                quiz_obj.items[position].ans = encodeText($("#2a #quiz_text_area_answer").val());

                update_question_list(quiz_obj);

                $("#2a_btn").show();
                $("#2a_edit_btn").hide();
                $("#2a_cancel_btn").hide();

                setTimeout(function () {
                    $("#2a .quiz_text_area").each(function () {
                        $(this).val("");
                    });
                }, 200);


            });

            break;
        default:
            break;
    }

}

function update_quiz_list(obj) {

    console.log(obj)

    var $ul = $(".quiz_warp ul ul");
    $ul.empty();

    for (var i = 0; i < obj.length; i++) {
        var li = "<li>" + obj[i].name + "<i class='fa fa-trash-o' aria-hidden='true' name='" + i + "'></i><i class='fa fa-pencil-square-o' aria-hidden='true' name='" + i + "'></i><i class='fa fa-info-circle' aria-hidden='true'  name='" + i + "'></i></li>";
        $ul.append(li);
    }

    $ul.find('li').each(function () {

        $(this).find('i').eq(0).on("click", function () {

            confirm_modal("Are you sure you want to delete this quiz and associated quiz questions?")

            var position = parseInt($(this).attr('name'));

            $("#confirm_delete").off();
            $("#confirm_delete").on("click", function () {

                var temp_array = [];

                for (var i = 0; i < obj.length; i++) {
                    if (i != position) {
                        temp_array.push(obj[i]);
                    }
                }

                obj.length = 0;
                obj = temp_array;
                set_quiz_meta = obj;

                $(".quiz_preview_wrap").hide();
                update_quiz_list(obj);

            });

        });

        $(this).find('i').eq(1).on("click", function () {


            $(".create_new_quiz_wrap").show();
            $(".quiz_save_btn_wrap").show();

            var position = parseInt($(this).attr('name'));

            /*------ pick data to insert into global various ------*/
            quiz_obj = set_quiz_meta[position];


            /*------ refresh create quiz time span ------*/
            $("#create_quiz_time_title").text('EDIT NEW QUIZ @');
            $("#create_quiz_time").text(seconds_to_HMS(quiz_obj.stop));
            $("#create_quiz_time").attr('stoptime', quiz_obj.stop);
            $("#quiz_save_btn").text("EDIT QUIZ @ " + seconds_to_HMS(quiz_obj.stop) + "  TO LIST ");

            update_question_list(quiz_obj);

        });

        $(this).find('i').eq(2).on("click", function () {

            var position = parseInt($(this).attr('name'));
            $(".quiz_preview_wrap").show();

            var preview_obj = {};
            preview_obj = obj[position];

            /*------ insert data ------*/
            $("#quiz_preview_name").text(decodeText(preview_obj.name));
            $("#quiz_preview_stop").text(decodeText(preview_obj.stop + "s"));
            $("#quiz_preview_details").empty();

            var head = "<tr>" +
                "<th>Title</th>" +
                "<th>Type</th>" +
                "<th>list</th>" +
                "<th>feedback</th>" +
                "<th>answer</th>" +
                "</tr>";

            $("#quiz_preview_details").append(head);

            for (var i = 0; i < preview_obj.items.length; i++) {

                switch (preview_obj.items[i].type) {
                    case "text":

                        var tr = "<tr>" +
                            "<td>" + decodeText(preview_obj.items[i].title) + "</td>" +
                            "<td>" + decodeText(preview_obj.items[i].type) + "</td>" +
                            "<td>N/A</td>" +
                            "<td>N/A</td>" +
                            "<td>" + decodeText(preview_obj.items[i].ans) + "</td>" +
                            "</tr>";

                        break;
                    case "multiple_choice":

                        var tr = "<tr>" +
                            "<td>" + decodeText(preview_obj.items[i].title) + "</td>" +
                            "<td>" + decodeText(preview_obj.items[i].type) + "</td>" +
                            "<td>" + decodeText(preview_obj.items[i].list.toString()) + "</td>" +
                            "<td>" + decodeText(preview_obj.items[i].feedback.toString()) + "</td>" +
                            "<td>" + decodeText(preview_obj.items[i].ans.toString()) + "</td>" +
                            "</tr>";

                        break;
                    default:
                        break;
                }

                $("#quiz_preview_details").append(tr);

            }


        });

    });

}

function clear() {
    $("#1a").children('textarea').each(function () {
        $(this).val("");
    });
    $("#1a").children('input').each(function () {
        $(this).tagsinput('removeAll')
    })

    $("#quiz_options_wrap tbody").empty();

    $("#2a").children('textarea').each(function () {
        $(this).val("");
    });

}

function reset() {

    /*------ clean multiple choice ------*/
    $("#1a_btn").show();
    $("#1a_edit_btn").hide();
    $("#1a_cancel_btn").hide();
    $("#1a .quiz_text_area").val("");
    $("#1a_cancel_btn").trigger("click");

    /*------ clean Q & A ------*/
    $("#2a_btn").show();
    $("#2a_edit_btn").hide();
    $("#2a_cancel_btn").hide();
    $("#2a #quiz_text_area_question").val("");
    $("#2a #quiz_text_area_answer").val("");

}

function submit_quiz_to_server(creator_id, identifier, media_type, quiz_data) {
    $.ajax({
        type: "POST",
        url: "/store_quiz",
        data: {
            creator_id: creator_id,			//int
            identifier: identifier,  		//stirng
            media_type: media_type,		    //string
            quiz_data: quiz_data            //obj
        },
        success: function (res) {
            if (res.result === "success") {
                $("#quiz_create_modal").modal("hide");

                alert_modal("quiz submit successfully");
            }
        },
        error: function (request, status, error) {

            alert_modal("error submit quiz - " + error)
        }
    });
}

function change_tag_status(identifier, media_type) {
    $("#quiz_submit_btn").attr("identifier", "");
    $("#quiz_submit_btn").attr("media_type", "");
    $("#quiz_submit_btn").attr("identifier", identifier);
    $("#quiz_submit_btn").attr("media_type", media_type);
}

function init_option(data) {

    $("#quiz_options_wrap tbody").empty();

    for (var i = 0; i < data.length; i++) {
        var $tr = $("<tr></tr>");


        if (data[i].checked) {

            var td = "<td>&nbsp;&nbsp; " + data[i].title + " : &nbsp;&nbsp;</td>" +
                "<td><input type='text' value='" + data[i].content + "'></input></td>" +
                "<td>" +
                "<input type='radio' class='radio_opt_button' id='radio_opt_button" + i + "' name='radio-group' checked>" +
                "<label for='radio_opt_button" + i + "'>Answer</label>" +
                "</td>" +
                "<td><input type='text' value='" + data[i].feedback + "' placeholder=' Instructor feedback'></input></td>";

        } else {

            var td = "<td>&nbsp;&nbsp; " + data[i].title + " : &nbsp;&nbsp;</td>" +
                "<td><input type='text' value='" + data[i].content + "'></input></td>" +
                "<td>" +
                "<input type='radio' class='radio_opt_button' id='radio_opt_button" + i + "' name='radio-group'>" +
                "<label for='radio_opt_button" + i + "'>Answer</label>" +
                "</td>" +
                "<td><input type='text' value='" + data[i].feedback + "' placeholder='Instructor feedback'></input></td>";

        }

        $tr.append(td);

        $("#quiz_options_wrap tbody").append($tr);
    }

}

function record_option() {
    var tr_length = $("#quiz_options_wrap tbody tr").length;
    for (var i = 0; i < tr_length; i++) {
        temp_option[i].content = $("#quiz_options_wrap tbody tr").eq(i).find('input[type="text"]').eq(0).val();
        temp_option[i].checked = $("#quiz_options_wrap tbody tr").eq(i).find('input[type="radio"]').prop('checked');
        temp_option[i].feedback = $("#quiz_options_wrap tbody tr").eq(i).find('input[type="text"]').eq(1).val();
    }
}

function create_instruction() {
    var instructution = "<h3>OVAL Quiz Setup Tutorial</h3>" +
        "<h4 style='font-weight:bolder'>You can have multiple quizzes per video and multiple quiz questions per quiz. The instructions below are to setup each quiz.</h4>" +
        "<h4 style='font-weight:bolder'>1. Pause video to set quiz</h4>" +
        "<p>SECTION A : video pre-view window, SECTION B : Quiz Creation window </p>" +
        "<p>Please pause video at specified time, (such as at 10th second), then the SECTION B will automatically display.</p>" +
        "<p>In SECTION B, you are able to setup two sorts of questions for each quiz, multiple choice and Short answer question. </p>" +
        "<div class='img_wrap'><img src='../../img/instruction_1.png' /></div>" +
        "<h4 style='font-weight:bolder'>2. Submit Quiz at specified time </h4>" +
        "<p>SECTION C : question list, SECTION D : Quiz save button </p>" +
        "<p>You could use SECTION C to check the current quiz's question list & review questions</p>" +
        "<p>Press SECTION D button to store this quiz to the quiz list.</p>" +
        "<div class='img_wrap'><img src='../../img/instruction_2.png' /></div>" +
        "<h4 style='font-weight:bolder'>3. Submit Quiz to server</h4>" +
        "<p>SECTION E : quiz list, SECTION F : Submit button </p>" +
        "<p>You could use SECTION E to check quiz for this video</p>" +
        "<p><strong>IMPORTANT:</strong>Do not forget to press SECTION F button to submit your quiz to server, if you do not press this button, it may lead to lost data.</p>" +
        "<div class='img_wrap'><img src='../../img/instruction_3.png' /></div>";

    $("#pop_out_dialog_body").empty();
    $("#pop_out_dialog_body").append(instructution);
}

function alert_modal(message) {
    $("#alert_dialog_content").empty();

    var content = "<h3>" + message + "</h3>";
    $("#alert_dialog_content").append(content);

    $("#alert_dialog").modal({
        backdrop: 'static',
        keyboard: false
    });

}

function confirm_modal(message, cb) {

    $("#confirm_dialog_content").empty();

    var content = "<h3>" + message + "</h3>";
    $("#confirm_dialog_content").append(content);

    $("#confirm_dialog").modal({
        backdrop: 'static',
        keyboard: false
    });

}

// })(jQuery);

/*------ youtube api ------*/

var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        width: '100%',
        videoId: '',
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange(event) {
    var videoTime = player.getCurrentTime().toFixed(0);
    var action = "";

    if (event.data == YT.PlayerState.PLAYING) {
        action = "Play";
    } else if (event.data == YT.PlayerState.PAUSED) {
        action = "Paused";
    } else if (event.data == YT.PlayerState.ENDED) {
        action = "Ended";
    } else if (event.data == YT.PlayerState.BUFFERING) {
        action = "Buffering";
    } else if (event.data == YT.PlayerState.CUED) {
        action = "Cued";
    }

    switch (action) {
        case "Paused":

            $(".create_new_quiz_wrap").show();
            $(".quiz_save_btn_wrap").show();
            $("#create_quiz_time_title").text("CREATE NEW QUIZ @ ");
            $("#create_quiz_time").text(seconds_to_HMS(videoTime));
            $("#create_quiz_time").attr('stoptime', videoTime);

            $(".quiz_save_btn_wrap button").text("CREATE QUIZ @ " + seconds_to_HMS(videoTime) + " TO LIST ");
            $(".quiz_save_btn_wrap button").append("<i class='fa fa-save' aria-hidden='true'></i>");

            /*------ @22/Feb/2018: check if this time spot has existed question, if has, then load exist questions, if not clear question list and input area ------*/


            $(".question_warp ul ul").empty();
            $("#1a .quiz_text_area").val("");
            $("#quiz_options_wrap tbody").empty();
            $("#2a #quiz_text_area_question").val("");
            $("#2a #quiz_text_area_answer").val("");

            for (var i in set_quiz_meta) {
                if (parseInt(videoTime) === set_quiz_meta[i].stop) {

                    update_question_list(set_quiz_meta[i]);

                }
            }

            /*------ @22/Feb/2018: end check if this time spot has existed question, if has, then load exist questions, if not clear question list and input area ------*/

            break;
        case "Play":

            $(".create_new_quiz_wrap").hide();
            $(".question_preview_wrap").hide();
            $(".quiz_save_btn_wrap").hide();

            break;
        default:

            break;
    }

}

function pauseVideo() {
    player.pauseVideo();
}

/*------ youtube api ------*/

/*------ helix media api ------*/

var tag = document.createElement('script');
tag.src = helix_js_host + "/jwplayer.js";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function isJWPlayerLoaded() {
    return typeof (jwplayer) == 'function';
}
var timer6852 = null;
var timerCounter6852 = 0;

function setup_jwplayer(video_identifier) {
    if (!isJWPlayerLoaded()) {
        timerCounter6852 = window.setTimeout(function () {
            timerCounter6852++;
            if (timerCounter6852 < 10) {
                setup_jwplayer(video_identifier);
            } else {
                window.clearTimeout(timer6852);
            }
        }, 1000);
        return;
    }

    var file = video_identifier + "_lo.mp4";
    var helix_host = helix_server_host.split('://').pop();
    var thumbnail_url = helix_server_host + "/thumbnails/" + video_identifier + ".jpg";

    setTimeout(function () {
        jwplayer("jwplayer").setup({
            "id": "jwplayer",
            "width": "98%",
            "provider": "rtmp",
            "streamer": "rtmp://" + helix_host + "/flash/",
            "file": file,
            "image": thumbnail_url,
            "modes": [
                {
                    type: "html5",
                    config: {
                        "file": helix_server_host + "/flash/" + file,
                        "provider": "video",
                    }
                },
            ],
            "plugins": {

            }
        });

        jwplayer().onPlay(function () {

        });
        jwplayer().onPause(function () {

            var videoTime = jwplayer().getPosition().toFixed(0);

            $(".create_new_quiz_wrap").show();
            $(".quiz_save_btn_wrap").show();
            $("#create_quiz_time").text(seconds_to_HMS(videoTime));
            $("#create_quiz_time").attr('stoptime', videoTime)

            $(".quiz_save_btn_wrap button").text("CREATE QUIZ @ " + seconds_to_HMS(videoTime) + " TO LIST ");
            $(".quiz_save_btn_wrap button").append("<i class='fa fa-save' aria-hidden='true'></i>");

            /*------ @22/Feb/2018: check if this time spot has existed question, if has, then load exist questions, if not clear question list and input area ------*/


            $(".question_warp ul ul").empty();
            $("#1a .quiz_text_area").val("");
            $("#quiz_options_wrap tbody").empty();
            $("#2a #quiz_text_area_question").val("");
            $("#2a #quiz_text_area_answer").val("");

            for (var i in set_quiz_meta) {
                if (parseInt(videoTime) === set_quiz_meta[i].stop) {

                    update_question_list(set_quiz_meta[i]);

                }
            }

            /*------ @22/Feb/2018: end check if this time spot has existed question, if has, then load exist questions, if not clear question list and input area ------*/


        });
        jwplayer().onComplete(function () {

        });
        jwplayer("jwplayer").onTime(function (e) {

        });

    }, 500);

}

function jwplayer_pauseVideo() {
    jwplayer("jwplayer").pause(true);
}

function currentVideoTime() {
    return jwplayer("jwplayer").getPosition();
}

/*------ helix media api ------*/


/*------ general function ------*/
function seconds_to_HMS(second) {
    second = Number(second);
    var h = Math.floor(second / 3600);
    var m = Math.floor(second % 3600 / 60);
    var s = Math.floor(second % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hour : " : " hours : ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute : " : " minutes : ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
    return hDisplay + mDisplay + sDisplay;
}
/*------ end general function ------*/


/*------ string process functions ------*/
function encodeText(txt) {
    return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
function decodeText(txt) {
    return txt.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'");
}
/*------ string process functions ------*/


