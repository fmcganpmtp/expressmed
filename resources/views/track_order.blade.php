@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home" aria-hidden="true"></i></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('track.order') }}">TrackOrder</a></li>

            </ol>
        </nav>
    </div>

    <div class="width-container main-product-detail-page track_order">

        <div class="product-detail-page-outer">
            <div class="listing-page-head d-flex justify-content-between">
                <h6>Track order</h6>
            </div>
            <div class="col-md-12 error-track">
                <div class="row">
                    @if (isset($error))
                        <div class="alert alert-danger mt-3">
                            {{ $error }}
                        </div>
                    @endif
                </div>
                <div class="pt-4 teck_submit">
                    <form method="get">
                        <div class="form-group row">
                            <div class="form-group mr-3">
                                <input type='text' class="form-control" placeholder="Enter Order ID" name="orderID" value="{{ request()->get('orderID') }}">
                            </div>
                            <div class="form-group">
                                <button type='submit' class="btn btn-primary">Track Order</button>
                            </div>
                        </div>
                    </form>


                    @if (isset($orders))
                        <div class="track_order_dtls">
                            @if ($orders->delivery_type == 'direct')
                                @if (isset($track_details))
                                    @foreach ($track_details as $row)
                                        <ul>
                                            <li>{{ $row['track_status'] }}</li>
                                            <li>{{ $row['error'] }}</li>

                                        </ul>
                                    @endforeach
                                @endif
                            @elseif($orders->delivery_type == 'pickup')
                                @if ($store_data)
                                    <p>{{ $store_data->name }}</p>
                                    <p>{{ $store_data->address }}</p>
                                    <p>{{ $store_data->location }}</p>
                                    <p>{{ $store_data->contact_number }}</p>
                                    <div class="track_map">{!! $store_data->map_location_code !!}</div>
                                @endif
                            @endif
                        </div>

                    @endif
                </div>
                {{-- </div> --}}
            </div>
        </div>

    </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('.alert-danger').delay(3000).fadeOut();
        })
    </script>
@endsection
