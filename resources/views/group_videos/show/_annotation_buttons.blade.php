<div class="annotations-buttons">
    @if ($group_video->show_annotations)
        <a href="{{ route('annotations.download', ["group_video_id" => $group_video->id, "course_id" => $course->id]) }}" target="_blank">
            <button type="button" class="btn btn-link download-comments" title="Download annotations and comments">
                Download
                <i class="fa fa-download"></i>
            </button>
        </a>
    @endif
    <button type="button" class="btn btn-link add-annotation" title="Add an annotation">
        New Annotation
        <i class="fa fa-plus-circle"></i>
    </button>
</div><!-- .annotations-buttons -->
