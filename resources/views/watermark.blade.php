@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->



    <div class="width-container main-product-detail-page">
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12 product-detail-page-outer">
                <div class="listing-page-head d-flex justify-content-between">

                </div>

                @if (session('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ session('success') }}</li>
                    </ul>
                </div>
            @endif
            {{ 'Showing ' .(($products_images->currentpage() - 1) * $products_images->perpage() + 1) .' - ' .(($products_images->currentpage() - 1) * $products_images->perpage() + count($products_images)) .' products images of ' .$products_images->total() }} products images

                @if (isset($success))
                <div class="alert alert-success">
                   {{(($products_images->currentpage() - 1) * $products_images->perpage() + 1) .' - ' .(($products_images->currentpage() - 1) * $products_images->perpage() + count($products_images))}} {{ $success }}
                    </div>
                @endif
                <div class="col-lg-10 col-md-10 col-sm-12 pt-4">


                    @if (isset($products_images))
                        @foreach ($products_images as $row)

                        @endforeach
                       {{ $products_images->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('.alert-success').delay(5000).fadeOut();
        })
    </script>
@endsection
