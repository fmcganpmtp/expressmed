@extends('layouts.frontview.app')

@section('content')

    @include('layouts.frontview.topmenubar_frontview')
    <div class=" main-slider-outer" id="mainBody_slider">
        {{-- Page Main Slider start --}}
        <div id="carousel-example-2" class="carousel slide carousel-fade carousel-slider" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            </ol>
            {{-- <ol class="carousel-indicators main-slider">
                    <li data-target="#carousel-example-2" data-slide-to="0" class="active"></li>
                </ol> --}}
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active">
                    <div class="view">

                        <div class="mask rgba-black-light"></div>
                    </div>
                    {{-- <div class="carousel-caption top-main-courosel">
                        <h3 class="h3-responsive">Banner Title</h3>
                        <div class="shop-now"><a href="#">Shop Now <i class="fas fa-shopping-cart"></i></a></div>
                        <div class="learn-more"><a href="#">Learn More <i class="fas fa-chevron-right"></i></a></div>
                    </div> --}}
                </div>
            </div>

            <a class="carousel-control-prev" href="#carousel-example-2" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carousel-example-2" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>

        </div>
        {{-- Page Main Slider End --}}
    </div>


    <section class="pr-owl-slider">
        <div class="width-container item-slider-outer">
            <div class="container-fluid">
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>New Arrivals</h2>
                    </div>
                    <div class="owl-slider">
                        <div id="carousel-pr" class="owl-carousel">

                            @foreach ($new_arrivals->chunk(6) as $products_set)
                                @if ($new_arrivals->isNotEmpty())
                                    @foreach ($new_arrivals as $products)
                                        @php

                                        @endphp
                                        <div class="item deals-day-product">
                                            <div class="product-listing">
                                                <a href="{{ route('shopping.productdetail', $products->product_url) }}">
                                                    @if ($products->product_image != '')
                                                        <img src="{{ asset('assets/uploads/products/') . '/' . $products->product_image }}" class="img-fluid" alt="" alt="">
                                                    @else
                                                        <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid" alt="">
                                                    @endif
                                                </a>
                                            </div>
                                            @php $key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp

                                            <div class="item-head"><a href="{{ route('shopping.productdetail', $products->product_url) }}">{{ $products->product_name }}</a></div>
                                            <div class="item-price"></i> {!! $common_settings[$key]['value'] !!} {{ $products->offer_price != 0 ? number_format($products->offer_price, 2) : number_format($products->price, 2) }}
                                                @if ($products->offer_price != 0)
                                                    <div class="old-price">{{ number_format($products->price, 2) }}</div>
                                                @endif
                                            </div>
                                            @if ($products->offer_price != 0)
                                                @php $percent = number_format((($products->price-$products->offer_price)*100) /$products->price) ;@endphp

                                                @if ($percent > 0)
                                                    <div class="discount-percent-list"><span>{{ $percent . '% Off' }}</span></div>
                                                @endif
                                            @endif

                                            <div class="product-incre-decre-outer sec_productadd">
                                                <div class="product-incre-decre">
                                                    <div class="input-group">
                                                        <input type="text" hidden class="form-control input-number" value="1" min="1" max="100" name="quant[1]">
                                                    </div>
                                                </div>
                                                <div class="add-buy-cart add-to-cart-list">

                                                    <a href="javascript:void(0)" class="btn {{ $products->not_for_sale == '1' || $products->flag == '1' ? "disable" : "add-cart-list add-cart-list_$products->id" }}" value="{{ $products->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                </div>
                                            </div>
                                            @if ($products->not_for_sale == '1')
                                                <div class="not-sale">Not for online sale !</div>
                                            @elseif($products->flag == '1')
                                                <div class="not-sale">Sold Out !</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="width-container item-slider-outer">
        <div class="row item-slider-row wow fadeInUp" data-wow-delay=".20s">
            <div class="col-md-3 item-slider">
                {{-- Sidebar Top 1 Slider Start --}}
                <div id="sidebar_sliderTop1" class="sidebar_sliderTop">
                    <div id="multi-item-example" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            {{-- <li data-target="#multi-item-example" data-slide-to="0" class="active"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            {{-- <div class="carousel-item active">
                            a<div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-3 item-slider">
                {{-- Sidebar Top 1 Slider Start --}}
                <div id="sidebar_sliderTop2" class="sidebar_sliderTop">
                    <div id="multi-item-example2" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            {{-- <li data-target="#multi-item-example" data-slide-to="0" class="active"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            {{-- <div class="carousel-item active">
                            a<div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

            </div>



            {{-- <div class="col-md-3 item-slider">
                <div class="news-letter">
                    <h6>News Letter Subscription</h6>
                    <p>Get all the latest information on Events, Sales and Offers.</p>
                    <div class="news-search flex-grow-1">
                        <input id="newsletter_email" class="news-letter-search" name="search" type="text" placeholder="Enter e-mail" />
                    </div>
                    <div id="subscribe_button_outer" class="news-letter-subscribe">
                        <button type="button" id="newsleter_submit" class="btn">Subscribe</button>
                    </div>
                    <p id="newsletter_alert" style="display: none"></p>
                </div>
            </div> --}}
            {{-- <div class="col-md-3 item-slider"> --}}
            {{-- Sidebar Top 2 Plain Start --}}
            {{-- <div class="left-clean-slider" id="sidebar_plainTop2">
                    <a href="#"><img src="{{ asset('front_view/images/left-banner-02.png') }}" class="img-fluid mx-auto d-block"></a>
                </div> --}}
            {{-- Sidebar Top 2 Plain End --}}
            {{-- </div> --}}

            @if ($testimonials->isNotEmpty())
                {{-- <div class="col-md-3 item-slider">
                    <div class="main-testim-slider">
                        <h2>Testimonials</h2>
                        <div id="carouselTestimonial" class="carousel carousel-testimonial slide" data-ride="carousel">
                            <ol class="carousel-indicators testim-slider-icon">
                                @for ($i = 0; $i < count($testimonials); $i++)
                                    <li data-target="#carouselTestimonial" data-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></li>
                                @endfor
                            </ol>
                            <div class="carousel-inner testim-slider-content">
                                @foreach ($testimonials as $key => $value)
                                    <div class="carousel-item testim-slider {{ $key === 0 ? 'active' : '' }}">
                                        <p>{{ Str::limit($value->comments, 150, '....') }}</p>
                                        <div class="carousel-testimonial-img p-1 rounded-circle m-auto">
                                            <img class="rounded-circle mx-auto d-block" src="{{ asset('assets/uploads/testimonials') . '/' . $value->profile_pic }}" alt="{{ $value->name }}_photo">
                                        </div>
                                        <h5>{{ $value->name }}</h5>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
            @endif
            <div class="col-md-3 item-slider">
                {{-- Sidebar Top 1 Slider Start --}}
                <div id="sidebar_sliderTop3" class="sidebar_sliderTop">
                    <div id="multi-item-example3" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            {{-- <li data-target="#multi-item-example" data-slide-to="0" class="active"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            {{-- <div class="carousel-item active">
                            a<div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-3 item-slider">
                {{-- Sidebar Top 1 Slider Start --}}
                <div id="sidebar_sliderTop4" class="sidebar_sliderTop">
                    <div id="multi-item-example4" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            {{-- <li data-target="#multi-item-example" data-slide-to="0" class="active"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            {{-- <div class="carousel-item active">
                            a<div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>



            <!--<div class="col-md-3 item-slider">
                <div class="left-deals-day-slider">
                    {{-- <div id="sidebar_sliderBottom1"> --}}
                        <div id="multi-item-example-2" class="carousel slide carousel-multi-item" data-ride="carousel">
                            <ol class="carousel-indicators product-slider">
                                {{-- <li data-target="#multi-item-example-2" data-slide-to="0" class="active"></li> --}}
                            </ol>
                            <div class="carousel-inner" role="listbox">
                                {{-- <div class="carousel-item active">
                                    <div class="left-small-slider-outer">
                                        <a href="#"><img src="{{ asset('front_view/images/left-banner-03.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
            <div class="col-md-3 item-slider">
                <div class="left-deals-day-slider">
                    {{-- <div id="sidebar_sliderBottom2"> --}}
                    <div id="multi-item-example-3" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            {{-- <li data-target="#multi-item-example-3" data-slide-to="0" class="active"></li> --}}
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            {{-- <div class="carousel-item active">
                                    <div class="left-small-slider-outer">
                                        <a href="#"><img src="{{ asset('front_view/images/left-banner-04.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                    </div>
                                </div> --}}
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
            </div>-->
            {{-- Sidebar Bottom 2 Slider End --}}
        </div>
        @php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
        <div class="col-md-12 covid-essentials">
            @if (!empty($category) && !empty($category[0]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">

                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[0]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[0]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>
                    <div class="products border-0_{{ $category[0]['id'] }}">

                    </div>
                </div>
            @endif


            {{-- Product Brand Section Start --}}
            <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                @if ($productbrands->isNotEmpty())
                    <div class="listing-head d-flex justify-content-between">
                        <h2>Popular brands</h2>
                        <div class="view-butn"><a href="{{ route('list.all-brands') }}" class="viewall-butn">View All</a></div>
                    </div>
                    <div class="medicliq-clients">
                        <div class="row">
                            @foreach ($productbrands as $key => $value)
                                <div class="col-sm client-outer">
                                    <a href="{{ url('/productlisting?productbrand%5B%5D=') . $value->id }}">
                                        @if ($value->image != '')
                                            <img src="{{ asset('assets/uploads/brands') . '/' . $value->image }}" alt="">
                                        @else
                                            <img src="{{ asset('front_view/images/brand-dummy.png') }}" class="img-fluid">
                                        @endif
                                        <h6>{{ ucfirst($value->name) }}</h6>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            {{-- Product Brand Section End --}}

            @if (!empty($category) && !empty($category[1]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[1]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[1]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>
                    <div class="products border-0_{{ $category[1]['id'] }}">

                    </div>
                </div>
            @endif


            {{-- MainBody Middle Banner Start --}}
            <div class="discount-banner" id="mainbody_plainMiddle">
                <div class="row"></div>
            </div>
            {{-- MainBody Middle Banner End --}}

            @if (!empty($category) && !empty($category[2]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[2]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[2]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>
                    <div class="products border-0_{{ $category[2]['id'] }}">

                    </div>
                </div>
            @endif


            @if ($top_selling->isNotEmpty())
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>Top Selling Products</h2>
                        {{-- <div class="view-butn"><a href="{{ route('list.all-brands')}}" class="viewall-butn">View All</a></div> --}}
                    </div>
                    <div class="products">
                        <div class="col-md-12 products-outer">
                            <div class="row">
                                @foreach ($top_selling as $top_selling_row)
                                    <div class="col-lg-2 col-sm-4 col-6 products-content-outer">
                                        <div class="products-content">
                                            <div class="product-listing">
                                                <a href="{{ route('shopping.productdetail', $top_selling_row->product_url) }}">
                                                    @if ($top_selling_row->product_image != '')
                                                        <img src="{{ asset('assets/uploads/products/') . '/' . $top_selling_row->product_image }}" class="img-fluid" alt="">
                                                    @else
                                                        <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                    @endif
                                                </a>
                                            </div>
                                            {{-- <div class="star-icon">
                                        <a href="javascript:void(0)" class="add_wishlist" data_item="{{ $product_row->id }}">
                                            @if (!empty($wishlist) && in_array($product_row->id, array_column($wishlist, 'product_id')))
                                                <img src="{{ asset('front_view/images/star-icon.png') }}">
                                            @else
                                                <img src="{{ asset('front_view/images/wishlist.png') }}">
                                            @endif
                                        </a>
                                    </div> --}}
                                            <div class="item-head"><a href="{{ route('shopping.productdetail', $top_selling_row->product_url) }}">{{ $top_selling_row->product_name }}</a></div>
                                            <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $top_selling_row->offer_price == 0 ? number_format($top_selling_row->price, 2) : number_format($top_selling_row->offer_price, 2) }}
                                                @if ($top_selling_row->offer_price != 0)
                                                    <div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($top_selling_row->price, 2) }}</div>
                                                @endif
                                            </div>
                                            @if ($top_selling_row->offer_price != 0)
                                                @php $percent = number_format((($top_selling_row->price-$top_selling_row->offer_price)*100) /$top_selling_row->price) ;@endphp
                                                @if ($percent > 0)
                                                    <div class="discount-percent-list"><span>{{ $percent . '% Off' }}</span></div>
                                                @endif
                                            @endif

                                            <div class="product-incre-decre-outer sec_productadd">
                                                <div class="product-incre-decre">
                                                    <div class="input-group">
                                                        <input type="text" hidden class="form-control input-number" value="1" min="1" max="100" name="quant[1]">
                                                    </div>
                                                </div>
                                                <div class="add-buy-cart add-to-cart-list">

                                                    <a href="javascript:void(0)" class="btn btn {{ $top_selling_row->not_for_sale == '1' || $top_selling_row->flag == '1' ? "disable" : "add-cart-list add-cart-list_$top_selling_row->id" }}" value="{{ $top_selling_row->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                </div>
                                            </div>
                                            @if ($top_selling_row->not_for_sale == '1')
                                                <div class="not-sale">Not for online sale !</div>
                                            @elseif($top_selling_row->flag == '1')
                                                <div class="not-sale">Sold Out !</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Product Brand Section End --}}

            @if (!empty($category) && !empty($category[3]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[3]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[3]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>

                    <div class="products border-0_{{ $category[3]['id'] }}">

                    </div>
                </div>
            @endif


            @if ($top_selling_brands->isNotEmpty())
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>Top Selling brands</h2>
                        {{-- <div class="view-butn"><a href="{{ route('list.all-brands') }}" class="viewall-butn">View All</a></div> --}}
                    </div>
                    <div class="medicliq-clients">
                        <div class="row">
                            @foreach ($top_selling_brands as $key => $value)
                                <div class="col-sm client-outer">
                                    <a href="{{ url('/productlisting?productbrand%5B%5D=') . $value->brands }}">
                                        @if ($value->image != '')
                                            <img src="{{ asset('assets/uploads/brands') . '/' . $value->image }}" alt="">
                                        @else
                                            <img src="{{ asset('front_view/images/brand-dummy.png') }}" class="img-fluid">
                                        @endif
                                        <h6>{{ ucfirst($value->brand_name) }}</h6>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($category) && !empty($category[4]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[4]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[4]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>

                    <div class="products border-0_{{ $category[4]['id'] }}">

                    </div>
                </div>
            @endif




            {{-- MainBody Bottom Banner Start --}}
            <div class="small-discount-banner" id="mainbody_plainBottom">
                <div class="row"></div>
            </div>
            {{-- <div class="small-discount-banner" id="mainbody_plainBottom2">
                <div class="row"></div>
            </div> --}}
            {{-- MainBody Bottom Banner End --}}

            {{-- @php dd($category[0]); @endphp --}}

            @if (!empty($category) && !empty($category[5]))
                <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                    <div class="listing-head d-flex justify-content-between">
                        <h2>{{ $category[5]['name'] }}</h2>
                        <div class="view-butn"><a href="{{ route('shopping.productlisting', $category[5]['name']) }}" class="viewall-butn">View All</a></div>
                    </div>

                    <div class="products border-0_{{ $category[5]['id'] }}">

                    </div>
                </div>
            @endif



        </div>
        <!--col-md-9-->

    </div>
    <!--row-->
    </div>
    <!--container-->

    {{-- {{ dd($top_selling) }} --}}
    @if ($top_selling_manufactures->isNotEmpty())
        <div class="product-company-details">
            <div class="width-container">
                <h5>Top Selling </h5>

                <div class="company-details pt-4"><span>Top Selling Manufacturer:</span>
                    @foreach ($top_selling_manufactures as $top_selling_manufact_row)
                        @if ($top_selling_manufact_row->manufact)
                            <a href="{{ url('/productlisting?manufact_=') . $top_selling_manufact_row->manufact }}"><span>{{ $top_selling_manufact_row->manufact }}</span></a> {{ $loop->last ? '' : '|' }}
                        @endif
                    @endforeach
                </div>
                {{-- <h5 class="most-search-heading">Most Searched Brands</h5>
                <div class="company-details"><span>Top Searched Healthcare Brands:</span> Baidyanath | Himalaya | Medlife Essentials | Sri Sri Tattva | Dabur | Jiva Ayurveda | Patanjali | Medlife | Dettol | Medlife PL | </div>
                <div class="company-details pt-2"><span>Top Searched Pharma Brands:</span> Dabur | Digene | Johnson & Johnson | Nicogum | Nicotex | Relispray | Volitra | SBL | Ostocalcium | Cremaffin
                </div> --}}
            </div>
        </div>
    @endif

    {{-- main page tags --}}

@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('home.categories') }}",
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    $.each(response.homepageProductsArray, function(key, element) {

                        homecategoryview(element.category_id, element.products);

                    });

                }
            });

        });


        (function() {
            "use strict";
            var carousels = function() {
                $(".owl-carousel1").owlCarousel({
                    loop: true,
                    center: true,
                    margin: 0,
                    responsiveClass: true,
                    nav: true,
                    responsive: {
                        0: {
                            items: 1,
                            nav: false
                        },
                        680: {
                            items: 2,
                            nav: false,
                            loop: false
                        },
                        1000: {
                            items: 3,
                            nav: true
                        }
                    }
                });
            };

            (function($) {
                carousels();
            })(jQuery);
        })();

        jQuery("#carousel-pr").owlCarousel({
            autoplay: true,
            rewind: true,
            /* use rewind if you don't want loop */
            margin: 20,
            /*
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            */
            responsiveClass: true,
            autoHeight: true,
            autoplayTimeout: 5000,
            smartSpeed: 800,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },

                400: {
                    items: 2
                },

                600: {
                    items: 3
                },

                1024: {
                    items: 4
                },

                1366: {
                    items: 6
                }
            }
        });

        var url_BannerImage = '{{ asset('assets/uploads/promotionbanner') }}' + '/';
        var url_productImage = '{{ asset('assets/uploads/products') }}' + '/';
        var url_no_image = '{{ asset('img/no-image.jpg') }}' + '';


        $(document).ready(function() {
            getHomeBanners();
        });



        function homecategoryview(category_id, products) {
            html = '';
            html += '<div class="col-md-12 products-outer"> <div class="row">';

            $.each(products, function(key, element) {
                var product_link = '{{ route('shopping.productdetail', ':product_url') }}';
                product_link = product_link.replace(':product_url', element.product_url);
                var offer_price = parseFloat(element.offer_price);
                var price = parseFloat(element.price);
                var percent = parseFloat(((price - offer_price) * 100) / price).toFixed();

                html += ' <div class="col-lg-2 col-sm-4 col-6 products-content-outer">';
                html += '<div class="products-content">';
                html += '<div class="item-img product-listing">';

                html += '<a href="' + (element.product_url != null ? product_link : '') + '">'
                html += '<img class="d-block w-100" src="' + (element.product_image != null ? url_productImage + element.product_image : url_no_image) + '" class="img-fluid" ></a>';

                html += '</div>';
                html += '<div class="item-head"><a href="' + product_link + '">' + element.product_name + '</a></div>';

                html += ' <div class="item-price">' + currencyIcon + ' ' + (element.offer_price == 0 ? price.toFixed(2) : offer_price.toFixed(2)) + '';
                html += (element.offer_price != 0 ? '<div class="old-price">' + currencyIcon + ' ' + price.toFixed(2) + '</div>' : '');
                html += '</div>';
                if (offer_price != 0) {
                    html += (percent > 0 ? '<div class="discount-percent-list"><span>' + percent + '% Off</span></div>' : '');
                }
                html += '<div class="product-incre-decre-outer sec_productadd">';
                html += '<div class="product-incre-decre">';
                html += '<div class="input-group">';
                html += '<input type="text" hidden class="form-control input-number" value="1" min="1" max="100" name="quant[1]">';
                html += '</div>';
                html += '</div>';
                html += '<div class="add-buy-cart add-to-cart-list">';
                html += '<a href="javascript:void(0)" class="btn ' + (element.flag == 1 || element.not_for_sale == 1 ? 'disable' : 'add-cart-list add-cart-list_' + element.id ) + '" id="add-cart-list_' + element.id + '" value="' + element.id + '"><i class="fas fa-shopping-cart"></i>Add</a>';
                html += '</div>';
                html += '</div>';
                html += (element.not_for_sale == 1 ? '<div class="not-sale">Not for online sale !</div>' : '');
                html += (element.flag == 1 ? '<div class="not-sale">Sold Out !</div>' : '');



                html += '</div></div>'
            })

            html += '</div></div>';
            $('.border-0_' + category_id).html(html);

            //========

        }



        function getHomeBanners() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ route('home.getBanners') }}',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {

                    //--------MainBody Slider1--
                    var html_mainbody_slider = '';
                    var html_mainbody_sl = '';

                    if (response.mainSlider.Images.length >= 1) {
                        $.each(response.mainSlider.Images, function(key, element) {
                            html_mainbody_sl += '<li data-target="#carousel-example-2" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_mainbody_slider += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_mainbody_slider += '<div class="view">';
                            html_mainbody_slider += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img class="d-block w-100 b-lazy" data-src="' + url_BannerImage + element.image + '" data-src-small="' + url_BannerImage + element.image + '" src="' + url_BannerImage + element.image + '" alt="Slider ' + (key + 1) + '"></a>';
                            html_mainbody_slider += '<div class="mask rgba-black-light"></div>';
                            html_mainbody_slider += '</div>';
                            html_mainbody_slider += '<div class="carousel-caption top-main-courosel">';

                            if (response.mainSlider.details.title != '' && response.mainSlider.details.title != null) {
                                // html_mainbody_slider += '<h3 class="h3-responsive">' + response.mainSlider.details.title + '</h3>';
                            }

                            //html_mainbody_slider += '<div class="shop-now"><a href="' + (element.banner_url != null ? element.banner_url : '') + '">Shop Now <i class="fas fa-shopping-cart"></i></a></div>';
                            // html_mainbody_slider += '<div class="learn-more"><a href="#">Learn More <i class="fas fa-chevron-right"></i></a></div>';
                            html_mainbody_slider += '</div>';
                            html_mainbody_slider += '</div>';
                        });

                        $('#mainBody_slider').find('.carousel-indicators').html(html_mainbody_sl);
                        $('#mainBody_slider').find('.carousel-inner').html(html_mainbody_slider);
                    }

                    //--------Mainbody Plain Middle--
                    var html_mainbodyplainMid = '';
                    if (response.middleBanner.Images.length >= 1) {
                        $.each(response.middleBanner.Images, function(key, element) {
                            html_mainbodyplainMid += '<div class="col-lg-6 col-sm-6 discount-banner-outer wow fadeInUp" data-wow-delay=".20s">';
                            html_mainbodyplainMid += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid b-lazy" data-src="' + url_BannerImage + element.image + '" alt="Banner Middle"></a>';
                            html_mainbodyplainMid += '</div>';
                        });

                        $('#mainbody_plainMiddle').find('.row').html(html_mainbodyplainMid);
                    }

                    //--------Mainbody Plain Bottom--
                    if (response.bottomBanner !== undefined) {
                        var html_mainbodyplainBot = '';
                        if (response.bottomBanner.Images.length >= 1) {
                            $.each(response.bottomBanner.Images, function(key, element) {
                                html_mainbodyplainBot += '<div class="col-lg-3 col-sm-6 col-6 small-discount-banner-outer wow fadeInUp"  data-wow-delay=".20s">';
                                html_mainbodyplainBot += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid" alt="Banner Bottom"></a>';
                                html_mainbodyplainBot += '</div>';

                            });

                            $('#mainbody_plainBottom').find('.row').append(html_mainbodyplainBot);
                        }
                    }

                    //--------Mainbody Plain Bottom2--
                    if (response.bottomBanner2 !== undefined) {
                        var html_mainbodyplainBot2 = '';
                        if (response.bottomBanner2.Images.length >= 1) {
                            $.each(response.bottomBanner2.Images, function(key, element) {
                                html_mainbodyplainBot2 += '<div class="col-lg-3 col-sm-6 col-6 small-discount-banner-outer wow fadeInUp"  data-wow-delay=".20s">';
                                html_mainbodyplainBot2 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid" alt="Banner Bottom"></a>';
                                html_mainbodyplainBot2 += '</div>';

                            });

                            $('#mainbody_plainBottom').find('.row').append(html_mainbodyplainBot2);
                        }
                    }
                    //--------Mainbody Plain Bottom3--
                    if (response.bottomBanner3 !== undefined) {
                        var html_mainbodyplainBot3 = '';
                        if (response.bottomBanner3.Images.length >= 1) {
                            $.each(response.bottomBanner3.Images, function(key, element) {
                                html_mainbodyplainBot3 += '<div class="col-lg-3 col-sm-6 col-6 small-discount-banner-outer wow fadeInUp"  data-wow-delay=".20s">';
                                html_mainbodyplainBot3 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid" alt="Banner Bottom"></a>';
                                html_mainbodyplainBot3 += '</div>';

                            });

                            $('#mainbody_plainBottom').find('.row').append(html_mainbodyplainBot3);
                        }
                    }

                    //--------Mainbody Plain Bottom4--

                    if (response.bottomBanner4 !== undefined) {

                        var html_mainbodyplainBot4 = '';
                        if (response.bottomBanner4.Images.length >= 1) {
                            $.each(response.bottomBanner4.Images, function(key, element) {
                                html_mainbodyplainBot4 += '<div class="col-lg-3 col-sm-6 col-6 small-discount-banner-outer wow fadeInUp"  data-wow-delay=".20s">';
                                html_mainbodyplainBot4 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid" alt="Banner Bottom"></a>';
                                html_mainbodyplainBot4 += '</div>';

                            });

                            $('#mainbody_plainBottom').find('.row').append(html_mainbodyplainBot4);
                        }
                    }

                    //--------Sidebar Slider Top1--
                    var html_sidebarSliderTop1 = '';
                    var sidebar_slTop1 = '';

                    if (response.sidebarSl_top1.Images.length >= 1) {
                        $.each(response.sidebarSl_top1.Images, function(key, element) {
                            sidebar_slTop1 += '<li data-target="#multi-item-example" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderTop1 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderTop1 += '<div class="left-small-slider-outer">';
                            html_sidebarSliderTop1 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderTop1 += '</div>';
                            html_sidebarSliderTop1 += '</div>';
                        });

                        $('#sidebar_sliderTop1').find('.carousel-indicators').html(sidebar_slTop1);
                        $('#sidebar_sliderTop1').find('.carousel-inner').html(html_sidebarSliderTop1);
                    }

                    //--------Sidebar Plain Top2--
                    var html_sidebarPlainTop2 = '';

                    // if (response.sidebarPlain_top2.Images != '') {
                    //     html_sidebarPlainTop2 += '<a href="' + (response.sidebarPlain_top2.Images.banner_url != null ? response.sidebarPlain_top2.Images.banner_url : '') + '"><img src="' + url_BannerImage + response.sidebarPlain_top2.Images.image + '" class="img-fluid mx-auto d-block"></a>';
                    //     $('#sidebar_plainTop2').html(html_sidebarPlainTop2);
                    // }

                    //--------Sidebar slider Top2
                    if (response.sidebarSl_top2.Images.length >= 1) {

                        var html_sidebarSliderTop2 = '';
                        var sidebar_slTop2 = '';
                        $.each(response.sidebarSl_top2.Images, function(key, element) {
                            sidebar_slTop2 += '<li data-target="#multi-item-example2" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderTop2 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderTop2 += '<div class="left-small-slider-outer">';
                            html_sidebarSliderTop2 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderTop2 += '</div>';
                            html_sidebarSliderTop2 += '</div>';
                        });

                        $('#sidebar_sliderTop2').find('.carousel-indicators').html(sidebar_slTop2);
                        $('#sidebar_sliderTop2').find('.carousel-inner').html(html_sidebarSliderTop2);
                    }

                    //--------Sidebar slider Top3

                    if (response.sidebarSl_top3.Images.length >= 1) {
                        var html_sidebarSliderTop3 = '';
                        var sidebar_slTop3 = '';
                        $.each(response.sidebarSl_top3.Images, function(key, element) {
                            sidebar_slTop3 += '<li data-target="#multi-item-example3" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderTop3 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderTop3 += '<div class="left-small-slider-outer">';
                            html_sidebarSliderTop3 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderTop3 += '</div>';
                            html_sidebarSliderTop3 += '</div>';
                        });

                        $('#sidebar_sliderTop3').find('.carousel-indicators').html(sidebar_slTop3);
                        $('#sidebar_sliderTop3').find('.carousel-inner').html(html_sidebarSliderTop3);
                    }
                    //--------Sidebar slider Top4

                    if (response.sidebarSl_top4.Images.length >= 1) {
                        var html_sidebarSliderTop4 = '';
                        var sidebar_slTop4 = '';
                        $.each(response.sidebarSl_top4.Images, function(key, element) {
                            sidebar_slTop4 += '<li data-target="#multi-item-example4" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderTop4 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderTop4 += '<div class="left-small-slider-outer">';
                            html_sidebarSliderTop4 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderTop4 += '</div>';
                            html_sidebarSliderTop4 += '</div>';
                        });

                        $('#sidebar_sliderTop4').find('.carousel-indicators').html(sidebar_slTop4);
                        $('#sidebar_sliderTop4').find('.carousel-inner').html(html_sidebarSliderTop4);
                    }

                    //--------Sidebar Slider Bottom1--
                    var html_sidebarSliderBottom1 = '';
                    var sidebar_slBottom1 = '';
                    if (response.sidebarSl_bottom1 !== undefined) {
                        if (response.sidebarSl_bottom1.Images.length >= 1) {
                            $.each(response.sidebarSl_bottom1.Images, function(key, element) {
                                sidebar_slBottom1 += '<li data-target="#multi-item-example-2" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                                html_sidebarSliderBottom1 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                                html_sidebarSliderBottom1 += '<div class="left-small-slider-outer wow fadeInUp">';
                                html_sidebarSliderBottom1 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                                html_sidebarSliderBottom1 += '</div>';
                                html_sidebarSliderBottom1 += '</div>';
                            });

                            $('#sidebar_sliderBottom1').find('.carousel-indicators').html(sidebar_slBottom1);
                            $('#sidebar_sliderBottom1').find('.carousel-inner').html(html_sidebarSliderBottom1);
                        }
                    }

                    //--------Sidebar Slider Bottom2--
                    var html_sidebarSliderBottom2 = '';
                    var sidebar_slBottom2 = '';

                    if (response.sidebarSl_bottom2.Images.length >= 1) {
                        $.each(response.sidebarSl_bottom2.Images, function(key, element) {
                            sidebar_slBottom2 += '<li data-target="#multi-item-example-3" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderBottom2 += '<div class="carousel-item ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderBottom2 += '<div class="1left-small-slider-outer wow fadeInUp">';
                            html_sidebarSliderBottom2 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderBottom2 += '</div>';
                            html_sidebarSliderBottom2 += '</div>';
                        });

                        $('#sidebar_sliderBottom2').find('.carousel-indicators').html(sidebar_slBottom2);
                        $('#sidebar_sliderBottom2').find('.carousel-inner').html(html_sidebarSliderBottom2);
                    }

                }
            });
        }
    </script>
@endsection
