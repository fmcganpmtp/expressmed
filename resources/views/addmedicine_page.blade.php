@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home" aria-hidden="true"></i></a></li>
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('generalprescription.create') }}">Upload prescription</a></li>

                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('prescription.addmedicine') }}">Add Medicine</a></li>

            </ol>
        </nav>
    </div>

    <div class="width-container main-all-med">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 add-med-outer">
                <div class="listing-page-head d-flex justify-content-between">
                    <h6>Add Medicines</h6>
                </div>
                @if (isset($error))
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endif
                <div class="add-med-outer">
                    <div class="add-med-cont">

                        {{-- <form action="{{ route('list.allproductlisting') }}" method="GET"> --}}
                        <div class="header-search">
                            <input type="text" class="header-search-a flex-grow-1" id="search_medicine_keyword" name="search_medicine_keyword" value="{{ isset($_GET['search_keyword']) && $_GET['search_keyword'] != '' ? $_GET['search_keyword'] : '' }}" placeholder="Search Products" autocomplete="off" />

                            <input type="hidden" id="userID" name="userID" value="" />
                            @foreach ($commonCategories as $row)
                                @if ($row['name'] == 'All Medicines')
                                    <input type="hidden" name="hid_searchCategory" id="all_med_category" value="{{ $row->id }}">
                                @endif
                            @endforeach


                            <input type="hidden" name="hid_searchCategoryname" id="hid_searchCategoryname" value="{{ 'All Medicines' }}">

                            @if (!empty($_GET['productbrand']))
                                @foreach ($_GET['productbrand'] as $brandId)
                                    <input type="hidden" name="productbrand[]" value="{{ $brandId }}">
                                @endforeach
                            @endif

                            @if (!empty($_GET['producttype']))
                                @foreach ($_GET['producttype'] as $producttype)
                                    <input type="hidden" name="producttype[]" value="{{ $producttype }}">
                                @endforeach
                            @endif

                            @if (!empty($_GET['medicineuse']))
                                @foreach ($_GET['medicineuse'] as $medicineuse)
                                    <input type="hidden" name="medicineuse[]" value="{{ $medicineuse }}">
                                @endforeach
                            @endif

                            <button type="submit" class="btn search-button" name="search_submit" value="search"><i class="fas fa-close" id="remove-text"></i></button>

                        </div>
                        <div id="sear" class="med-add" style="background-color: #ffffff;"></div>

                        {{-- </form> --}}
                    </div>

                </div>

            </div>
            <div class="col-md-12 checkout-btn add-med-btn pb-3"><a class="btn btn-green" href="{{ route('product.checkout') }}" style="text-decoration:none"><i class="fas fa-credit-card"></i> Checkout</a></div>
        </div>

    </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('.alert-danger').delay(3000).fadeOut();
        })
        $(document).on('click','#remove-text',function(e){
            $('#search_medicine_keyword').val('');
            $('#sear').html('');

        });
    </script>
@endsection
