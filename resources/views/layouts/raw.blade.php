<!DOCTYPE html>
<html lang="en">

<head>
    @include('parts.head')
</head>

<body class="light">

    <div class="canvas">

        @yield('content')

        @include('parts.scripts')

        @yield('javascript')

    </div><!-- .canvas -->
    @yield('modal')
</body>

</html>
