<!DOCTYPE html>
<html lang="en">

<head>
    @include('parts.head')
</head>

<body class="{{ $theme ?? 'light' }}">

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

<div class="modal fade" id="export-eye-tracking-data" tabindex="-1" role="dialog"
    aria-labelledby="export-eye-tracking-data" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
                <h4 class="modal-title" id="assigned-groups-modal-title">EXPORT EYE TRACKING DATA</h4>
            </div><!-- .modal-header -->

            <div class="modal-body">
                <div class="flex">
                    <div class="w-52">
                        <button id="select-duration" data-dropdown-toggle="select-duration-dropdown"
                            class="h-16 text-center flex justify-center w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-2xl px-5 py-2.5 text-center inline-flex items-center"
                            type="button">
                            <p>Duration &nbsp;</p> <i class="fa fa-caret-down"></i>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="select-duration-dropdown"
                            class="w-52 mt-3 z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="w-full py-2 text-2xl text-gray-700 dark:text-gray-200"
                                aria-labelledby="select-duration">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 30 minutes</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 1 hour</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 5 hours</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 1 day</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 1 week</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Past 30 days</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        All</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="flex justify-center w-56 h-16 px-5 py-2.5 items-center ml-5 border-solid border-zinc-500 border-2 bg-gray-200">
                        <p id="selected-duration" class="text-black font-bold text-2xl" data-duration="1d">Past 1 day
                        </p>
                    </div>
                </div>

                <div
                    class="mt-2 text-2xl flex items-center px-4 py-8 border border-gray-200 rounded dark:border-gray-700">
                    <input id="filter-by-group-video" type="number"
                        class="form-input px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    <label for="filter-by-group-video" class="w-full !m-0 font-bold text-black">
                        &nbsp; &nbsp; Filter by group video ID</label>
                </div>
            </div>

            <div class="modal-footer">
                <button id="export-eye-tracking-btn" type="button" data-dismiss="modal"
                    class="text-2xl px-5 py-2.5 font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <i class="fa fa-download"></i> &nbsp;
                    Export
                </button>
            </div>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .#modal-form -->

<script>
    (() => {
        const $toggleButton = $('#select-duration');
        const $dropdownMenu = $('#select-duration-dropdown');
        const $selectedDuration = $('#selected-duration');
        const $exportBtn = $('#export-eye-tracking-btn');
        const durationMapping = {
            'Past 30 minutes': '30m',
            'Past 1 hour': '1h',
            'Past 5 hours': '5h',
            'Past 1 day': '1d',
            'Past 1 week': '1w',
            'Past 30 days': '30d',
            'All': 'all'
        };

        $('#export-eye-tracking-data').on("show.bs.modal", function(e) {
            $exportBtn.data('id', $(e.relatedTarget).data('id'));
        });

        $toggleButton.on('click', function(e) {
            e.stopPropagation();
            $dropdownMenu.toggleClass('hidden');
        });

        $dropdownMenu.find('a').on('click', function(event) {
            event.preventDefault();
            var selectedText = $(this).text().trim();
            $selectedDuration.text(selectedText);
            $selectedDuration.data('duration', durationMapping[selectedText]);
            $dropdownMenu.addClass('hidden');
        });

        $exportBtn.on('click', function() {
            const duration = $selectedDuration.data('duration');
            const data = {
                duration: duration === 'all' ? undefined : duration,
            };

            const filter = $('#filter-by-group-video').val();
            if (filter !== null && filter !== undefined && filter !== '' && !isNaN(filter)) {
                data.gv_id = filter;
            }

            window.location.href = `/trackings/eye_tracking_query?${$.param(data)}`;
        });

        $(window).on('click', function(event) {
            if (!$toggleButton.is(event.target) && $toggleButton.has(event.target).length === 0 &&
                !$dropdownMenu.is(event.target) && $dropdownMenu.has(event.target).length === 0) {
                $dropdownMenu.addClass('hidden');
            }
        });
    })()
</script>

</html>
