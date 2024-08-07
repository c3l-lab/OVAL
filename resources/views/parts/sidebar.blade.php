 <div class="navmenu navmenu-default navmenu-fixed-left vertical-scroll">
     <div class="theme-switch">
         <span class="glyphicon glyphicon-adjust" aria-hidden="true"></span>
         <label class="switch">
             <input type="checkbox" {{ $theme == 'dark' ? 'checked' : '' }}>
             <span class="slider round"></span>
         </label>
     </div>

     <div class="navmenu-title">MENU</div>
     <ul class="nav navmenu-nav side-bar-nav">
         <li><a href="/">Home</a></li>
         @if (isset($user) && $user->isAnInstructor())
             <li><a href="{{ route('group_videos.index') }}">Video Management</a></li>
         @endif
         @if (isset($user) && $user->role == 'A')
             <li><a href="/analysis_requests">Manage Content Analysis Requests</a></li>
             <li><a href="/register">Add an admin user</a></li>
             <li><a style="cursor: pointer" type="button" data-toggle="modal"
                     data-target="#export-eye-tracking-data">Export eye
                     tracking data</a></li>
         @endif
         @if (isset($user) && $user->isAnInstructor())
             <li><a href="/analytics">Analytics</a></li>
         @endif
         @if (isset($user) && $user->role == 'A')
             <li><a href="/transcripts">Batch Upload</a></li>
             <li><a href="{{ route('consumers.index') }}">Manage LTI 1.1 Connections</a></li>
             <li><a href="{{ route('registrations.index') }}">Manage LTI 1.3 Registrations</a></li>
         @endif
         <li class="space-top"><a href="/logout">Logout</a></li>
     </ul>
     <div class="nav-footer">
         <p>You are currently using the Beta version of OVAL; designed to allow teachers and students to experience Oval
             in the final stages of development. We appreciate feedback on your OVAL experience and suggestions for
             improvement. Please email your feedback using the following link.</p>
         <p>
             <a href="mailto:LearnOnline_feedback@unisa.edu.au?subject=Oval%20Feedback" target="_blank">
                 Email feedback on your OVAL experience
             </a>
         </p>
     </div><!-- .footer -->
 </div><!-- .navmenu -->
