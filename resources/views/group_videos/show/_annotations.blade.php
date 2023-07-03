<ul class="nav nav-tabs content-tabs" role="tablist">
    <li role="presentation" class="active col-xs-6"><a href="#annotations" aria-controls="annotations" role="tab"
            data-toggle="tab">ANNOTATIONS</a></li>
    @if ($group_video->show_analysis == true && count($video->keywords) > 0)
        <li role="presentation" class="col-xs-6"><a href="#content-analysis" aria-controls="current topics"
                role="tab" data-toggle="tab">CURRENT TOPICS</a></li>
    @endif
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="annotations">
        @include('group_videos._annotation_buttons')

        <div class="">
            <canvas id="trends"></canvas>
            <div id="annotations-list"></div>
        </div><!-- .horizontal-scroll -->

        <div id="annotation-filter" data-toggle="buttons">
            <label class="btn active" title="Show all Annotations">
                <input type="radio" name="filter" value="4" checked><i class="fa fa-eye"></i>All
            </label>
            <label class="btn" title="Show only Annotations by me">
                <input type="radio" name="filter" value="1"><i class="fa fa-dot-circle-o"></i>Mine
            </label>
            <label class="btn" title="Show only Annotations by Student">
                <input type="radio" name="filter" value="3"><i class="fa fa-circle-o"></i>Students
            </label>
            <label class="btn" title="Show only Annotations by Instructors">
                <input type="radio" name="filter" value="2"><i class="fa fa-circle"></i>Instructors
            </label>
        </div><!-- #annotation-filter -->
    </div><!-- #annotations -->

    @if ($group_video->show_analysis == true && count($video->keywords) > 0)
        <div role="tabpanel" class="tab-pane" id="content-analysis">
            <h4>Current topic</h4>
            <div class="panel panel-default">
                <div class="panel-body" id="current-keywords">
                    &nbsp;
                </div><!-- panel-body -->
            </div><!-- panel -->
            <div class="content-analysis-body">
                <h4>List of topics covered in this video</h4>
                <div class="keyword-ul-box vertical-scroll" id="keyword-list">
                    <ul id="keyword-ul">
                    </ul>
                    <div class="no-keyword-msg"></div>
                </div><!-- keyword-ul-box -->
            </div><!-- content-analysis-body -->
        </div><!-- #content-analysis -->
    @endif
</div><!-- tab-content -->
