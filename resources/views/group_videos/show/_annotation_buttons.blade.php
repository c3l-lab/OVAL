<div class="annotations-buttons">
    @if (data_get($group_video->annotation_config, 'downloadable', true))
        <a href="{{ route('annotations.download', ['group_video_id' => $group_video->id, 'course_id' => $course->id]) }}"
            target="_blank">
            <button type="button" class="btn btn-link download-comments" title="Download annotations and comments">
                Download
                <i class="fa fa-download"></i>
            </button>
        </a>
    @endif
    @if (data_get($group_video->annotation_config, 'is_show_annotation_button', true))
        <button type="button" class="btn btn-link add-annotation" title="Add an annotation">
            {{ data_get($group_video->annotation_config, 'label', 'New Annotation') }}
            <i class="fa fa-plus-circle"></i>
        </button>
    @endif
</div><!-- .annotations-buttons -->
