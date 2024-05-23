<form class="form-horizontal" id="controls-setting-form"
    action="{{ route('group_videos.controls.update', ['group_video' => $groupVideo]) }}">
    <div class="flex items-center">
        <h4 class="text-4xl font-bold text-gray-6">
            Config player controls
        </h4>

        <button class="bg-gray-500 text-white font-bold py-2 px-4 rounded text-5xl mx-3" type="submit">
            <i class="fa fa-save"></i>
        </button>

    </div>
    <div class="form-group">
        <label class="col-sm-4">Fullscreen</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="fullscreen" @checked($groupVideo->controls['fullscreen']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Captions</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="captions" @checked($groupVideo->controls['captions']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Playback Speed</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="speed" @checked($groupVideo->controls['speed']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-4">Play/Pause</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="play" @checked($groupVideo->controls['play']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Timeline</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="progress" @checked($groupVideo->controls['progress']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Volume</label>
        <div class="col-sm-8">
            <label class="switch">
                <input type="checkbox" name="volume" @checked($groupVideo->controls['volume']) />
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</form>
<h4 class="modal-title">Config Annotation</h4>
