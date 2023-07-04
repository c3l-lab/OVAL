	<div class="navmenu navmenu-default navmenu-fixed-left vertical-scroll">
  		<div class="navmenu-title">MENU</div>
  		<ul class="nav navmenu-nav side-bar-nav">
			<li><a href="/">Home</a></li>
  			@if(isset($user) && $user->isAnInstructor())
  			<li><a href="/video-management">Video Management</a></li>
			@endif
			@if(isset($user) && $user->role=="A")
			<li><a href="/manage-analysis-requests">Manage Content Analysis Requests</a></li>
			<li><a href="/register">Add an admin user</a></li>
			@endif
			@if(isset($user) && $user->isAnInstructor())
			<li><a href="/analytics">Analytics</a></li>
			@endif
			@if(isset($user) && $user->role=="A")
			<li><a href="/batch-upload">Batch Upload</a></li>
			<li><a href="/manage-lti-connections">Manage LTI 1.1 Connections</a></li>
			<li><a href="/lti/registrations">Manage LTI 1.3 Registrations</a></li>
			@endif
  			<li class="space-top"><a href="/logout">Logout</a></li>
  		</ul>
  		<div class="nav-footer">
			<p>You are currently using the Beta version of OVAL; designed to allow teachers and students to experience Oval in the final stages of development. We appreciate feedback on your OVAL experience and suggestions for improvement. Please email your feedback using the following link.</p>
			<p>
				<a href="mailto:LearnOnline_feedback@unisa.edu.au?subject=Oval%20Feedback" target="_blank">
				Email feedback on your OVAL experience
				</a>
			</p>
  		</div><!-- .footer -->
  	</div><!-- .navmenu -->
