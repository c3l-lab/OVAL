var modal_lti_id;

/**
 * @description
 * Function to create random string in length and with char set(optional) passed in.
 * reference https://stackoverflow.com/a/1349462/1142137
 *
 * @param {Number} len length of resulting random string
 * @param {String} charSet letters to be included in random string
 */
function randomString(len, charSet) {
    charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#$%^&*()_-+=/?.>,<';
    var randomString = '';
    for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * charSet.length);
        randomString += charSet.substring(randomPoz,randomPoz+1);
    }
    return randomString;
}


$('document').ready(function(){

    $("#generate-key").on("click", function() {
        $("#lti-connection-key").val(randomString(20));
    });

    $("#generate-secret").on("click", function() {
        $("#lti-connection-secret").val(randomString(20));
    });

    $("#edit-lti-modal").on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        modal_lti_id = button.data('id');

        $.ajax({
            type: "GET",
            url: "/lti/consumers/" + modal_lti_id,
            success: function(data) {
                $("#lti-name").val(data.name);
                $("#lti-key").val(data.key);
                $("#lti-secret").val(data.secret);
                $("#lti-from").val(data.from);
                $("#lti-to").val(data.to);
            },
            error: function (request, status, error) {
                console.log("error /get_lti_connection_detail - "+error);
            }
        });
    })

    $("#edit-lti-save-button").on("click", function() {
        $.ajax({
            type: "PUT",
            url: "/lti/consumers/" + modal_lti_id,
            data: {
                id: modal_lti_id,
                name: $("#lti-name").val(),
                key: $("#lti-key").val(),
                secret: $("#lti-secret").val(),
                from: $("#lti-from").val(),
                to: $("#lti-to").val(),
            },
            success: function (data) {
                window.location.reload();
            },
            error: function(request, status, error) {
                console.log("error /edit_lti_connection - "+error);
            }
        });
    });
});//end document.ready
