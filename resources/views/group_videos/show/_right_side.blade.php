<div id="right-side" class="col-md-4">
    <ul class="nav nav-tabs content-tabs" role="tablist">
        @if ($group_video->show_analysis == true && count($video->keywords) > 0)
            <li role="presentation" class="active col-xs-6"><a href="#comments" aria-controls="comments"
                    role="tab" data-toggle="tab">GENERAL COMMENTS</a></li>
            <li role="presentation" class="col-xs-6"><a href="#related-videos" aria-controls="related-videos"
                    role="tab" data-toggle="tab">RECOMMENDED RESOURCES</a></li>
        @else
            <li role="presentation" class="active"><a href="#comments" aria-controls="comments" role="tab"
                    data-toggle="tab">GENERAL COMMENTS</a></li>
        @endif
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="comments">
            <div class="header">
                <button type="button" class="btn btn-link add-comment" title="Add a comment">
                    New Comment
                    <i class="fa fa-plus-circle"></i>
                </button><!-- .add-comment -->
            </div><!-- .header -->
            <div class="comments-box vertical-scroll">
            </div><!-- .comments-box -->
        </div><!-- #comments -->

        @if ($group_video->show_analysis == true && count($video->keywords) > 0)
            <div role="tabpanel" class="tab-pane" id="related-videos">
                <div class="keyword-ul-box vertical-scroll" id="related-links">
                    <form id="topic-search-form">
                        <div class="input-group">
                            <input type="search" id="topic-search-textbox" name="topic-search-textbox"
                                class="form-control dropdown-button" placeholder="Search for topic...">
                            <span class="input-group-btn">
                                <button id="topic-search-button" class="btn btn-default dropdown-button"
                                    type="button">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </span>
                        </div><!-- /input-group -->
                        <!-- <input class="form-control gray-textbox" type="text" placeholder="Search topics..."> -->
                    </form>
                    <ul id="related-ul">
                    </ul>
                    <div id="no-links-msg">
                    </div>
                </div><!-- keyword-ul-box -->
            </div><!-- #related-videos -->
        @endif
    </div><!-- tab-content -->
</div><!-- #right-side -->

