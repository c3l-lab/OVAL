 <div class="modal " id="annotation-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="modalLabel">Modal title</h4>
                 <div class="edit-instruction">
                     <button type="button" id="edit-instruction-button" class="btn btn-link">
                         COMMENT INSTRUCTION
                         <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                     </button>
                 </div><!-- edit-instruction -->
                 <div class="flex flex-wrap justify-between">
                     <div class="edit-annotation-time">
                         <button type='button' id="rewind-button"><i class='fa fa-step-backward'></i></button>
                         <span id='time-label'>01:15</span>
                         <button type='button' id="forward-button"><i class='fa fa-step-forward'></i></button>
                     </div>
                     <div class="flex items-center anno-dynamic-content">
                         <div class="font-bold mr-2 text-gray-600">STRUCTURED ANNOTATION</div>
                         <label class="switch mb-0">
                             <input id="toggle-anno-question-mode-switch" type="checkbox" name="" class="">
                             <span class="slider round"></span>
                         </label>
                     </div>
                 </div>
                 <div class="meta-data">
                     <div class="username"></div>
                     <div class="privacy-icon"></div>
                     <div class="date"></div>
                 </div><!-- meta-data -->
             </div><!-- .modal-header -->
             <div class="modal-body">
                 <form id="annotation-form" role="form" data-toggle="validator">
                     <div id="anno-text-mode-input" class="form-group">
                         <label for="annotation-description" id="annotation-instruction"></label>
                         <textarea id="annotation-description" name="annnotation-description" rows="10" placeholder="Your comment ..."
                             required></textarea>
                         <div class="help-block with-errors"></div>
                     </div><!-- form-group -->

                     <div id="anno-question-mode-input" class="form-group anno-dynamic-content">
                         <div class="create_new_quiz_wrap px-0 overflow-auto">
                             <div class="question_warp px-0">
                                 <ul>
                                     <div style="width: 100%">
                                         <span class="title">Question List</span>
                                         <i class="fa fa-caret-down" aria-hidden="true" id="quiz_list_toggle_btn"></i>
                                     </div>
                                     <ul></ul> <!-- IMPORTANT, $(".question_warp").children('ul').find('ul') -->
                                 </ul>
                             </div>


                             <div class="question_preview_wrap" style="display:none;width:100%">
                                 <h2 class="flex justify-between items-center">
                                     Question preview
                                     <button id="question_preview_wrap_close" type="button"
                                         class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full px-4 py-2.5 text-center mx-2 my-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                         <i class="fa fa-close text-3xl"></i>
                                     </button>
                                 </h2>
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

                             <br>

                             <div class="create_block_warp relative">
                                 <ul class="nav nav-pills">
                                     <li class="active">
                                         <a href="#1a" data-toggle="tab">MULTIPLE CHOICE</a>
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

                                             </tbody>
                                         </table>

                                         <button class="quiz_options_btn" id="quiz_options_add">Add option
                                             <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                         <button class="quiz_options_btn" id="quiz_options_remove">Remove
                                             option <i class="fa fa-minus-circle" aria-hidden="true"></i></button>

                                         <br>

                                         <button type="button" id="1a_btn" blocktype="multiple_choice"
                                             class="top-0 right-0 absolute text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-lg text-4xl px-5 py-2.5 text-center mt-4 mx-2">
                                             Add <i class="fa fa-plus" aria-hidden="true"></i>
                                         </button>
                                         <button type="button" class="btn btn-link pull-left add_question_list_btn"
                                             id="1a_edit_btn" blocktype="text" style="display:none;">
                                             Finish &amp; Add to list <i class="fa fa-pencil-square-o"
                                                 aria-hidden="true"></i>
                                         </button>
                                         <button type="button" class="btn btn-link pull-left add_question_list_btn"
                                             id="1a_cancel_btn" blocktype="text" style="display:none;">
                                             Cancle Edit <i class="fa fa-ban" aria-hidden="true"></i>
                                         </button>
                                     </div>
                                     <div class="tab-pane" id="2a">
                                         <h4>Please input your Question</h4>
                                         <textarea class="quiz_text_area" rows="2" id="quiz_text_area_question"></textarea>
                                         <h4>Please input your default Answer</h4>
                                         <textarea class="quiz_text_area" rows="2" id="quiz_text_area_answer"></textarea>
                                         <button type="button" id="2a_btn" blocktype="text"
                                             class="top-0 right-0 absolute text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-lg text-4xl px-5 py-2.5 text-center mx-2 mt-4">
                                             Add <i class="fa fa-plus" aria-hidden="true"></i>
                                         </button>
                                         <button type="button" class="btn btn-link pull-left add_question_list_btn"
                                             id="2a_edit_btn" blocktype="text" style="display:none;">
                                             Finish &amp; Add to list <i class="fa fa-pencil-square-o"
                                                 aria-hidden="true"></i>
                                         </button>
                                         <button type="button" class="btn btn-link pull-left add_question_list_btn"
                                             id="2a_cancel_btn" blocktype="text" style="display:none;">
                                             Cancle Edit <i class="fa fa-ban" aria-hidden="true"></i>
                                         </button>
                                     </div>
                                 </div>

                             </div>

                         </div>
                     </div><!-- form-group -->

                     <div class="form-group">
                         <i class="fa fa-tags"></i>
                         <input type="text" id="tags" name="tags"
                             placeholder="Tags separated by comma ...">
                     </div><!-- form-group -->

                     <div id="annotation-visibility-form" class="form-group">
                         <label for="private" class="private-radio-label">
                             <input type="radio" id="private" name="privacy-radio" value="private">
                             <span>Private</span>
                         </label>
                         <label for="public" class="public-radio-label">
                             <input type="radio" id="public" name="privacy-radio" value="all"
                                 checked="checked">
                             <span>All students in course</span>
                         </label>
                         <label for="nominated" class="nominated-radio-label">
                             <input type="radio" id="nominated" name="privacy-radio" value="nominated">
                             <span>Nominated students</span>
                         </label>
                         <div id="nominated-selection" class="row justify-content-center">
                             <div class="col-xs-8 col-xs-offset-2 space-top">
                                 <select class="form-control inputstl" id="nominated-students-list"
                                     multiple="multiple" required>
                                 </select>
                                 <div class="help-block with-errors"></div>
                             </div><!-- col-md-8 -->
                         </div><!-- row -->
                     </div><!-- form-group -->
                     <div class="form-buttons">
                         <button type="button" class="btn btn-link" id="save">
                             <i class="fa fa-save" aria-hidden="true"></i>
                         </button>

                         <button type="button" class="btn btn-link pull-right" id="delete"><i
                                 class="fa fa-trash"></i></button>
                     </div><!-- form-group -->
                 </form>
             </div><!-- .modal-body -->
         </div><!-- .modal-content -->
     </div><!-- .modal-dialog -->
 </div><!-- .modal -->

 <div id="structure-annotation-question-modal" class="modal" tabindex="-1" role="dialog"
     aria-labelledby="modalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="modalLabel">Question Sheet</h4>
             </div><!-- .modal-header -->
             <div class="modal-body" id="structure-annotation-question-sheet">
                 <form class="modal-body">
                 </form>
                 <button id="structure-annotation-answer-submit">Submit</button>
             </div>
             <div class="modal-body" id="structure-annotation-question-result">
                 <table class="analytics_wrap">
                     <tbody>
                         <tr>
                             <th>Question Type</th>
                             <th>Question Title</th>
                             <th>Your Answer</th>
                             <th>Correctness Checking</th>
                             <th>Instructor Feedback</th>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>

 <div id="feedback" class="modal">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="modalLabel">VIDEO KEY POINTS</h4>
             </div><!-- modal-header -->
             <div class="modal-body">

                 <form>
                     <div class="form-group">
                         <div class="space-bottom">
                             Below is a list of the correct key points you should have provided in your comment. Select
                             the ones that match your response.
                         </div>
                         <div id="point-instruction">
                         </div>
                         <div id="feedback-content">
                         </div><!-- form-content -->
                     </div><!-- form-group -->


                     <div class="form-group">
                         <label for="confidence-level">
                             What is your level of confidence that your comment fully captures the key points of the
                             video?
                         </label>
                         <select class="form-control" id="confidence-level" name="confidence-level">
                             <option value="0">Please select...</option>
                             <option value="5">Very High</option>
                             <option value="4">High</option>
                             <option value="3">Medium</option>
                             <option value="2">Low</option>
                             <option value="1">Very Low</option>
                         </select>
                     </div><!-- form-group -->
                     <div class="form-buttons">
                         <button type="button" class="btn btn-link modal-text-button" id="re-enter-comment">
                             <i class="fa fa-chevron-left" aria-hidden="true"></i>
                             Back
                         </button>
                         <button type="button" class="btn btn-link center-block" id="save-points"><i
                                 class="fa fa-save"></i></button>
                     </div><!-- form-buttons -->
                 </form>
             </div><!-- modal-body -->
         </div><!-- modal-content -->
     </div><!-- modal-dialog -->
 </div><!-- #feedback -->


 <div id="preview">
     <div class="modal-content">
         <div class="modal-header">
             <button type="button" class="close" id="close-preview-button">
                 <i class="fa fa-close"></i>
             </button>
             <div class="play-icon"><button class="btn btn-link play-annotation-button"><i
                         class="fa fa-play-circle"></i></button></div>
             <div class="time-label">0:15</div>
             <div class="meta-data">
                 <button type="button" class="btn btn-link edit-annotation-button">
                     <i class="fa fa-pencil-square-o"></i>
                 </button>


                 <div class="username"></div>
                 <div class="privacy-icon"><i class="fa fa-eye"></i></div>
                 <div class="date">25 December 2016</div>
             </div><!-- meta-data -->
         </div><!-- .modal-header -->
         <div class="modal-body">
             <div class="form-group">
                 <div class="preview-comment vertical-scroll">comment text goes here...</div>
             </div><!-- form-group -->
             <div>
                 <i class="fa fa-tags"></i>
                 <div class="preview-tags"></div>
             </div><!-- form-group -->
         </div><!-- .modal-body -->
     </div><!-- .modal-content -->
 </div><!-- #preview -->

 <div class="modal" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="modal-video-title"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="modal-video-title">RELATED VIDEO</h4>
             </div><!-- modal-header -->
             <div class="modal-body">
                 <div id="modal-player">
                     <iframe id="modal-iframe" width="543" height="300"></iframe>
                 </div><!-- #modal-player -->
             </div><!-- modal-body -->
         </div><!-- modal-content -->
     </div><!-- modal-dialog -->
 </div><!-- #video-modal -->


 <div class="modal" id="comment-instruction-modal" tabindex="-1" role="dialog"
     aria-labelledby="instruction-modal-title" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="instruction-modal-title">GENERAL COMMENT REQUIREMENTS</h4>
             </div><!-- modal-header -->
             <div class="modal-body">
                 <form id="comment-instruction-form" role="form" data-toggle="validator">
                     <div class="form-group">
                         <label for="comment-instruction-description">
                             Please add your requirements here and it will be displayed to your students when they
                             write comments.
                         </label>
                         <textarea id="comment-instruction-description" name="comment-instruction-description" rows="3"
                             placeholder="Enter instruction ..." required></textarea>
                         <div class="help-block with-errors"></div>
                     </div><!-- form-group -->
                     <div class="form-buttons">
                         <button type="button" class="btn btn-link" id="save-comment-instruction"><i
                                 class="fa fa-save"></i></button>
                         <button type="button" class="btn btn-link pull-right" id="delete-comment-instruction"><i
                                 class="fa fa-trash"></i></button>
                     </div><!-- form-group -->
                 </form>
             </div><!-- modal-body -->
         </div><!-- modal-content -->
     </div><!-- modal-dialog -->
 </div><!-- #comment-instruction-modal -->


 <div class="modal" id="same-tag-modal" tabindex="-1" role="dialog" aria-labelledby="same-tag-modal-title"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title" id="same-tag-modal-title"></h4>
             </div><!-- modal-header -->
             <div class="modal-body">

             </div><!-- .modal-body -->
         </div><!-- .modal-content -->
     </div><!-- .modal-dialog -->
 </div><!-- #comment-with-same-tag-modal -->



 <!-- Quiz Modal content-->
 <div id="quiz_modal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <i class="fa fa-close" aria-hidden="true"></i>
                 </button>
                 <h4 class="modal-title">Oval Quiz</h4>
             </div>
             <div class="modal-body">
                 <form id="annotation-form" role="form" data-toggle="validator">
                     <div class="form-group">
                         <ul class="client_question_list_wrap">
                         </ul>
                     </div>
                     <div class="form-buttons" style="text-align:center;">
                         <button type="button" class="btn btn-link" id="submit_result"><i
                                 class="fa fa-save"></i></button>
                         <!-- <button type="button" class="btn btn-link pull-right"><i class="fa fa-pencil-square-o" data-dismiss="modal"></i></button> -->
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <!-- End Quiz Modal content-->

 <!-- alert modal -->
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
                 <button type="button" class="btn btn-link center-block quiz_submit_btn"
                     onclick="$('#alert_dialog').modal('hide');">
                     OK, I got it. &nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i>
                 </button>
             </div>
         </div>
     </div>
 </div>

 <!-- end alert modal -->

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
                 <button type="button" class="btn btn-link center-block" id="confirm_delete"
                     onclick="$('#confirm_dialog').modal('hide');">
                     Continue
                 </button>
                 <button type="button" class="btn btn-link center-block"
                     onclick="$('#confirm_dialog').modal('hide');">
                     Cancle
                 </button>
             </div>
         </div>
     </div>
 </div>

 <!-- feedback modal -->
 <div id="feedback_dialog" class="modal fade" tabindex="-1" style="display: none;">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" aria-hidden="true"
                     onclick="$('#feedback_dialog').modal('hide'); $('#quiz_modal').modal('hide');">×</button>
                 <h4 class="modal-title">Oval Notification</h4>
             </div>
             <div class="modal-body" id="feedback_dialog_content">
                 <table class="analytics_wrap">
                     <tbody>
                         <tr id="feedback_dialog_content_table_head">
                             <th>Question Type</th>
                             <th>Question Title</th>
                             <th>Your Answer</th>
                             <th>Correctness Checking</th>
                             <th>Instructor Feedback</th>
                         </tr>
                     </tbody>
                 </table>
             </div>
             <div>
                 <button type="button" class="btn btn-link center-block quiz_submit_btn"
                     onclick="$('#feedback_dialog').modal('hide'); $('#quiz_modal').modal('hide');">
                     OK, I got it. &nbsp;&nbsp;&nbsp;<i class="fa fa-thumbs-up" aria-hidden="true"></i>
                 </button>
             </div>
         </div>
     </div>
 </div>
 <!-- end feedback modal -->
