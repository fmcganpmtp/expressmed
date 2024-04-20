@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container main-product-detail-page">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Page not found</h3>
                @if ($errors)
                    <h5 class="text-center text-danger">{{ $errors->all()[0] }}</h5>
                @endif
            </div>
        </div>
    </div>

@endsection
