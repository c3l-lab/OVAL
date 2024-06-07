<div class="modal fade" id="annotation-config-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
                <h4 class="modal-title">Config Annotation</h4>
            </div><!-- .modal-header -->
            <div class="modal-body container-fluid">
                <form class="form-horizontal" id="annotation-config-form">
                    <div class="form-group">
                        <label class="col-sm-4">Show annotations</label>
                        <div class="col-sm-8">
                            <label class="switch">
                                <input type="checkbox" name="show_annotations" class="toggle-annotation-visibility">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">Show download</label>
                        <div class="col-sm-8">
                            <label class="switch">
                                <input type="checkbox" name="downloadable" class="toggle-downloadable">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">Show annotation button</label>
                        <div class="col-sm-8">
                            <label class="switch">
                                <input type="checkbox" name="is_show_annotation_button"
                                    class="toggle-annotation-button">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">Enable structured annotation quiz</label>
                        <div class="col-sm-8">
                            <label class="switch">
                                <input type="checkbox" name="enable_structured_annotation_quiz"
                                    class="toggle-annotation-button">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">Label</label>
                        <div class="col-sm-8">
                            <input class="p-2 border-2 border-solid border-gray-600 form-label" type="text"
                                name="label">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">Header name</label>
                        <div class="col-sm-8">
                            <input class="p-2 border-2 border-solid border-gray-600 form-header-name" type="text"
                                name="header_name">
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button class="btn btn-link" type="submit">
                            <i class="fa fa-save"></i>
                        </button>
                    </div><!-- form-group -->
                </form>
            </div><!-- .modal-body -->
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .#modal-form -->
