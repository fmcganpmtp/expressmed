<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="keywords" content="@yield('meta_keywords','some default keywords')">
    <meta name="description" content="@yield('meta_description','default description')">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <link href="{{ asset('front_view/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('front_view/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('front_view/css/font-awesome.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link href="{{ asset('front_view/css/jquery-ui.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('front_view/css/jquery.exzoom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('front_view/css/animate.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">


    <!-- Scripts -->
    <script src="{{ asset('front_view/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front_view/js/fontawesome.js') }}"></script>
    <script src="{{ asset('front_view/js/popper.min.js') }}"></script>
    <script src="{{ asset('front_view/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('front_view/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('/front_view/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('/front_view/js/jquery.exzoom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>



    {{-- <script src="{{ asset('front_view/js/blazy.min.js') }}"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <title>{{ config('app.name', 'Laravel') }}</title>
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-V38VHB21NF"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-V38VHB21NF');
</script>
</head>

<body>
    <header>
        @include('layouts.frontview.topbar_frontview')
    </header>
    <main>
        @yield('content')
        @include('layouts.frontview.footer_frontview')
    </main>
    {{-- <script src="https://services.billdesk.com/checkout-widget/src/app.bundle.js"></script> --}}
    {{-- <script type="module" src="https://uat.billdesk.com/jssdk/v1/dist/billdesksdk/billdesksdk.esm.js"></script>
    <script nomodule="" src="https://uat.billdesk.com/jssdk/v1/dist/billdesksdk.js"></script>
    <link href="https://uat.billdesk.com/jssdk/v1/dist/billdesksdk/billdesksdk.css" rel="stylesheet"> --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script type="module" src="https://pay.billdesk.com/jssdk/v1/dist/billdesksdk/billdesksdk.esm.js"></script>
    <script nomodule="" src="https://pay.billdesk.com/jssdk/v1/dist/billdesksdk.js"></script>
    <link href="https://pay.billdesk.com/jssdk/v1/dist/billdesksdk/billdesksdk.css" rel="stylesheet">

    @yield ('footer_scripts')
    @yield ('login_scripts')

    {{-- <script src="{{ asset('front_view/js/all.js') }}"></script> --}}
    <script src="{{ asset('front_view/js/wow.min.js') }}"></script>
    <script>
        new WOW().init();
    </script>
</body>

</html>
