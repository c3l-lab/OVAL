<!-- jQuery -->
<script src="https://code.jquery.com/jquery.js"></script>
<!-- NiceScroll -->
<script type="text/javascript" src="{{ URL::secureAsset('js/jquery.nicescroll.min.js') }}"></script>
<!-- Bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- Jasny Bootstrap -->
<script type="text/javascript" src="{{ URL::secureAsset('js/jasny-bootstrap.min.js') }}"></script>

@yield('additional-js')

<script type="text/javascript">

	$(document).ready(function () {
		$("#logout").on("click", function() {
			$.ajax({
				type: "get",
				url: "{{ secure_url("logout") }}",
				success: function(data) {
					location.reload();
				},
				async: false
			});
		});
	});
</script>

@if (!empty($user))<script type="text/javascript">
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
 			   data: {api_token:"{{$user->api_token}}"},
	   beforeSend: function(request){
	   	request.setRequestHeader("Authorization", "Bearer {{$user->api_token}}");
	   },
	});
</script>
@endif

<script type="text/javascript" src="{{ URL::secureAsset('js/theme.js') }}"></script>
