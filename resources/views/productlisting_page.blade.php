@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('list.allproductlisting') }}">Product Listings</a></li>
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
        <div class="list_pr_full_product">
            <div class="row">
                <div class="col-md-12 filter-click-out"><i id="filter-click" class="fa fa-filter" aria-hidden="true"></i></div>
                <div class="col-md-3 lft-listing-outer panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
                    <form action="" method="get" id="form_productfilter">
                        @if (empty($selectCategories) || (!empty($selectCategories) && !in_array($selectCategories->id, $all_med_child_categoryIds)) || (request()->has('hid_searchCategoryname') && request()->hid_searchCategoryname != 'All Medicines'))
                            @if ($all_brands->isNotEmpty())
                                <div class="filter-sec">
                                    <div class="filter-head d-flex justify-content-between">
                                        <h6>Brand</h6>
                                        <div class="filter-head-icon" data-toggle="collapse" data-parent="#accordion" data-target="#collapseBrand" aria-expanded="true" aria-controls="collapseBrand"><i class="fas {{ request()->has('productbrand')? 'fa-chevron-up': (empty(request()->productcategory) && empty(request()->producttype) && empty(request()->medicineuse)? 'fa-chevron-up': 'fa-chevron-down') }} filter_collapse"></i></div>
                                    </div>
                                    <div class="product-filter panel-collapse collapse {{ request()->has('productbrand')? 'show': (empty(request()->productcategory) && empty(request()->producttype) && empty(request()->medicineuse)? 'show': '') }}" id="collapseBrand" style="max-height: 450px; overflow-y: scroll;">
                                        @foreach ($all_brands as $key => $all_brands_row)
                                            <div class="listing-product">
                                                <input type="checkbox" class="productbrand" id="{{ 'brand_' . $all_brands_row->id }}" name="productbrand[]" value="{{ $all_brands_row->id }}" {{ isset($_GET['productbrand']) && in_array($all_brands_row->id, $_GET['productbrand']) ? 'checked' : '' }}>
                                                <label for="{{ 'brand_' . $all_brands_row->id }}" class="pl-1" title="{{ $all_brands_row->name }}"> {{ $all_brands_row->name }}</label><br>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                        {{-- Str::limit(ucfirst($all_brands_row->name), 28, $end = '..') --}}
                        @if ($all_categories->isNotEmpty())
                            <div class="filter-sec">
                                <div class="filter-head d-flex justify-content-between">
                                    <h6>Type</h6>
                                    <div class="filter-head-icon" data-toggle="collapse" data-parent="#accordion" data-target="#collapseCategory" aria-expanded="true" aria-controls="collapseCategory"><i class="fas {{ isset($_GET['productcategory']) && !empty($_GET['productcategory']) ? 'fa-chevron-down' : 'fa-chevron-up' }} filter_collapse"></i></div>
                                </div>
                                <div class="product-filter panel-collapse collapse {{ isset($_GET['productcategory']) && !empty($_GET['productcategory'])? 'show': (empty(request()->productcategory) && empty(request()->medicineuse)? 'show': '') }}" id="collapseCategory" style="max-height: 450px; overflow-y: scroll;">
                                    @foreach ($all_categories as $key => $all_categories_row)
                                        <div class="listing-product">
                                            <input type="checkbox" class="productcategory" id="{{ 'category_' . $all_categories_row->id }}" name="productcategory[]" value="{{ $all_categories_row->id }}" {{ isset($_GET['productcategory']) && in_array($all_categories_row->id, $_GET['productcategory'])? 'checked': '' }}>
                                            <label for="{{ 'category_' . $all_categories_row->id }}" class="pl-1" title="{{ $all_categories_row->name }}"> {{ $all_categories_row->name }}</label><br>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        {{-- @if ($all_producttypes->isNotEmpty())
                            <div class="filter-sec">
                                <div class="filter-head d-flex justify-content-between">
                                    <h6>Type</h6>
                                    <div class="filter-head-icon" data-toggle="collapse" data-parent="#accordion" data-target="#collapseType" aria-expanded="true" aria-controls="collapseType"><i class="fas {{ isset($_GET['producttype']) && !empty($_GET['producttype']) ? 'fa-chevron-up' : 'fa-chevron-down' }} filter_collapse"></i></div>
                                </div>
                                <div class="product-filter panel-collapse collapse {{ isset($_GET['producttype']) && !empty($_GET['producttype']) ? 'show' : '' }}" id="collapseType" style="max-height: 450px; overflow-y: scroll;">
                                    @foreach ($all_producttypes as $key => $all_producttypes_row)
                                        <div class="listing-product">
                                            <input type="checkbox" class="producttype" id="{{ 'producttype_' . $all_producttypes_row->id }}" name="producttype[]" value="{{ $all_producttypes_row->id }}" {{ isset($_GET['producttype']) && in_array($all_producttypes_row->id, $_GET['producttype']) ? 'checked' : '' }}>
                                            <label for="{{ 'producttype_' . $all_producttypes_row->id }}" class="pl-1" title="{{ $all_producttypes_row->producttype }}"> {{ Str::limit($all_producttypes_row->producttype, 28, $end = '..') }}</label><br>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif --}}

                        @if ($all_medicineuse->isNotEmpty())
                            <div class="filter-sec">
                                <div class="filter-head d-flex justify-content-between">
                                    <h6>Use</h6>
                                    <div class="filter-head-icon" data-toggle="collapse" data-parent="#accordion" data-target="#collapseUse" aria-expanded="true" aria-controls="collapseUse"><i class="fas {{ isset($_GET['medicineuse']) && !empty($_GET['medicineuse']) ? 'fa-chevron-up' : 'fa-chevron-down' }} filter_collapse"></i></div>
                                </div>
                                <div class="product-filter panel-collapse collapse {{ isset($_GET['medicineuse']) && !empty($_GET['medicineuse']) ? 'show' : '' }}" id="collapseUse" style="max-height: 450px; overflow-y: scroll;">
                                    @foreach ($all_medicineuse as $key => $all_medicineuse_row)
                                        <div class="listing-product">
                                            <input type="checkbox" class="medicineuse" id="{{ 'medicineuse_' . $all_medicineuse_row->id }}" name="medicineuse[]" value="{{ $all_medicineuse_row->id }}" {{ isset($_GET['medicineuse']) && in_array($all_medicineuse_row->id, $_GET['medicineuse']) ? 'checked' : '' }}>
                                            <label for="{{ 'medicineuse_' . $all_medicineuse_row->id }}" class="pl-1" title="{{ $all_medicineuse_row->name }}"> {{ $all_medicineuse_row->name }}</label><br>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <input type="hidden" name="manufact_" id="manufact_filter" value="{{ request()->has('manufact_') && request()->manufact_ != '' ? request()->manufact_ : '' }}">
                        <input type="hidden" name="hid_searchCategory" value="{{ isset($_GET['hid_searchCategory']) && $_GET['hid_searchCategory'] != 0 ? $_GET['hid_searchCategory'] : 0 }}">
                        <input type="hidden" name="hid_searchCategoryname" value="{{ isset($_GET['hid_searchCategoryname']) && $_GET['hid_searchCategoryname'] != ''? $_GET['hid_searchCategoryname']: '' }}">
                    </form>
                </div>

                <div class="col-md-9 product-listing-page-outer">
                    @if (request()->filled('productbrand') || request()->filled('productcategory') || request()->filled('producttype') || request()->filled('medicineuse') || request()->filled('manufact_'))
                        <div class="category-check-list">
                            @if (request()->has('productbrand'))
                                @foreach ($all_brands as $brandval)
                                    @if (in_array($brandval->id, request('productbrand')))
                                        <button type="button" class="btn btn-sm btn-default" data-id="brand_{{ $brandval->id }}">{{ $brandval->name }} <i class="fas fa-times deselect_filter"></i></button>
                                    @endif
                                @endforeach
                            @endif
                            @if (request()->has('productcategory'))
                                @foreach ($all_categories as $categoriesval)
                                    @if (in_array($categoriesval->id, request('productcategory')))
                                        <button type="button" class="btn btn-sm btn-default" data-id="category_{{ $categoriesval->id }}">{{ $categoriesval->name }} <i class="fas fa-times deselect_filter"></i></button>
                                    @endif
                                @endforeach
                            @endif
                            @if (request()->has('producttype'))
                                @foreach ($all_producttypes as $producttypesval)
                                    @if (in_array($producttypesval->id, request('producttype')))
                                        <button type="button" class="btn btn-sm btn-default" data-id="producttype_{{ $producttypesval->id }}">{{ $producttypesval->producttype }} <i class="fas fa-times deselect_filter"></i></button>
                                    @endif
                                @endforeach
                            @endif
                            @if (request()->has('medicineuse'))
                                @foreach ($all_medicineuse as $medicineuseval)
                                    @if (in_array($medicineuseval->id, request('medicineuse')))
                                        <button type="button" class="btn btn-sm btn-default" data-id="medicineuse_{{ $medicineuseval->id }}">{{ $medicineuseval->name }} <i class="fas fa-times deselect_filter"></i></button>
                                    @endif
                                @endforeach
                            @endif
                            @if (request()->has('manufact_') && request()->manufact_ != '')
                                <button type="button" class="btn btn-sm btn-default" data-id="manufacture_btn">{{ request()->manufact_ }} <i class="fas fa-times deselect_filter"></i></button>
                            @endif
                            <span><a href="javascript:void(0)" class="clear_filter" style="color: #74a324"><small>Clear Filters</small></a></span>
                        </div>
                    @endif

                    <div class="listing-page-head d-flex justify-content-between">
                        <h2>{{ isset(request()->route()->parameters['categoryname'])? request()->route()->parameters['categoryname']: 'Product Listing' }}</h2>
                        @if (count($products) > 0)
                            {{ 'Showing ' .(($products->currentpage() - 1) * $products->perpage() + 1) .' - ' .(($products->currentpage() - 1) * $products->perpage() + count($products)) .' products of ' .$products->total() }} products
                        @endif
                    </div>
                    @php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                    <div class="products">
                        <div class="col-md-12 products-outer">
                            <div class="row">
                                @if ($products->isNotEmpty())
                                    @foreach ($products as $products_row)
                                        <div class="col-lg-3 col-sm-4 col-6 products-content-outer">
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
                                                {{-- <div class="star-icon">
                                                <a href="javascript:void(0)" class="add_wishlist" data_item="{{ $products_row->id }}">
                                                    @if (!empty($wishlist) && in_array($products_row->id, array_column($wishlist, 'product_id')))
                                                        <img src="{{ asset('front_view/images/star-icon.png') }}">
                                                    @else
                                                        <img src="{{ asset('front_view/images/wishlist.png') }}">
                                                    @endif
                                                </a>
                                            </div> --}}
                                                <div class="item-head"><a href="{{ route('shopping.productdetail',$products_row->product_url) }}">{{ $products_row->product_name }}</a></div>
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
                                    {{ $products->links('pagination::bootstrap-4') }}
                                @else
                                    <div class="col-lg-12 col-sm-12 col-12 products-content">
                                        <div class="text-center text-danger">Sorry... Products not found in this category.</div>
                                    </div>
                                @endif

                            </div>
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
        $(document).ready(function() {
            $('.alert-success').delay(3000).fadeOut();

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
        $('#filter-click').on('click', function() {
            $('#accordion').toggle();
        });
    </script>
@endsection
