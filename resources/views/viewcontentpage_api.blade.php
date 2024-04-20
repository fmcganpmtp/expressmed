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
    <link href="{{ asset('front_view/css/font-awesome.min.css') }}" type="text/css" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('front_view/js/fontawesome.js') }}"></script>
    <script src="{{ asset('front_view/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front_view/js/bootstrap.min.js') }}"></script>

    <title>{{ config('app.name', 'Laravel') }}</title>
</head>

<body>

    <div class="width-container main-product-detail-page">
        <div class="row">

            <div class="col-md-12 product-detail-page-outer">
                <div class="row">

                    @if ($contentpage->banner_type == 'slider' && !empty($sliders))
                        {{-- slider section starts --}}
                        <div class="col-lg-12 col-md-12 col-sm-12 main-slider-outer">
                            <div id="carousel-example-2" class="carousel slide carousel-fade" data-ride="carousel">
                                <ol class="carousel-indicators main-slider">
                                    @foreach($sliders as $key=>$images)
                                        <li data-target="#carousel-example-2" data-slide-to="{{ $key }}" class="{{($key == 0) ? 'active' : '' }}"></li>
                                    @endforeach

                                </ol>
                                <div class="carousel-inner" role="listbox">

                                    @foreach($sliders as $key=>$images)
                                        <div class="carousel-item {{($key == 0) ? 'active' : 'carousel-img-outer' }}">
                                            <div class="view">
                                                <img class="d-block w-100" src="{{ asset('/assets/uploads/sliders/'.$images->image) }}" alt="First slide">
                                                <div class="mask rgba-black-light"></div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <a class="carousel-control-prev main-curosel-prev" href="#carousel-example-2" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next  main-curosel-next" href="#carousel-example-2" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        {{-- slider section ends --}}
                    @elseif($contentpage->banner_type == 'banner' && $contentpage->banner != '')
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="banner-container">
                                <img src="{{ asset('/assets/uploads/contents/'.$contentpage->banner) }}" alt="banner-image" style="width: 100%;">
                                <h2 class="text-white">{{ $contentpage->title }}</h2>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12 product-detail-page-item">
                        {!! $contentpage->page_content !!}
                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 product-detail-right-slider-a">
                <div id="multi-item-example" class="carousel slide carousel-multi-item" data-ride="carousel">
                    <ol class="carousel-indicators product-slider">
                        <li data-target="#multi-item-example" data-slide-to="0" class="active"></li>
                        <li data-target="#multi-item-example" data-slide-to="1"></li>
                    </ol>
                    <div class="carousel-inner cosmetic-product-outer" role="listbox">
                        <div class="carousel-item active cosmetic-product-slider">
                            <div class="left-small-slider-outer">
                                <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                            </div>
                        </div>
                        <div class="carousel-item cosmetic-product">
                            <div class="left-small-slider-outer">
                                <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="left-deals-day-slider">
                    <div id="multi-item-example-3" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            <li data-target="#multi-item-example-3" data-slide-to="0" class="active"></li>
                            <li data-target="#multi-item-example-3" data-slide-to="1"></li>
                            <!--<li data-target="#multi-item-example" data-slide-to="2"></li>-->
                        </ol>
                        <div class="carousel-inner ayurvedic-product-slider" role="listbox">
                            <div class="carousel-item active ayurvedicc-product-slider">
                                <div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/left-banner-04.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div>
                            <div class="carousel-item ayurvedicc-product-slider">
                                <div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/left-banner-04.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
