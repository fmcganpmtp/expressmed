@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls active">{{ $contentpage->page }}</li>
            </ol>
        </nav>
    </div>

    <div class="width-container main-product-detail-page content-page-outer">
        <div class="row">
            {{-- <div class="col-lg-12 col-md-12 col-sm-12 product-detail-page-outer">
                <div class="row"> --}}

            @if ($contentpage->banner_type == 'slider' && !empty($sliders))
                {{-- slider section starts --}}
                <div class="col-lg-12 col-md-12 col-sm-12 main-slider-outer content-page1">
                    <div id="carousel-example-2" class="carousel slide carousel-fade" data-ride="carousel">
                        <ol class="carousel-indicators main-slider">
                            @foreach ($sliders as $key => $images)
                                <li data-target="#carousel-example-2" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                            @endforeach

                            {{-- <li data-target="#carousel-example-2" data-slide-to="1"></li>
                                    <li data-target="#carousel-example-2" data-slide-to="2"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">

                            @foreach ($sliders as $key => $images)
                                <div class="carousel-item {{ $key == 0 ? 'active' : 'carousel-img-outer' }}">
                                    <div class="view">
                                        <img class="d-block w-100" src="{{ asset('/assets/uploads/sliders/' . $images->image) }}" alt="First slide">
                                        <div class="mask rgba-black-light"></div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- <div class="carousel-item carousel-img-outer">
                                        <div class="view">
                                            <img class="d-block w-100" src="{{ asset('front_view/images/slider-01.png') }}" alt="Second slide">
                                            <div class="mask rgba-black-strong"></div>
                                        </div>
                                    </div>
                                    <div class="carousel-item carousel-img-outer">
                                        <div class="view">
                                            <img class="d-block w-100" src="{{ asset('front_view/images/slider-01.png') }}" alt="Second slide">
                                            <div class="mask rgba-black-strong"></div>
                                        </div>
                                    </div> --}}
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
                <div class="col-lg-12 col-md-12 col-sm-12 content-page2">

                    <div class="banner-container">
                        <img src="{{ asset('/assets/uploads/contents/' . $contentpage->banner) }}" alt="banner-image" style="width: 100%;">
                        <h2 class="text-white">{{ $contentpage->title }}</h2>
                    </div>
                </div>
            @endif

            <div class="col-md-12 product-detail-page-item content-page3">
                {!! $contentpage->page_content !!}
            </div>
            {{-- </div>
            </div> --}}

            {{-- <div class="col-lg-3 col-md-3 col-sm-12 product-detail-right-slider-a">
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
            </div> --}}

        </div>
        @if ($contentpage->seo_url == 'about-us')


             

           

            <section class="main-testimonial">
                <div class="width-container">
                    <div class="gtco-testimonials">
                        <h2>Customer Review</h2>
                        <div class="owl-carousel owl-carousel1 owl-theme">
                            @if ($testimonials->isNotEmpty())
                                @foreach ($testimonials as $testimonials_data)
                                    <div>
                                        <div class="card text-center">

                                            @if ($testimonials_data->profile_pic != '')
                                                <img class="card-img-top" src="{{ asset('/assets/uploads/testimonials/' . $testimonials_data->profile_pic) }}" alt="">
                                            @endif
                                            <div class="card-body">
                                                <h5>{{ $testimonials_data->name }}</h5>
                                                <p class="card-text">{{ $testimonials_data->comments }}</p>
                                                @if ($testimonials_data->title != '')
                                                    <h6>{{ $testimonials_data->title }}</h6>
                                                @endif
                                                @if ($testimonials_data->company_name != '')
                                                    <h6>{{ $testimonials_data->company_name }}</h6>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>

@endsection
