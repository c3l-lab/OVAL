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

    $(".delete-lti-button").on("click", function(e) {
        if (confirm("Are you sure you want to delete? This will delete all data (including annotations, quiz results etc) related to this LTI connection.")){
            $.ajax({
                type: "POST",
                url: "/delete_lti_connection",
                data: {id: $(this).data("id")},
                success: function(data) {
                    location.reload();
                },
                error: function(request, status, error) {
                    console.log("error /delete_lti_connection - "+error);
                }
            });
        }
    });

    $("#edit-lti-modal").on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        modal_lti_id = button.data('id');

        $.ajax({
            type: "POST",
            url: "/get_lti_connection_detail",
            data: {id:modal_lti_id},
            success: function(data) {
                $("#lti-name").val(data.name);
                $("#lti-key").val(data.key);
                $("#lti-secret").val(data.secret);
                $("#lti-from").val(data.from);
                $("#lti-to").val(data.to);
                $("#lti-dbtype").val(data.db_type);
                $("#lti-host").val(data.host);
                $("#lti-port").val(data.port);
                $("#lti-db").val(data.database);
                $("#lti-un").val(data.username);
                $("#lti-pw").val(data.password);
                $("#lti-prefix").val(data.prefix);
            },
            error: function (request, status, error) {
                console.log("error /get_lti_connection_detail - "+error); 
            }
        });
    })

    $("#edit-lti-save-button").on("click", function() {
        $.ajax({
            type: "POST",
            url: "/edit_lti_connection",
            data: {
                id: modal_lti_id,
                name: $("#lti-name").val(),
                key: $("#lti-key").val(),
                secret: $("#lti-secret").val(),
                from: $("#lti-from").val(),
                to: $("#lti-to").val(),
                dbtype: $("#lti-dbtype").val(),
                host: $("#lti-host").val(),
                port: $("#lti-port").val(),
                db: $("#lti-db").val(),
                un: $("#lti-un").val(),
                pw: $("#lti-pw").val(),
                prefix: $("#lti-prefix").val()
            },
            error: function(request, status, error) {
                console.log("error /edit_lti_connection - "+error);
            }
        });
    });
});//end document.ready