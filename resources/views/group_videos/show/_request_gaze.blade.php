@if ($group_video->enable_eye_tracking)
    <div id="gaze-request"
        class="hidden !pointer-events-auto z-50 fixed w-96 bg-slate-300 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 m-6 p-4 rounded shadow-2xl">
        <p class="text-teal-900 bold text-2xl">Gaze Tracking. Cookie.</p>
        <p class="text-slate-500 bold">
            We would like to use your camera for eye-tracking. We would only store your gaze data. The actual camera
            image is not stored.
            A cookie will be set to save your permission preference.
            If you agree, you will be <span class="text-red-900">navigated</span> to a calibration process before
            watching the video.
        </p>
        <br>
        <div class="flex items-center justify-around">
            <button id="gaze-yes"
                class="w-40 align-middle font-sans font-bold text-center uppercase transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none text-lg py-3 px-6 bg-gray-900 text-white shadow-md shadow-gray-900/10 hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none rounded-full"
                type="button">
                That's fine
            </button>
            <button id="gaze-no"
                class="w-32 align-middle font-sans font-bold text-center uppercase transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none text-lg py-3 px-6 border border-gray-900 text-gray-900 hover:opacity-75 focus:ring focus:ring-gray-300 active:opacity-[0.85] rounded-full"
                type="button">
                No
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gazeConsent = document.cookie.match(new RegExp('(^| )allow_gaze_tracking=([^;]+)'));
            const enableEyeTracking = !!window.Oval.currentGroupVideo.enable_eye_tracking;
            if (!gazeConsent && enableEyeTracking) {
                document.getElementById('gaze-request').style.setProperty('display', 'block', 'important');
                document.body.classList.add('block-disable');
            }
        });

        document.getElementById('gaze-yes').addEventListener('click', function() {
            const today = new Date();
            const nextWeek = new Date(today)
            nextWeek.setDate(today.getDate() + 7);

            document.cookie =
                `allow_gaze_tracking=true; expires=${nextWeek.toUTCString()}; path=/group_videos`;
            window.location.href = '/group_videos/calibrate';
        });

        document.getElementById('gaze-no').addEventListener('click', function() {
            const today = new Date();
            const nextWeek = new Date(today)
            nextWeek.setDate(today.getDate() + 7);

            document.cookie =
                `allow_gaze_tracking=false; expires=${nextWeek.toUTCString()}; path=/group_videos`;

            document.getElementById('gaze-request').style.display = 'none';
            document.body.classList.remove('block-disable');
        });
    </script>
@endif
