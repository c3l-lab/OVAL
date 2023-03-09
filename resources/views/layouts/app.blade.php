<!DOCTYPE html>
<html lang="en">
	<head>
		@include('parts.head')
	</head>

	<body>
	
		@include('parts.sidebar')
		<div class="canvas">
			@include('parts.navbar')    
			@yield('selection_bar')    

        	@yield('content')
        	
        	@include('parts.footer')

			@include('parts.scripts')
	
			@yield('javascript')

			
		</div><!-- .canvas -->
		@yield('modal')
    </body>
</html>





