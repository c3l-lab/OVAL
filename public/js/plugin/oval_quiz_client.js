/**
 * oval quiz plugin
 * yin_gong<Max.Gong@unisa.edu.au>
 */
(function ($) {
    "use strict";

    /**
     *
    var quiz_meta = [{
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
        items: [{
            title: "question 1 title LALALA",
            type: "text" //type: "text","multiple_choice"......
        },{
            title: "demo question 2 title",
            type: "multiple_choice",
            list: ["choice 1", "choice 2", "choice 3"]
        },{
            title: "demo question 3 title",
            type: "multiple_choice",
            list: ["lala 1", "lala 2", "lala 3"]
        }]
    }, {
        name: "demo_quiz_3",
        stop: 15, //stop @ which second
        items: []
    }];
     *
     */

    var quiz_meta = [];

    var flag = false; //true for modal fired, false for not fired

    //TODO： split to two branch
    var intervalID_youtubeplayer = window.setInterval(
        function () {

            if(quiz_meta.length > 0){

                var current_video_time = currentVideoTime();
                var trigger = true;
                var meta_position = 0;

                for(var i=0; i< quiz_meta.length; i++){
                    if(trigger  && current_video_time <= quiz_meta[i].stop){
                        meta_position = i;
                        trigger = false
                    }
                }

                if(meta_position > 0){
                    var quiz_stop = quiz_meta[meta_position].stop
                }else{
                    var quiz_stop = quiz_meta[0].stop
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

    function show_quiz(data) {
        pauseVideo();
        show_quiz_modal(data)
    }

    function show_quiz_modal(data,cb){
        if(!flag){
            //console.log("/*--------------show quiz--------------*/");
            //console.log("/*--------------" + data.name + "--------------*/");
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

                        // var choice_list = $("<div class='quiz_radio_wrap'></div>");
                        // for(var j = 0; j < data.items[i].list.length; j++){
                        //     var choice = "<div class='radio'>" +
                        //                     "<label class='checkbox-inline'><input type='radio' name='quiz_q" + (parseInt(i) + 1) + "'>" + data.items[i].list[j] + "</label>" +
                        //                  "</div>";
                        //     choice_list.append(choice);
                        // }

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
        }

        return cb();
    }


})(jQuery);
