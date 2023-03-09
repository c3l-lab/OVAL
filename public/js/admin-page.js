$('document').ready(function(){

    $("#delete-all").on("click", function() {
        if(!confirm("Are you sure to delete this request?")) {
            e.preventDefault();
        }
    });

    $(".delete-form").on("submit", function(e) {
        if(!confirm("Are you sure to delete this request?")) {
            e.preventDefault();
        }
    });

});//end document ready