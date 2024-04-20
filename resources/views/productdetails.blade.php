@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')

    <!--END-nav-->

    @if (count($errors) > 0)
        <div class="row">
            <div class="col-md-12 alert alert-danger">
                <div class="text-danger">{{ $errors->all()[0] }}</div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="Approverdetails" tabindex="-1" role="dialog" aria-labelledby="ApproverdetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 600px;;height: 750px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="ApproveModalLabel">Prescription approved person details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="prescription_id" value="0">
                    <div class="col-md-12">
                        @if ($approverDetails != '')
                            <table id="dtBasicExample" class="table table-striped table-bordered table-md " cellspacing="0" width="100%">
                                <tr>
                                    <td>Approved by</td>
                                    <td>{{ $approverDetails->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $approverDetails->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{ $approverDetails->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Job Position</td>
                                    <td>{{ $approverDetails->job_title }}</td>
                                </tr>


                            </table>
                            <strong>Licence</strong><br>
                            <embed src='{{ asset('assets/uploads/admin_licence/') }}/{{ $approverDetails->licence }}' #toolbar=0 width="100%" height="350px">
                        @endif

                    </div>
                </div>
                <div class="modal-footer">
                    <div id="ajax_loader" style="display:none;"><img src="{{ asset('img/ajax-loader.gif') }}"></div>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">

                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home" aria-hidden="true"></i></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('list.allproductlisting') }}">Product Listings</a></li>
                @if (!empty($categories))
                    @if ($categories->getParentsNames() !== $categories->name)
                        @foreach ($categories->getParentsNames()->reverse() as $item)
                            @if ($item->parent_id == 0)
                                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('shopping.productlisting', $item->name) }}">{{ $item->name }}</a></li>
                            @else
                                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('shopping.productlisting', $item->name) }}">{{ $item->name }}</a></li>
                            @endif
                        @endforeach
                    @endif
                    <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('shopping.productlisting', $categories->name) }}">{{ $categories->name }}</a></li>
                    <li class="breadcrumb-item inner-breadcrumb-dtls active">{{ $product->product_name }}</li>
                @endif

            </ol>
        </nav>
    </div>
    @php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
    <div class="width-container main-product-detail-page">
        <div class="row">

            <div class="col-md-9 product-detail-page-outer">
                <div class="row">

                    <div class="col-md-6 product-detail-page-item detail-img-container">
                        <div class="exzoom hidden" id="exzoom">
                            <div class="exzoom_img_box">

                                <ul class="exzoom_img_ul">
                                    @if ($product_images->isNotEmpty())
                                        @foreach ($product_images as $key => $value)
                                            @if ($value->product_image)
                                                <li data-target="#carousel-thumb" data-slide-to="{{ $key }}" class="active">
                                                    <img src="{{ asset('assets/uploads/products/') }}/{{ $value->product_image }}" width="100">
                                                </li>
                                            @endif
                                        @endforeach
                                    @else
                                        <li class="no-image-details"><img src="{{ asset('img/no-image.jpg') }}"></li>
                                    @endif

                                </ul>


                            </div>
                            @if ($product_images->isNotEmpty())
                                <div class="exzoom_nav"></div>
                                <p class="exzoom_btn">
                                    <a href="javascript:void(0);" class="exzoom_prev_btn">
                                        < </a>
                                            <a href="javascript:void(0);" class="exzoom_next_btn"> > </a>
                                </p>
                            @endif
                        </div>

                        <!--/.END-Carousel Wrapper-->
                    </div>

                    {{-- <div class="col-md-6 product-detail-page-item">
                        <div id="carousel-thumb" class="carousel slide carousel-fade carousel-thumbnails" data-ride="carousel">
                            <div class="carousel-inner" role="listbox">

                                @if ($product_images->isNotEmpty())
                                    @foreach ($product_images as $key => $value)
                                        @if ($value->product_image)
                                            <div class="carousel-item{{ $key == 0 ? ' active' : '' }}">
                                                <img class="d-block w-100" src="{{ asset('assets/uploads/products/') }}/{{ $value->product_image }}" alt="{{ $value->product_image }}">
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                @endif
                            </div>
                            <a class="carousel-control-prev" href="#carousel-thumb" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel-thumb" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                            <ol class="carousel-indicators small-curosel">
                                @foreach ($product_images as $key => $value)
                                    @if ($value->product_image)
                                        <li data-target="#carousel-thumb" data-slide-to="{{ $key }}" class="active">
                                            <img src="{{ asset('assets/uploads/products/') }}/{{ $value->product_image }}" width="100">
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                        <!--/.Carousel Wrapper-->
                    </div> --}}
                    @php $ratepercentage = (bcdiv($product->getAverageRatingAttribute(),1,1)/5)*100; @endphp
                    <div class="col-md-6 product-detail-page-product">
                        @if(isset($shareComponent))
                        <div class="items_share">
                            {{-- <i class="fa fa-share" aria-hidden="true"></i> --}}
                            <i class="fa fa-share-alt" id="share-btn" aria-hidden="true"></i>
                            <div class="items_share_cont">
                                {!! $shareComponent !!}
                            </div>
                        </div>
                        @endif
                        <div class="product-detail-content">
                            <h2>{{ $product->product_name }}</h2>
                            @if ($type['name'] != 'All Medicines')
                                <div class="item-rating">
                                    <div class="star-ratings-sprite">
                                        <span style="width:{{ $ratepercentage }}%" class="star-ratings-sprite-rating"></span>
                                    </div>
                                    <div class="item-review"><a href="#">{{ bcdiv($product->getAverageRatingAttribute(), 1, 1) }} ({{ $total_reviews }} Reviews)</a></div>
                                </div>
                            @endif
                            @if ($product->prescription == 1)
                                <div class="required-pre">
                                    <span><img src="{{ asset('assets/uploads/prescription/icon/rx_icon.png') }}"> Prescription required</span><br>
                                </div>
                            @endif
                            @if ($product->not_for_sale == '1')
                                <div class="not-online-sale"><span>Not for online sale !</span></div>
                            @endif

                            {{-- <p>{{ $product->tagline }}</p> --}}
                            <ul class="product-description">


                                {{-- @if ($product->category != '')
                                    <li>
                                        <label>Category:</label>
                                        <div>{{ $product->category != '' ? $product->category : 'N/a' }}</div>
                                    </li>
                                @endif --}}

                                {{-- @if ($type['id'] != '')
                                    <li>
                                        <label>Type:</label>
                                        <div>{{ $type['id'] != '' ? $type['name'] : 'N/a' }}</div>
                                    </li>
                                @endif --}}

                                @if ($product->manufacturer != '')
                                    <div class="product-detail-descr p-0 feat-product-detail">
                                        <h6>Manufacturer</h6>
                                        <P><a href="{{ url('/productlisting?manufact_=') . $product->manufacturer }}">{{ $product->manufacturer }}</a></P>
                                    </div>
                                @endif
                                {{-- @if ($product->product_pack != '')
                                    <div class="product-detail-descr p-0 feat-product-detail">
                                        <h6>Package</h6>
                                       <p> {{ $product->product_pack != '' ? $product->product_pack : 'N/a' }}</p>
                                    </div>
                                @endif --}}

                                @php
                                    $contents = '';
                                @endphp

                                @if (count($ProductContents) > 0)
                                    <div class="detail-key-incre">
                                        <h6>Key Ingredients</h6>
                                        <ul>
                                            @foreach ($ProductContents as $value)
                                                <li> {{ $value['name'] }}</li>
                                                @php $contents .= $value['name'].',' @endphp
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($product->features != '')
                                    <div class="product-detail-descr p-0 feat-product-detail">
                                        <h6>Features</h6>
                                        <p>
                                            {{ $product->features != '' ? $product->features : 'N/a' }}
                                        </p>
                                    </div>
                                @endif

                                @if ($product->quantity != '')
                                    <div class="product-detail-descr p-0 feat-product-detail">
                                        <h6>Quantity</h6>
                                        <div class="item-qty">{{ $product->quantity != '' ? $product->quantity : 'N/a' }}</div>
                                    </div>
                                @endif



                                {{-- @if (count($product_variants) > 0)
                                    <label>Variants:</label>
                                    <div class="products related-products-outer">
                                        <div class="col-md-12 products-outer">
                                            <div class="row">
                                                @if (count($product_variants) > 0)
                                                    @foreach ($product_variants as $key => $product_variants_row)
                                                        <div class="col-lg-3 col-sm-4 col-6 -price-pr-content">
                                                            <a href="{{ route('shopping.productdetail', $product_variants_row->product_url) }}">
                                                                @if ($product_variants_row->product_image != '')
                                                                    <img src="{{ asset('assets/uploads/products/') }}/{{ $product_variants_row->product_image }}" class="img-fluid" alt="">
                                                                @else
                                                                    <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                                @endif
                                                            </a>

                                                            <div class="item-head"><a href="{{ route('shopping.productdetail', $product_variants_row->product_url) }}">{{ $product_variants_row->product_name }}</a></div>
                                                            <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $product_variants_row->offer_price == 0 ? number_format($product_variants_row->price, 2) : number_format($product_variants_row->offer_price, 2) }}
                                                                @if ($product_variants_row->offer_price != 0)<div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($product_variants_row->price, 2) }}</div>@endif
                                                            </div>
                                                            @if ($product_variants_row->offer_price != 0)
                                                        @php $percent = number_format((($product_variants_row->price -$product_variants_row->offer_price)*100) /$product_variants_row->price);@endphp
                                                        @if ($percent > 0)
                                                            <div class="discount-percent"><span>{{ $percent . '% Off' }}</span></div>
                                                        @endif
                                                       @endif
                                                            @if ($product_variants_row->not_for_sale == '1')
                                                             <div class="not-sale">Not for online sale !</div>
                                                            @elseif($product_variants_row->flag == '1')
                                                             <div class="not-sale">Sold Out !</div>
                                                             @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    N/a
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif --}}
                            </ul>

                            {{-- @if ($product->prescription == 1 && $enablePrescription === true && $allowPurchase === false)
                                <div class="priscription">
                                    <button class="btn" id="add_prescription" @guest('user') disabled @endguest><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                    <input type="file" id="prescription_file" style="width:0;height:0">
                                </div>
                            @endif --}}

                            <div class="item-price detail-price">{!! $common_settings[$currency_key]['value'] !!}{{ $product->offer_price == 0 ? number_format($product->price, 2) : number_format($product->offer_price, 2) }}
                                @if ($product->offer_price != 0)
                                    <div class="old-price detail-old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($product->price, 2) }}</div>
                                @endif
                            </div>

                            @if ($product->offer_price != 0)
                                @php $percent = number_format((($product->price-$product->offer_price)*100) /$product->price) ;@endphp
                                @if ($percent > 0)
                                    <div class="discount-percent"><span>{{ $percent . '% Off' }}</span></div>
                                @endif
                            @endif
                            {{-- @if ($product->prescription == 1 && $enablePrescription === true && $allowPurchase === false) --}}
                            {{-- <button class="btn detail-like add_wishlist" data_item="{{ $product->id }}">
                                        @if (!empty($wishlist) && in_array($product->id, array_column($wishlist, 'product_id')))
                                            <img src="{{ asset('front_view/images/star-icon.png') }}">
                                        @else
                                            <img src="{{ asset('front_view/images/wishlist.png') }}">
                                        @endif
                                    </button> --}}
                            {{-- @endif --}}

                            {{-- @if ($product->prescription == 1 && $allowPurchase === true)
                                    <button class="btn buy-now" id="buy_now" data-id="{{ $product->id }}">Buy Now</button> --}}

                            {{-- <button class="btn detail-like add_wishlist" data_item="{{ $product->id }}">
                                        @if (!empty($wishlist) && in_array($product->id, array_column($wishlist, 'product_id')))
                                            <img src="{{ asset('front_view/images/star-icon.png') }}">
                                        @else
                                            <img src="{{ asset('front_view/images/wishlist.png') }}">
                                        @endif

                                    </button> --}}

                            {{-- @endif --}}


                            {{-- @if ($product->prescription == 1 && $allowPurchase === true && $show_approverdetails === true)
                                <a href="" data-toggle="modal" data-target="#Approverdetails">Prescription approved person details</a>
                            @endif --}}


                            {{-- @if ($product->prescription != 1) --}}

                            @if ($product->not_for_sale != '1')
                                @if ($product->flag != '1')
                                    <div class="row product-incre-decre-outer sec_productadd prdtls-add">
                                        <div class="product-incre-decre">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </span>
                                                <input type="text" class="form-control input-number" value="1" min="1" max="100" name="quant[1]">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-number" data-type="plus" data-field="quant[1]">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col add-buy-cart">
                                            <button class="btn add-cart" value="{{ $product->id }}">Add to Cart</button>
                                            <button class="btn buy-now" id="buy_now" data-id="{{ $product->id }}">Buy Now</button>
                                            {{-- <button class="btn detail-like add_wishlist" data_item="{{ $product->id }}">
                                            @if (!empty($wishlist) && in_array($product->id, array_column($wishlist, 'product_id')))
                                                <img src="{{ asset('front_view/images/star-icon.png') }}">
                                            @else
                                                <img src="{{ asset('front_view/images/wishlist.png') }}">
                                            @endif
                                        </button> --}}
                                        </div>
                                    </div>
                                @else
                                    <div><span style="color: red">Sold Out !</span></div><br>
                                @endif
                                {{-- @else
                            <div><span style="color: red">Not for sale !</span></div> --}}
                            @endif



                            {{-- @endif --}}
                        </div>


                        @if (count($product_variants) > 0)
                            @foreach ($product_variants as $key => $product_variants_row)
                                <div class="col-md-12 addit-pack-outer">
                                    <div class="varient">Variant</div>
                                    <div class="additional-pack row align-items-center">
                                        <div class="col-md-6 pack-item-out">
                                            <div class="item-sm-head"><a href="{{ route('shopping.productdetail', $product_variants_row->product_url) }}">{{ $product_variants_row->product_name }}</a></div>
                                            <div class="pack-item">
                                                <a href="{{ route('shopping.productdetail', $product_variants_row->product_url) }}">
                                                    @if ($product_variants_row->product_image != '')
                                                        <img src="{{ asset('assets/uploads/products/') }}/{{ $product_variants_row->product_image }}" class="img-fluid" alt="">
                                                    @else
                                                        <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pack-right-item">
                                            <!-- <div class="item-head"><a href="#">TRESemme 2-in-1 Shampoo And Conditioner</a></div> -->

                                            @if ($product_variants_row->offer_price != 0)
                                                <div class="old-sm-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($product_variants_row->price, 2) }}</div>
                                                <div class="item-sm-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($product_variants_row->offer_price, 2) }}</div>
                                            @else
                                                <div class="item-sm-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($product_variants_row->price, 2) }}</div>
                                            @endif
                                            @if ($product_variants_row->not_for_sale != '1')
                                                @if ($product_variants_row->flag != '1')
                                                    <button class="btn buy-sm-now" id="buy_now" data-id="{{ $product_variants_row->id }}">Buy Now</button>
                                                @else
                                                    <div class="variant-sold-out">Sold Out !</div>
                                                @endif
                                            @else
                                                <div class="variant-not-sale">Not for online sale !</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>


                    <div id="tabs" class="productdetail-desc-tab">
                        <div class="col-md-12 product-full-desc">
                            <nav class="description-tab">
                                <div class="nav nav-tabs nav-fill description-tab-contnt" id="nav-tab" role="tablist">
                                    <a class="nav-item na -link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Description</a>
                                    @if ($type['name'] != 'All Medicines')
                                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Reviews</a>
                                    @endif
                                    {{-- <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Shipping & Delivery</a> --}}
                                </div>
                            </nav>
                        </div>
                        <div class="tab-content desc-tab-outer" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="tab-details product-detail-descr">
                                    @if ($product->description != '')
                                        <h6>Product Description</h6>
                                        <p>
                                            {!! $product->description !!}
                                        </p>
                                    @endif

                                    @if ($product->how_to_use != '')
                                        <h6>How to use</h6>
                                        <p>
                                            {!! $product->how_to_use !!}
                                        </p>
                                    @endif

                                    @if (count($medicine_uses) > 0)
                                        <h6>Uses</h6>
                                        <p>
                                            @foreach ($medicine_uses as $medicine_uses_Row)
                                                {{ ucfirst($medicine_uses_Row['medicine_for']) }} {!! $medicine_uses_Row['name'] . ($loop->last ? '' : '<br>') !!}
                                            @endforeach
                                        </p>
                                    @endif

                                    {{-- @if ($product->benefits != '')
                                        <h6>Benefits</h6>
                                        <p>
                                            {!! $product->benefits !!}
                                        </p>
                                    @endif --}}

                                    @if ($product->side_effects != '')
                                        <h6>Side Effects</h6>
                                        <p>
                                            {!! $product->side_effects !!}
                                        </p>
                                    @endif

                                    {{-- @php
                                        $contents = '';
                                    @endphp
                                    @if (!empty($ProductContents))
                                        <div class="detail-key-incre">
                                            <h6>Key Ingredients:</h6>
                                            <ul>
                                                @foreach ($ProductContents as $value)
                                                    <li>{{ $value['name'] }}</li>
                                                    @php $contents .= $value['name'].',' @endphp
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif --}}



                                    @if ($product->storage != '')
                                        <h6>Storage </h6>
                                        <p> {{ $product->storage }}&#8451;</p>
                                    @endif

                                    @if ($product->brand_name != '')
                                        <div class="brand-dtl">
                                            <h6>Brand </h6>
                                            <p><a href="{{ url('/productlisting?productbrand%5B%5D=') . $product->brands }}">
                                                    @if ($product->brand_image != '')
                                                        <img src="{{ asset('assets/uploads/brands') }}/{{ $product->brand_image }}">
                                                    @else
                                                        {{ $product->brand_name }}
                                                    @endif

                                                </a></p>
                                        </div>
                                    @endif

                                    {{-- @if ($product->manufacturer!='')
                                        <div class="brand-dtl">
                                            <h6>Manufacturer </h6>
                                            <p><a href="{{ url('/productlisting?manufact_=') . $product->manufacturer }}">
                                                    @if ($product->manufacturer_image != '')
                                                        <img src="{{ asset('assets/uploads/manufacturers') }}/{{ $product->manufacturer_image }}">
                                                    @else
                                                        {{ $product->manufacturer }}
                                                    @endif

                                                </a>
                                            </p>
                                        </div>
                                    @endif --}}

                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="comments-container pt-4">
                                    <div class="form-row align-items-center">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="well well-sm">
                                                <div class="row">
                                                    <div class="col-xs-12 col-md-3 text-center">
                                                        <h1 class="rating-num">{{ bcdiv($product->getAverageRatingAttribute(), 1, 1) }}</h1>
                                                        <div class="star-ratings-sprite">
                                                            <span style="width:{{ $ratepercentage }}%" class="star-ratings-sprite-rating"></span>
                                                        </div>
                                                        <div class="">
                                                            <br> <i class="fas fa-user text-muted"></i> {{ $total_reviews }} Reviews
                                                        </div>

                                                    </div>

                                                    @php
                                                        $rateone = 0;
                                                        $ratetwo = 0;
                                                        $ratethree = 0;
                                                        $ratefour = 0;
                                                        $ratefive = 0;

                                                        if ($product->getRatingavg() != 0) {
                                                            $ratefive = bcdiv(($product->getRatingavg(5) / $product->getRatingavg()) * 100, 1, 1);
                                                            $ratefour = bcdiv(($product->getRatingavg(4) / $product->getRatingavg()) * 100, 1, 1);
                                                            $ratethree = bcdiv(($product->getRatingavg(3) / $product->getRatingavg()) * 100, 1, 1);
                                                            $ratetwo = bcdiv(($product->getRatingavg(2) / $product->getRatingavg()) * 100, 1, 1);
                                                            $rateone = bcdiv(($product->getRatingavg(1) / $product->getRatingavg()) * 100, 1, 1);
                                                        }

                                                    @endphp
                                                    <div class="col-xs-12 col-md-9">
                                                        <div class="row rating-desc">
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="glyphicon glyphicon-star"><i class="fas fa-star text-muted"></i></span>5
                                                            </div>

                                                            <div class="col-xs-8 col-md-9">
                                                                <div class="progress progress-striped">
                                                                    <div class="progress-bar progress-bar-success bg-1" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ratefive }}%">
                                                                        <span class="sr-only">{{ $ratefive }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end 5 -->
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="glyphicon glyphicon-star"><i class="fas fa-star text-muted"></i></span>4
                                                            </div>
                                                            <div class="col-xs-8 col-md-9">
                                                                <div class="progress">
                                                                    <div class="progress-bar progress-bar-success bg-2" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ratefour }}%">
                                                                        <span class="sr-only">{{ $ratefour }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end 4 -->
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="glyphicon glyphicon-star"><i class="fas fa-star text-muted"></i></span>3
                                                            </div>
                                                            <div class="col-xs-8 col-md-9">
                                                                <div class="progress">
                                                                    <div class="progress-bar progress-bar-info bg-3" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ratethree }}%">
                                                                        <span class="sr-only">{{ $ratethree }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end 3 -->
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="glyphicon glyphicon-star"><i class="fas fa-star text-muted"></i></span>2
                                                            </div>
                                                            <div class="col-xs-8 col-md-9">
                                                                <div class="progress">
                                                                    <div class="progress-bar progress-bar-warning bg-4" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ratetwo }}%">
                                                                        <span class="sr-only">{{ $ratetwo }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end 2 -->
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="glyphicon glyphicon-star"><i class="fas fa-star text-muted"></i></span>1
                                                            </div>
                                                            <div class="col-xs-8 col-md-9">
                                                                <div class="progress">
                                                                    <div class="bg-danger progress-bar progress-bar-danger bg-5" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: {{ $rateone }}%">
                                                                        <span class="sr-only">{{ $rateone }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- end 1 -->
                                                        </div>
                                                        <!-- end row -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Add new review for product --}}
                                        @auth('user')
                                            @if ($ReviewAllow)
                                                <div class="col-md-12 comment-input pt-5 rating_product">
                                                    <div class="rating">
                                                        <span class="glyphicon glyphicon-star star_rateproduct" data-star="1"><i class="fas fa-star"></i></span>
                                                        <span class="glyphicon glyphicon-star star_rateproduct" data-star="2"><i class="fas fa-star"></i></span>
                                                        <span class="glyphicon glyphicon-star star_rateproduct" data-star="3"><i class="fas fa-star"></i></span>
                                                        <span class="glyphicon glyphicon-star star_rateproduct" data-star="4"><i class="fas fa-star"></i></span>
                                                        <span class="glyphicon glyphicon-star star_rateproduct" data-star="5"><i class="fas fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" class="hid_productrate" value="0">

                                                    <textarea class="form-control shadow-sm height-div product_review" id="exampleFormControlTextarea1" rows="3" placeholder="Write your review..." style="resize: none;"></textarea>
                                                    <div class="detail-buttons mt-4">
                                                        <button type="button" class="btn btn-dark text-white float-right border-radius-20 rateproduct" data-id="{{ $product->id }}">Add Review</button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth

                                    </div>

                                    <!--review list section-->
                                    @if (!$reviews->isEmpty())
                                        @foreach ($reviews as $review_content)
                                            <div class="comment-list border mt-4">
                                                <div class="comment-head">
                                                    <span class="btn btn-green-cart">{{ $review_content->rating }}<small class="f-9"><i class="fas fa-star"></i></small></span>
                                                    <span class="font-weight-bold">
                                                        @if ($review_content->rating == '5')
                                                            Highly recommended
                                                        @elseif($review_content->rating == '4')
                                                            Recommended
                                                        @elseif($review_content->rating == '3')
                                                            Average
                                                        @elseif($review_content->rating == '2')
                                                            Not Bad
                                                        @else
                                                            Poor
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="comment-text px-4 py-2">{{ $review_content->reviews }}</div>
                                                <div class="comment-footer px-4 pb-3">
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <span class="comment-name">
                                                                @if (isset($review_content->user->name))
                                                                    {{ $review_content->user->name }}
                                                                @endif
                                                            </span>
                                                            <span class="verified-user"><i class="fas fa-check-circle"></i> Verified Customer</span>
                                                            <span class="comment-time">
                                                                Published on: {{ date('Y-m-d', strtotime($review_content->created_at)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        {{ $reviews->links() }}
                                    @endif

                                    <!--review view section-->
                                </div>
                            </div>
                            <div class="tab-pane fade pb-90" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="tab-details product-detail-descr">
                                    <h6>Information about Shelcal - HD Tablet</h6>
                                    <p>
                                        Shelcal - HD Tablet helps to treat Calcium deficiency that leads to weak bones as the body will start using calcium from the bone. The right amount of Vitamin D, Calcium, and Phosphorus is important for building and keeping strong bones. Shelcal - HD Tablet helps to treat Calcium deficiency that leads to weak bones as the body will start using calcium from the bone. The right amount of Vitamin D, Calcium, and Phosphorus is important for building and keeping
                                        strong bones.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 product-detail-right-slider-a">
                <div id="sidebar_sliderTop1">
                    <div id="multi-item-example" class="carousel slide carousel-multi-item" data-ride="carousel">
                        <ol class="carousel-indicators product-slider">
                            <li data-target="#multi-item-example" data-slide-to="0" class="active"></li>
                        </ol>
                        <div class="carousel-inner cosmetic-product-outer" role="listbox">
                            <div class="carousel-item active cosmetic-product-slider">
                                <div class="left-small-slider-outer">
                                    <a href="#"><img src="{{ asset('front_view/images/small-slider-01.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="left-deals-day-slider">
                    <div id="sidebar_sliderBottom2">
                        <div id="multi-item-example-3" class="carousel slide carousel-multi-item" data-ride="carousel">
                            <ol class="carousel-indicators product-slider">
                                <li data-target="#multi-item-example-3" data-slide-to="0" class="active"></li>
                            </ol>
                            <div class="carousel-inner ayurvedic-product-slider" role="listbox">
                                <div class="carousel-item active ayurvedicc-product-slider">
                                    <div class="left-small-slider-outer">
                                        <a href="#"><img src="{{ asset('front_view/images/left-banner-04.png') }}" class="img-fluid mx-auto d-block" alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($similarcontentproducts) && count($similarcontentproducts) > 0)
                <div class="col-md-12 related-product">
                    <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                        <div class="listing-head d-flex justify-content-between">
                            <h2>Products containing {{ trim($contents, ',') }}</h2>
                            <div class="view-butn"><a href="{{ url('productlisting?productcontents%5B%5D=').$product->product_url }}" class="viewall-butn">View All</a></div>
                        </div>

                        <div class="products related-products-outer">
                            <div class="col-md-12 products-outer">
                                <div class="row">
                                    @foreach ($similarcontentproducts as $key => $similarcontentproducts_row)
                                        <div class="col-lg-2 col-sm-4 col-6 products-content-outer">
                                            <div class="products-content">
                                                <div class="product-listing">
                                                    <a href="{{ route('shopping.productdetail', $similarcontentproducts_row->product_url) }}">
                                                        @if ($similarcontentproducts_row->product_image != '')
                                                            <img src="{{ asset('assets/uploads/products/') }}/{{ $similarcontentproducts_row->product_image }}" class="img-fluid" alt="">
                                                        @else
                                                            <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                        @endif
                                                    </a>
                                                </div>
                                                {{-- <div class="star-icon">
                                                <a href="javascript:void(0)" class="add_wishlist" data_item="{{ $similarcontentproducts_row->id }}">
                                                    @if (!empty($wishlist) && in_array($similarcontentproducts_row->id, array_column($wishlist, 'product_id')))
                                                        <img src="{{ asset('front_view/images/star-icon.png') }}">
                                                    @else
                                                        <img src="{{ asset('front_view/images/wishlist.png') }}">
                                                    @endif
                                                </a>
                                            </div> --}}
                                                <div class="item-head"><a href="{{ route('shopping.productdetail', $similarcontentproducts_row->product_url) }}">{{ $similarcontentproducts_row->product_name }}</a></div>
                                                <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $similarcontentproducts_row->offer_price == 0 ? number_format($similarcontentproducts_row->price, 2) : number_format($similarcontentproducts_row->offer_price, 2) }}
                                                    @if ($similarcontentproducts_row->offer_price != 0)
                                                        <div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($similarcontentproducts_row->price, 2) }}</div>
                                                    @endif
                                                </div>
                                                @if ($similarcontentproducts_row->offer_price != 0)
                                                    @php $percent = number_format((($similarcontentproducts_row->price -$similarcontentproducts_row->offer_price)*100) /$similarcontentproducts_row->price) ;@endphp
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

                                                        <a href="javascript:void(0)" class="btn add-cart-list add-cart-list_{{ $similarcontentproducts_row->id }} {{ $similarcontentproducts_row->not_for_sale == '1' || $similarcontentproducts_row->flag == '1' ? 'disable' : '' }}" id="add-cart-list_{{ $similarcontentproducts_row->id }}" value="{{ $similarcontentproducts_row->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                    </div>
                                                </div>
                                                @if ($similarcontentproducts_row->not_for_sale == '1')
                                                    <div class="not-sale">Not for online sale !</div>
                                                @elseif($similarcontentproducts_row->flag == '1')
                                                    <div class="not-sale">Sold Out !</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($similarUse->isNotEmpty())
                <div class="col-md-12 related-product">
                    <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                        <div class="listing-head d-flex justify-content-between">
                            <h2>Products use for {{ count($medicineuses_name) > 0 ? $medicineuses_name[0] : '' }}</h2>
                            <div class="view-butn"><a href="{{ url('productlisting?medicineuse%5B%5D=') . $medicineusesIds[0] . '&hid_searchCategory=0&hid_searchCategoryname=' }}" class="viewall-butn">View All</a></div>
                        </div>

                        <div class="products related-products-outer">
                            <div class="col-md-12 products-outer">
                                <div class="row">
                                    @foreach ($similarUse as $key => $similarUse_row)
                                        <div class="col-lg-2 col-sm-4 col-6 products-content-outer">
                                            <div class="products-content">
                                                <div class="product-listing">
                                                    <a href="{{ route('shopping.productdetail', $similarUse_row->product_url) }}">
                                                        @if ($similarUse_row->product_image != '')
                                                            <img src="{{ asset('assets/uploads/products/') }}/{{ $similarUse_row->product_image }}" class="img-fluid" alt="">
                                                        @else
                                                            <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                        @endif
                                                    </a>
                                                </div>
                                                {{-- <div class="star-icon">
                                                    <a href="javascript:void(0)" class="add_wishlist" data_item="{{ $similarUse_row->id }}">
                                                        @if (!empty($wishlist) && in_array($similarUse_row->id, array_column($wishlist, 'product_id')))
                                                            <img src="{{ asset('front_view/images/star-icon.png') }}">
                                                        @else
                                                            <img src="{{ asset('front_view/images/wishlist.png') }}">
                                                        @endif
                                                    </a>
                                                </div> --}}
                                                <div class="item-head"><a href="{{ route('shopping.productdetail', $similarUse_row->product_url) }}">{{ $similarUse_row->product_name }}</a></div>
                                                <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $similarUse_row->offer_price == 0 ? number_format($similarUse_row->price, 2) : number_format($similarUse_row->offer_price, 2) }}
                                                    @if ($similarUse_row->offer_price != 0)
                                                        <div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($similarUse_row->price, 2) }}</div>
                                                    @endif
                                                </div>
                                                @if ($similarUse_row->offer_price != 0)
                                                    @php $percent = number_format((($similarUse_row->price -$similarUse_row->offer_price)*100) /$similarUse_row->price) ;@endphp
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

                                                        <a href="javascript:void(0)" class="btn add-cart-list add-cart-list_{{ $similarUse_row->id }} {{ $similarUse_row->not_for_sale == '1' || $similarUse_row->flag == '1' ? 'disable' : '' }}" id="add-cart-list_{{ $similarUse_row->id }}" value="{{ $similarUse_row->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                    </div>
                                                </div>

                                                @if ($similarUse_row->not_for_sale == '1')
                                                    <div class="not-sale">Not for online sale !</div>
                                                @elseif($similarUse_row->flag == '1')
                                                    <div class="not-sale">Sold Out !</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif



            @if ($relatedProducts->isNotEmpty())
                <div class="col-md-12 related-product">
                    <div class="cat-products-outer wow fadeInUp" data-wow-delay=".20s">
                        <div class="listing-head d-flex justify-content-between">
                            <h2>Related category - {{ $product->category }}</h2>
                            <div class="view-butn"><a href="{{ route('shopping.productlisting', $product->category) }}" class="viewall-butn">View All</a></div>
                        </div>

                        <div class="products related-products-outer">
                            <div class="col-md-12 products-outer">
                                <div class="row">
                                    @foreach ($relatedProducts as $key => $relatedProducts_row)
                                        <div class="col-lg-2 col-sm-4 col-6 products-content-outer">
                                            <div class="products-content">
                                                <div class="product-listing">
                                                    <a href="{{ route('shopping.productdetail', $relatedProducts_row->product_url) }}">
                                                        @if ($relatedProducts_row->product_image != '')
                                                            <img src="{{ asset('assets/uploads/products/') }}/{{ $relatedProducts_row->product_image }}" class="img-fluid" alt="">
                                                        @else
                                                            <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                        @endif
                                                    </a>
                                                </div>
                                                {{-- <div class="star-icon">
                                                    <a href="javascript:void(0)" class="add_wishlist" data_item="{{ $relatedProducts_row->id }}">
                                                        @if (!empty($wishlist) && in_array($relatedProducts_row->id, array_column($wishlist, 'product_id')))
                                                            <img src="{{ asset('front_view/images/star-icon.png') }}">
                                                        @else
                                                            <img src="{{ asset('front_view/images/wishlist.png') }}">
                                                        @endif
                                                    </a>
                                                </div> --}}
                                                <div class="item-head"><a href="{{ route('shopping.productdetail', $relatedProducts_row->product_url) }}">{{ $relatedProducts_row->product_name }}</a></div>
                                                <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $relatedProducts_row->offer_price == 0 ? number_format($relatedProducts_row->price, 2) : number_format($relatedProducts_row->offer_price, 2) }}
                                                    @if ($relatedProducts_row->offer_price != 0)
                                                        <div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($relatedProducts_row->price, 2) }}</div>
                                                    @endif
                                                </div>
                                                @if ($relatedProducts_row->offer_price != 0)
                                                    @php $percent = number_format((($relatedProducts_row->price -$relatedProducts_row->offer_price)*100) /$relatedProducts_row->price);@endphp
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

                                                        <a href="javascript:void(0)" class="btn add-cart-list add-cart-list_{{ $relatedProducts_row->id }} {{ $relatedProducts_row->not_for_sale == '1' || $relatedProducts_row->flag == '1' ? 'disable' : '' }}" id="add-cart-list_{{ $relatedProducts_row->id }}" value="{{ $relatedProducts_row->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                    </div>
                                                </div>
                                                @if ($relatedProducts_row->not_for_sale == '1')
                                                    <div class="not-sale">Not for online sale !</div>
                                                @elseif($relatedProducts_row->flag == '1')
                                                    <div class="not-sale">Sold Out !</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('footer_scripts')
    <script>
        var url_BannerImage = '{{ asset('assets/uploads/promotionbanner') }}' + '/';
        $('#social-links').hide();
        $('.detail-img-container').imagesLoaded(function() {
            $("#exzoom").exzoom({
                autoPlay: false,
            });
            $("#exzoom").removeClass('hidden')
        });

        $(document).ready(function() {
            getPageBanners();
        });

        $(document).on('click', '#add_prescription', function(e) {
            e.preventDefault();

            @auth('user')
                $('#prescription_file').trigger("click");
            @endauth
        });

        $('#prescription_file').on('change', function() {
            formData = new FormData();
            var file = document.getElementById('prescription_file');
            var pid = {{ $product->id }};

            formData.append('product_id', pid);
            formData.append('file', file.files[0]);
            formData.append("_token", "{{ csrf_token() }}");
            $.ajax({
                type: "post",
                data: formData,
                enctype: 'multipart/form-data',
                contentType: false,
                processData: false,
                url: '{{ route('product.prescription.upload') }}',
                success: function(res) {
                    if (res.result) {
                        swal({
                            title: 'Success',
                            html: true,
                            text: res.message,
                            type: 'success',
                            // timer: 1500,
                            showCancelButton: false,
                            showConfirmButton: true
                        });
                    } else {
                        swal({
                            title: 'Failed',
                            html: true,
                            text: res.message,
                            type: 'error',
                            // timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: true
                        });
                    }
                }
            });
        });

        $(document).on('click', '#buy_now', function() {
            var productId = $(this).attr('data-id');
            var quantity = ($(this).closest('.sec_productadd').find('input[name="quant[1]"]').val() != null ? $(this).closest('.sec_productadd').find('input[name="quant[1]"]').val() : 0);
            var prescriptionId = '{{ $prescriptionId }}';
            var quantityquery = '';

            if (quantity > 0) {
                quantityquery = '&quantity=' + quantity;
            }

            if (productId != null) {
                document.location.href = '{{ url('/product/checkout?productid=') }}' + productId + quantityquery + '&checkouttype=direct_buy';
            }
        });

        $('.btn-number').on('click', function(e) {
            e.preventDefault();
            var type = $(this).attr('data-type');
            var input = $(this).closest('.input-group').find('.input-number');
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                if (type == 'minus') {
                    if (currentVal > input.attr('min')) {
                        input.val(currentVal - 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('min')) {
                        $(this).attr('disabled', true);
                    }

                } else if (type == 'plus') {
                    if (currentVal < input.attr('max')) {
                        input.val(currentVal + 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('max')) {
                        $(this).attr('disabled', true);
                    }

                }
            } else {
                input.val(0);
            }
        });

        $('.input-number').focusin(function() {
            $(this).data('oldValue', $(this).val());
        });

        $('.input-number').change(function() {
            minValue = parseInt($(this).attr('min'));
            maxValue = parseInt($(this).attr('max'));
            valueCurrent = parseInt($(this).val());

            name = $(this).attr('name');
            if (valueCurrent >= minValue) {
                $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                alert('Sorry, the minimum value was reached');
                $(this).val($(this).data('oldValue'));
            }
            if (valueCurrent <= maxValue) {
                $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                alert('Sorry, the maximum value was reached');
                $(this).val($(this).data('oldValue'));
            }
        });

        $(".input-number").keydown(function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        //-----star rating for products--
        $(document).on('click', '.star_rateproduct', function() {
            var starvalue = $(this).attr('data-star');

            $(this).addClass('star-yellow').prevAll().addClass('star-yellow');
            $(this).nextAll().removeClass('star-yellow');

            $(this).closest('.rating_product').find('.hid_productrate').val(starvalue);
        });

        //-----Add rating and review for products--
        $(document).on('click', '.rateproduct', function() {
            var productid = $(this).attr('data-id');
            var starvalue = $(this).closest('.rating_product').find('.hid_productrate').val();
            var productreview = $(this).closest('.rating_product').find('.product_review').val();

            if (productid != '') {
                if (starvalue != 0) {
                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            productid: productid,
                            starvalue: starvalue,
                            productreview: productreview
                        },
                        url: "{{ route('add.product.review') }}",
                        success: function(data) {
                            if (data.ajax_status == 'success') {
                                window.location.reload(true);
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                } else {
                    alert('Please rate star value for product.');
                }

            } else {
                alert('Invalid product choose.');
            }
        });

        function getPageBanners() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ route('page.getBanners') }}',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {

                    //--------Sidebar Slider Top1--
                    var html_sidebarSliderTop1 = '';
                    var sidebar_slTop1 = '';

                    if (response.sidebarSl_top1.Images.length >= 1) {
                        $.each(response.sidebarSl_top1.Images, function(key, element) {
                            sidebar_slTop1 += '<li data-target="#multi-item-example" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderTop1 += '<div class="carousel-item cosmetic-product-slider ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderTop1 += '<div class="left-small-slider-outer">';
                            html_sidebarSliderTop1 += '<a href="' + (element.banner_url != null ? element.banner_url : '') + '"><img src="' + url_BannerImage + element.image + '" class="img-fluid mx-auto d-block" alt="Slider ' + (key + 1) + '"></a>';
                            html_sidebarSliderTop1 += '</div>';
                            html_sidebarSliderTop1 += '</div>';
                        });

                        $('#sidebar_sliderTop1').find('.carousel-indicators').html(sidebar_slTop1);
                        $('#sidebar_sliderTop1').find('.carousel-inner').html(html_sidebarSliderTop1);
                    }

                    //--------Sidebar Slider Bottom2--
                    var html_sidebarSliderBottom2 = '';
                    var sidebar_slBottom2 = '';

                    if (response.sidebarSl_bottom2.Images.length >= 1) {
                        $.each(response.sidebarSl_bottom2.Images, function(key, element) {
                            sidebar_slBottom2 += '<li data-target="#multi-item-example-3" data-slide-to="' + key + '" class="' + (key == 0 ? 'active' : '') + '"></li>';

                            html_sidebarSliderBottom2 += '<div class="carousel-item ayurvedicc-product-slider ' + (key == 0 ? 'active' : '') + '">';
                            html_sidebarSliderBottom2 += '<div class="left-small-slider-outer">';
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
        $(document).on('click', '#share-btn', function(e) {
         $('#social-links').toggle();
        });
    </script>
@endsection
