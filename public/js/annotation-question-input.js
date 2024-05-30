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

var set_quiz_meta = [];

var temp_option = [];

/*------ create new quiz obj ------*/
var quiz_obj = {
    items: []
};
window.quiz_obj = quiz_obj; // used in home.js

var is_instructor = false;
var user_id = 0;

////////////#quiz_submit_btn
///////////update_quiz_list
$(document).ready(function () {
    /*------ add question function ------*/
    $("#1a_btn").on("click", function () {
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
        var block_type = $(this).attr("blockType");
        var question = $("#2a #quiz_text_area_question").val();
        var answer = $("#2a #quiz_text_area_answer").val();

        if (question === "") {
            alert_modal("you need to fill required form.");
            return;
        }

        var temp = {
            title: encodeText(question),
            type: block_type,
            ans: encodeText(answer),
        };

        quiz_obj.items.push(temp)

        update_question_list(quiz_obj);

        clear();
        temp_option.length = 0;
    });

    $(".question_preview_wrap").find('#question_preview_wrap_close').on('click', function (e) {
        $(".question_preview_wrap").hide();
        $(".create_block_warp").show();
        $(".question_warp").show();
    });

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

    $("#quiz_list_toggle_btn").on("click", function () {
        $(this).parent().siblings('ul').toggle();
        $(this).toggleClass('fa-caret-down fa-list-ul');
    });
})


function update_question_list(obj) {
    var $ul = $(".question_warp").children('ul').find('ul');
    $ul.empty();

    for (var i = 0; i < obj.items.length; i++) {
        var li = `<li name='${i}' style='width:100%'>
                    Question ${i + 1}
                    <i class='fa fa-trash-o' aria-hidden='true' name='${i}' style='font-size:21px'></i>
                    <i class='fa fa-pencil-square-o' aria-hidden='true' name='${i}' style='font-size:21px'></i>
                    <i class='fa fa-info-circle' aria-hidden='true' name='${i}' style='font-size:21px'></i>
                </li>`;
        $ul.append(li);
    }

    $ul.find('li').each(function () {
        const actions = $(this).find('i'); // delete:0,edit:1,info:2
        actions.eq(0).on("click", function () {
            confirm_modal("Are you sure you want to delete this quiz question?")
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

        actions.eq(1).on("click", function () {
            var position = parseInt($(this).attr('name'));
            edit_question(obj.items[position], position);
        });

        actions.eq(2).on("click", function () {
            $(".question_warp").hide();
            $(".create_block_warp").hide();
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

function submit_questions_to_server(creator_id, group_video_id, media_type, quiz_data) {
    $.ajax({
        type: "PUT",
        url: "/group_videos/" + group_video_id + "/quiz",
        data: {
            creator_id: creator_id,			//int
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

function change_tag_status(identifier, media_type, group_video_id) {
    $("#quiz_submit_btn").attr("identifier", "");
    $("#quiz_submit_btn").attr("media_type", "");
    $("#quiz_submit_btn").attr("identifier", identifier);
    $("#quiz_submit_btn").attr("media_type", media_type);
    $("#quiz_submit_btn").data("group-video-id", group_video_id);
}

function init_option(data) {

    $("#quiz_options_wrap tbody").empty();

    for (let i = 0; i < data.length; i++) {
        let $tr = $("<tr></tr>");


        if (data[i].checked) {

            var td = `
                <td>&nbsp;&nbsp; ${data[i].title} : &nbsp;&nbsp;</td>
                <td><input type="text" value="${data[i].content}"></td>
                <td>
                    <input type="radio" class="radio_opt_button" id="radio_opt_button${i}" name="radio-group" checked>
                    <label for="radio_opt_button${i}">Answer</label>
                </td>
                <td>
                    <input type="text" value="${data[i].feedback}" placeholder="Instructor feedback">
                </td>
            `;

        } else {

            var td = `
                <td>&nbsp;&nbsp; ${data[i].title} : &nbsp;&nbsp;</td>
                <td><input type="text" value="${data[i].content}"></td>
                <td>
                    <input type="radio" class="radio_opt_button" id="radio_opt_button${i}" name="radio-group">
                    <label for="radio_opt_button${i}">Answer</label>
                </td>
                <td>
                    <input type="text" value="${data[i].feedback}" placeholder="Instructor feedback">
                </td>
            `;

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

/*------ string process functions ------*/
function encodeText(txt) {
    return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
function decodeText(txt) {
    return txt.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'");
}
/*------ string process functions ------*/


