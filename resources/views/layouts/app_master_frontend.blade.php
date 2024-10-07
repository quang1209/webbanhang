<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <title>{{ $title_page ?? "Đồ Án"   }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('ico.png') }}" />
        @yield('css')

        {{-- Thông báo --}}
        @if(session('toastr'))
            <script>
                var TYPE_MESSAGE = "{{session('toastr.type')}}";
                var MESSAGE = "{{session('toastr.message')}}";
            </script>
        @endif
        <style>
            .main-title {
                color: red;
            }
            .product-item a h3 {
                font-size: 14px !important;
            }
        </style>
    </head>
    <body>
        @include('frontend.components.header')
        @yield('content')
        @include('frontend.components.footer')
        <script>
            var DEVICE = '{{  device_agent() }}'
        </script>
        @yield('script')
    </body>
</html>
