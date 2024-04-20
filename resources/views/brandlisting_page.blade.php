@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('list.all-brands') }}">All Brands</a></li>

            </ol>
        </nav>
    </div>

    <div class="width-container">
        <div class="row">


            <div class="col-md-12 product-listing-page-outer">


                <div class="listing-page-head d-flex justify-content-between">
                    <h2>{{  'All Brands' }}</h2>
                </div>
                {{-- <div class="col-md-12 products-outer">
                        <div class="row products-alpha">
                            @php
                            if(request()->has('label'))
                            {
                            $label_alpha=request()->get('label');
                            }
                            else{
                            $label_alpha='A';
                             }
                            @endphp

                @foreach($alphas as $key =>$alpha)
                <div class="products-content-alpha">

                <a class="{{($label_alpha == $alpha)?'text-danger active ':''}}" href="{{ route('list.all-medicines', ['label'=>$alpha]) }}" id="tags">{{$alpha}}</a>

                </div>
                @endforeach
               </div>
            </div> --}}
                <div class="products">
                    <div class="col-md-12 products-outer">
                        <div class="row">
                            @if ($product_brands->isNotEmpty())
                            @foreach ($product_brands as $key => $value)
                            <div class="col-lg-2 col-sm-4 col-6 products-content-outer brand-prdct-outer">
                                <div class="products-content pb-0">
                                    <div class="product-listing">
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
                        </div>
                    </div>
                        </div>
                        @endforeach
                                {{ $product_brands->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                            @else
                                <div class="col-lg-12 col-sm-12 col-12 products-content">
                                    <div class="text-center text-danger">Sorry... Brands not found.</div>
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

@endsection
