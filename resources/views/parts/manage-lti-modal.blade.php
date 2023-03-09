<div class="modal fade" id="edit-lti-modal" tabindex="-1" role="dialog" aria-labelledby="edit-lti-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title" id="edit-lti-title">EDIT LTI CONNECTION</h4>
			</div><!-- .modal-header -->
			
			<div class="modal-body container-fluid">
				<form id="assign-video-to-group-form" role="form" data-toggle="validator">
					<div class="row">
						<div class="col-xs-10 col-xs-offset-1">
                            <div class="form-group">
                                <label for="lti-name">Name</label>
                                <input type="text" id="lti-name" class="form-control" name="name">
                            </div><!-- form-group -->

                            <div class="form-group">
                                <label for="lti-key">Key</label>
                                <input type="text" id="lti-key" class="form-control" name="key" required>
                                <div class="col-xs-12 help-block with-errors"></div>
                            </fieldset>

                            <div class="form-group">
                                <label for="lti-secret">Secret</legend>  
                                <input type="text" id="lti-secret" class="form-control" name="secret" required>
                                <div class="col-xs-12 help-block with-errors"></div>
                            </fieldset>

                            <div class="form-group">
                                <label for="lti-from">From</label>
                                <input type="date" id="lti-from" class="form-control" name="from_date">
                            </div>

                            <div class="form-group">
                                <label for="lti-to">To</label>
                                <input type="date" id="lti-to" class="form-control" name="to_date">
                            </div>

                            <fieldset class="light-gray">
                                <legend>Database Credential</legend>
                                
                                <div class="form-group col-xs-12">
                                    <label for="lti-type">DB type</label>
                                    <input id="lti-dbtype" class="form-control" name="db_type">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-host">Host</label>
                                    <input id="lti-host" class="form-control" name="host">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-port">Port</label>
                                    <input id="lti-port" class="form-control" name="port">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-name">DB Name</label>
                                    <input id="lti-db" class="form-control" name="db_name">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-un">User Name</label>
                                    <input id="lti-un" class="form-control" name="user">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-pw">Password</label>
                                    <input id="lti-pw" class="form-control" name="pw">
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="lti-prefix">Table Prefix</label>
                                    <input id="lti-prefix" class="form-control" name="prefix">
                                </div>
                            </fieldset>

                            <div class="form-buttons">
                                <button type="button" class="btn btn-link center-block" id="edit-lti-save-button">
                                    SAVE
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                </button>
                            </div>

                        </div><!-- col-xs-10 -->
                    </div><!-- row -->
                </form>
            </div><!-- modal-body -->

        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- modal -->