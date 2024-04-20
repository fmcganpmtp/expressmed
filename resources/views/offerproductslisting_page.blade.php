@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('list.offerproducts') }}">Deals</a></li>
                @if (!empty($selectCategories))
                    @if ($selectCategories->getParentsNames() !== $selectCategories->name)
                        @foreach ($selectCategories->getParentsNames()->reverse() as $item)
                            @if ($item->parent_id == 0)
                                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('shopping.productlisting', $item->name) }}">{{ $item->name }}</a></li>
                            @else
                                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('shopping.productlisting', $item->name) }}">{{ $item->name }}</a></li>
                            @endif
                        @endforeach
                    @endif
                    <li class="breadcrumb-item inner-breadcrumb-dtls active">{{ $selectCategories->name }}</li>
                @endif
            </ol>
        </nav>
    </div>

    <div class="width-container">
        <div class="row">


            <div class="col-md-12 product-listing-page-outer">


                <div class="listing-page-head d-flex justify-content-between">
                    <h2>{{ isset(request()->route()->parameters['categoryname'])? request()->route()->parameters['categoryname']: 'Offer products' }}</h2>
                    @if (count($products) > 0)
                        {{ 'Showing ' .(($products->currentPage() - 1) * count($products) + 1) .' - ' .count($products) * $products->currentPage() .' products of ' .$products->total() }} products
                    @endif
                </div>
                {{-- <form method="" id="sort_form">
                    <div class="p-2 bd-highlight">
                        <div class="dropdown inner-sort w-100">
                            <select name="sort" id="sort_deal_products" class="banner-select inner-banner-select form-fluid">
                                <option value="">Sort By Price </option>
                                <option value="low-to-high" @if (isset($_GET['sort']) && $_GET['sort'] == 'low-to-high') selected @endif>Low - High</option>
                                <option value="high-to-low" @if (isset($_GET['sort']) && $_GET['sort'] == 'high-to-low') selected @endif>High - Low</option>
                            </select>
                        </div>
                    </div>
                </form> --}}

                <form method="" id="sort_form">
                    <div id="tabs" class="sort-offer">
                        <div class="col-md-12 sort-pro">
                            <h6>Sort by :</h6>
                            <nav class="description-tab">
                                <div class="nav nav-tabs nav-fill description-tab-contnt">
                                    <a href="{{ url('/list/offer-products?sort=low-to-high') }}" class="nav-item nav-link @if (isset($_GET['sort']) && $_GET['sort'] == 'low-to-high') active @endif " >Price--Low to High</a>
                                    <a href="{{ url('/list/offer-products?sort=high-to-low') }}" class="nav-item nav-link @if (isset($_GET['sort']) && $_GET['sort'] == 'high-to-low') active @endif" >Price--High to Low</a>
                                    {{-- <a class="nav-item nav-link" value="high-to-low" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><a href="{{ url('/list/offer-products?sort=high-to-low') }}">Price--High to Low</a></a> --}}
                                </div>
                            </nav>
                        </div>

                    </div>
                </form>





                @php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                <div class="products">
                    <div class="col-md-12 products-outer">
                        <div class="row">
                            @if ($products->isNotEmpty())
                                @foreach ($products as $products_row)
                                    <div class="col-lg-2 col-sm-4 col-6 products-content-outer">
                                        <div class="products-content">
                                            <div class="product-listing">
                                                <a href="{{ route('shopping.productdetail', $products_row->product_url) }}">
                                                    @if ($products_row->product_image != '')
                                                        <img src="{{ asset('assets/uploads/products/') . '/' . $products_row->product_image }}" class="img-fluid" alt="">
                                                    @else
                                                        <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                    @endif
                                                </a>
                                            </div>


                                            <div class="item-head"><a href="{{ route('shopping.productdetail', $products_row->product_url) }}">{{ $products_row->product_name }}</a></div>
                                            <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $products_row->offer_price == 0? number_format($products_row->price, 2): number_format($products_row->offer_price, 2) }}
                                                @if ($products_row->offer_price != 0)
                                                    <div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($products_row->price, 2) }}</div>
                                                @endif
                                            </div>
                                            @if ($products_row->offer_price != 0)
                                                @php $percent = number_format((($products_row->price -$products_row->offer_price)*100) /$products_row->price) ;@endphp
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

                                                    <a href="javascript:void(0)" class="btn {{ $products_row->not_for_sale == '1' || $products_row->flag == '1' ? "disable" : "add-cart-list add-cart-list_$products_row->id" }}" id="add-cart-list_{{ $products_row->id }}" value="{{ $products_row->id }}"><i class="fas fa-shopping-cart"></i>Add</a>
                                                </div>
                                            </div>
                                            @if ($products_row->not_for_sale == '1')
                                                <div class="not-sale">Not for online sale !</div>
                                            @elseif($products_row->flag == '1')
                                                <div class="not-sale">Sold Out !</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                            @else
                                <div class="col-lg-12 col-sm-12 col-12 products-content">
                                    <div class="text-center text-danger">Sorry... Products not found.</div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--row-->
    </div>
    <!--container-->

@endsection

@section('footer_scripts')
    <script>
        $('.productbrand, .productcategory, .producttype, .medicineuse').on('click', function() {
            $('#form_productfilter').submit();
        });

        $('.filter_collapse').on('click', function(event) {
            $(this).toggleClass("fa-chevron-up fa-chevron-down");
        });

        $('.deselect_filter').on('click', function() {
            var checkbox_elm = $(this).closest('button').attr('data-id');

            if (checkbox_elm == 'manufacture_btn') {
                $('#manufact_filter').val('');
            } else {
                $('#form_productfilter').find('#' + checkbox_elm).attr('checked', false);
            }

            $('#form_productfilter').submit();
        });

        $('.clear_filter').on('click', function() {
            $('#form_productfilter').find('input[type=checkbox]').attr('checked', false);
            $('#form_productfilter').submit();
        });

        $('#sort_deal_products').on('change', function(e) {
            alert("hi")

            $('#sort_form').submit();

        });
    </script>
@endsection
