<div class="annotations-buttons">
    @if ($group_video->annotation_config['downloadable'])
        <a href="{{ route('annotations.download', ['group_video_id' => $group_video->id, 'course_id' => $course->id]) }}"
            target="_blank">
            <button type="button" class="btn btn-link download-comments" title="Download annotations and comments">
                Download
                <i class="fa fa-download"></i>
            </button>
        </a>
    @endif
    @if ($group_video->annotation_config['is_show_annotation_button'])
        <button type="button" class="btn btn-link add-annotation" title="Add an annotation">
            {{ $group_video->annotation_config['label'] }}
            <i class="fa fa-plus-circle"></i>
        </button>
    @endif
</div><!-- .annotations-buttons -->
