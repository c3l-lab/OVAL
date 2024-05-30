 <div class="modal" id="annotation-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
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
                 <div class="edit-annotation-time">
                     <button type='button' id="rewind-button"><i class='fa fa-step-backward'></i></button>
                     <span id='time-label'>01:15</span>
                     <button type='button' id="forward-button"><i class='fa fa-step-forward'></i></button>
                 </div>
                 <div class="meta-data">
                     <div class="username"></div>
                     <div class="privacy-icon"></div>
                     <div class="date"></div>
                 </div><!-- meta-data -->
             </div><!-- .modal-header -->
             <div class="modal-body">
                 <form id="annotation-form" role="form" data-toggle="validator">
                     <div class="form-group">
                         <label for="annotation-description" id="annotation-instruction"></label>
                         <textarea id="annotation-description" name="annnotation-description" rows="10" placeholder="Your comment ..."
                             required></textarea>
                         <div class="help-block with-errors"></div>
                     </div><!-- form-group -->
                     <div class="form-group">
                         <i class="fa fa-tags"></i>
                         <input type="text" id="tags" name="tags" placeholder="Tags separated by comma ...">
                     </div><!-- form-group -->

                     <div id="annotation-visibility-form" class="form-group">
                         <label for="private" class="private-radio-label">
                             <input type="radio" id="private" name="privacy-radio" value="private">
                             <span>Private</span>
                         </label>
                         <label for="public" class="public-radio-label">
                             <input type="radio" id="public" name="privacy-radio" value="all" checked="checked">
                             <span>All students in course</span>
                         </label>
                         <label for="nominated" class="nominated-radio-label">
                             <input type="radio" id="nominated" name="privacy-radio" value="nominated">
                             <span>Nominated students</span>
                         </label>
                         <div id="nominated-selection" class="row justify-content-center">
                             <div class="col-xs-8 col-xs-offset-2 space-top">
                                 <select class="form-control inputstl" id="nominated-students-list" multiple="multiple"
                                     required>
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
