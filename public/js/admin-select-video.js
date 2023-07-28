$('document').ready(function(){
    if (current_group_video_id) {
        $('#select-radio-'+current_group_video_id).prop('checked', true);
    }

    $('input[name=select-radio]').on('change', function() {
        if (confirm("Would you like to select this video?")) {
            var group_video_id = $(this).val();
            $.ajax({
               type: "POST",
               url: "/set_lti_resource_link",
               data:{link_id:link_id , group_video_id:group_video_id},
               success: function(data) {
                   window.location.href = "/group_videos/" + group_video_id;
               },
               error: function (req, status, error) {
                   console.log("error /set_lti_resource_link - "+error);//////
               }
            });
        }
    });



});// end document ready
