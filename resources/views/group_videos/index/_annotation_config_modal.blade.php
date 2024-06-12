<div class="modal fade" id="annotation-config-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog relative" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
                <h4 class="modal-title">Config Annotation</h4>
            </div><!-- .modal-header -->
            <div class="modal-body container-fluid">
                <form class="form-horizontal" id="annotation-config-form">
                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Show annotations</label>
                        <div class="col-sm-6">
                            <label class="switch">
                                <input type="checkbox" name="show_annotations" class="toggle-annotation-visibility">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Show download</label>
                        <div class="col-sm-6">
                            <label class="switch">
                                <input type="checkbox" name="downloadable" class="toggle-downloadable">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Show annotation button</label>
                        <div class="col-sm-6">
                            <label class="switch">
                                <input type="checkbox" name="is_show_annotation_button"
                                    class="toggle-annotation-button">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Set structured annotations</label>
                        <div class="col-sm-6">
                            <button id="set-annotation-btn" type="button" class="btn btn-link pl-0">
                                <i class="fa fa-pencil-square-o group-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Enable structured annotation quiz</label>
                        <div class="col-sm-6">
                            <label class="switch">
                                <input type="checkbox" name="enable_structured_annotation_quiz"
                                    class="toggle-annotation-button">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Label</label>
                        <div class="col-sm-6">
                            <input class="p-2 border-2 border-solid border-gray-600 form-label" type="text"
                                name="label">
                        </div>
                    </div>

                    <div class="form-group flex items-center">
                        <label class="col-sm-6">Header name</label>
                        <div class="col-sm-6">
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
        <div class="modal-body relative" id="create-structure-annotation-form">
            <button class="absolute p-3 z-10 right-0 top-0" id="close-set-annotation-btn">
                <i class="fa fa-close text-4xl" aria-hidden="true"></i>
            </button>
            <div id="change_warning"
                class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 absolute top-0 left-0 w-full"
                role="alert">
                <span class="font-bold">Warning:</span> Changes are not saved.
            </div>
            <form class="modal-body !mt-14">
            </form>
            <div id="add-question" class="btn-group flex justify-around items-center my-8 space-x-4">
                <button id="add-multiple-choice-question"
                    class="flex-2 bg-blue-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-blue-600">
                    <i class="fa fa-plus-circle mr-2"></i> Multiple Choice
                </button>
                <button id="add-short-question"
                    class="flex-2 bg-green-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-green-600">
                    <i class="fa fa-plus-circle mr-2"></i> Short Question
                </button>
                <button id="submit-question"
                    class="flex-1 bg-orange-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-orange-600">
                    <i class="fa fa-save mr-2"></i> Submit
                </button>
                <button id="remove-question"
                    class="flex-1 bg-red-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-red-600">
                    <i class="fa fa-minus-circle mr-2"></i> Remove
                </button>
            </div>
            <div id="add-multiple-choice-question-sub" class="btn-group hidden w-full">
                <div class="w-full mt-3 pt-3 border-t-2">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Title</label>
                        <input type="text"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Choices</label>
                        <div id="option-input" class="flex items-center mb-2 hidden">
                            <input type="radio" name="options" class="!mr-6" disabled>
                            <input type="text"
                                class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Option">
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <button id="add-option-sub"
                        class="flex-2 !ml-0  bg-blue-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-blue-600">
                        <i class="fa fa-plus-circle mr-2"></i> Add Option
                    </button>
                    <button id="remove-option-sub" disabled
                        class="flex-2 bg-gray-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-gray-600">
                        <i class="fa fa-minus-circle mr-2"></i> Remove Option
                    </button>
                    <button id="confirm-multiple-choice-question-sub"
                        class="flex-1 bg-green-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-green-600">
                        <i class="fa fa-save mr-2"></i> Confirm
                    </button>
                    <button
                        class="cancel-btn flex-1 bg-red-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center enabled:hover:bg-red-600">
                        <i class="fa fa-minus-circle mr-2"></i> Cancel
                    </button>
                </div>
            </div>
            <div id="add-short-question-sub" class="btn-group w-full hidden">
                <div class="w-full">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Title</label>
                        <input type="text"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex space-x-4">
                    <button id="confirm-short-question-sub"
                        class="flex-1 !ml-0 bg-green-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-green-600">
                        <i class="fa fa-save mr-2"></i> Confirm
                    </button>
                    <button
                        class="cancel-btn flex-1 bg-red-500 text-white font-bold py-2 px-4 rounded flex items-center justify-center disabled:opacity-50 enabled:hover:bg-red-600">
                        <i class="fa fa-minus-circle mr-2"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div><!-- .modal-dialog -->
</div><!-- .#modal-form -->
