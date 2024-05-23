<form class="form-horizontal" id="annotation-config-modal"
    action="{{ route('group_videos.annotation_config.update', ['group_video' => $groupVideo]) }}">
    <div class="form-group">
        <label class="col-sm-4">Show annotations</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="show_annotations" @checked($groupVideo->show_annotations) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Show download</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="downloadable" @checked($groupVideo->annotation_config['downloadable']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Show annotation button</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="is_show_annotation_button" @checked($groupVideo->annotation_config['is_show_annotation_button']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Label</label>
        <div class="col-sm-8">
            <input class="p-2 border-2 border-solid border-gray-600" type="text" name="label"
                value="{{ $groupVideo->annotation_config['label'] }}" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Header name</label>
        <div class="col-sm-8">
            <input class="p-2 border-2 border-solid border-gray-600" type="text" name="header_name"
                value="{{ $groupVideo->annotation_config['header_name'] }}" />
        </div>
    </div>

    <div class="form-buttons">
        <button class="btn btn-link" type="submit">
            <i class="fa fa-save"></i>
        </button>
    </div><!-- form-group -->

</form>
