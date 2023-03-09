$('document').ready(function() {

	$('#upload-json-form').validator({
		custom: {
			filetype: function($el) {
				var acceptable = $el.data('filetype').split(',');
				var filename = $('#upload-json-form').find("#batch-data-file").val();
				var extension = filename.replace(/^.*\./, '');
				if (extension == filename) {
					extension = '';
				} 
				else {
					extension = extension.toLowerCase();
				}
				if ($.inArray(extension, acceptable) == -1) {
					return "Invalid file type. Please select .json file";
				}
			}
		}
	});


});//end document.ready