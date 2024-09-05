<!DOCTYPE html>
<html lang="en">

@include('template.head')

<body>
    <script src="{{ asset('template/assets/static/js/initTheme.js') }}"></script>

    <div id="app">
        @include('template.sidebar')

        <div id="main" style="position: relative">
            @include('template.header')

            @yield('content')

            @include('template.footer')
        </div>
    </div>

    @include('template.js')

</body>

</html>
