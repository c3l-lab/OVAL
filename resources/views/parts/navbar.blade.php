		<div class="navbar">
			<ul class="nav navbar-nav col-xs-6">
				<li>
					<button type="button" id="menu-button" class="navbar-button" data-toggle="offcanvas" data-recalf="false" data-target=".navmenu" data-canvas=".canvas" title="Menu">
						<i class="fa fa-bars" aria-hidden="true"></i>
					</button>
				</li>
				@if (Auth::user())
				<li>
					<span class="greetings" userid="{{isset($user) ? $user->id() : ''}}" isinstructor = "{{isset($user) ? $user->isAnInstructor() : ''}}">Hi {{ Auth::user()->fullName() }}</span>
				</li>
				@endif
			</ul>

			<div class="oval-branding">
				<img src="{{asset('img/OVAL-IconSmallNoFill01.png')}}" height="30">
				<span class="oval-name hidden-xs">
					Online Video Annotation for Learning
				</span>
			</div>
		</div><!-- .navbar -->
