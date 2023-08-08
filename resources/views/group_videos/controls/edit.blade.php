<form
    class="form-horizontal"
    id="controls-setting-form"
    action="{{ route('group_videos.controls.update', ['group_video' => $groupVideo]) }}"
>
    <div class="form-group">
        <label class="col-sm-4">Fullscreen</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="fullscreen" @checked($groupVideo->controls["fullscreen"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Captions</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="captions" @checked($groupVideo->controls["captions"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Playback Speed</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="speed" @checked($groupVideo->controls["speed"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-4">Pausing</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="pause" @checked($groupVideo->controls["pause"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Timeline</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="progress" @checked($groupVideo->controls["progress"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Quanlity</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="quality" @checked($groupVideo->controls["quality"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Volume</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="volume" @checked($groupVideo->controls["volume"]) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-buttons">
        <button class="btn btn-link" type="submit">
            <i class="fa fa-save"></i>
        </button>
    </div><!-- form-group -->

</form>
