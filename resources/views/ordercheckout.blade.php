@extends('layouts.frontview.app')
@section('content')
    @include('layouts.frontview.topmenubar_frontview')
    @php
    if (!isset($_SESSION)) {
        session_start();
    }
    unset($_SESSION['product_prescriptions']);
    @endphp

    @php
    $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item'));
    $shipping_charge_key = array_search('shipping_charge', array_column($common_settings, 'item'));
    $shipping_charge_limit_key = array_search('shipping_charge_limit', array_column($common_settings, 'item'));

    @endphp
    <div class="container container-m">
        <div class="col-md-12 py-5">
            <div class="row">
                <div class="col-md-8">


                    <div id="nextpageLoader" class="row">

                        <div class="col-md-12 pt-3">
                            <p class="bg-cyan p-11 font-weight-bold text-white text-center text-uppercase f-20 gray mb-0"> Payment Options </p>
                            <div class="col-md-12">
                                <div class="w-100" id="myDIV">
                                    <div class="row payment">
                                        <div class="col-md-3 border pt-5 pb-3 tn active" id="profile" tabindex="1">
                                            <img src="{{ asset('front_view/images/bank.png') }}" class="img-fluid mx-auto d-block">
                                            <p class="text-center pt-2 text-uppercase">Bank Transfer </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="2">
                                            <img src="{{ asset('front_view/images/card.png') }}" class="img-fluid mx-auto d-block">
                                            <p class="text-center pt-2 text-uppercase">Card Payment </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="3">
                                            <img src="{{ asset('front_view/images/upi.png') }}" class="img-fluid mx-auto d-block">
                                            <p class="text-center pt-2 text-uppercase"> UPI Payments </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="4">
                                            <img src="{{ asset('front_view/images/cashon.png') }}" class="img-fluid mx-auto d-block">
                                            <p class="text-center pt-2 text-uppercase">Cash on Delivery </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @if ($product->prescription == 1 && $enablePrescription === true && $allowPurchase === false)

                            @endif --}}


                        @php $count=0; @endphp
                        {{-- @if ($checkout_type == 'Cart')
                            @if (!Session::has('session_data')) --}}

                        @if (!empty($order_details))
                            @foreach ($order_details as $order_row)
                                {{-- {{ $order_row }} --}}
                                @if (isset($order_row->prescription) && !empty($order_row->prescription))
                                    @if ($order_row->prescription == '1')
                                        @php $count=$count+1; @endphp
                                        <div class="col-md-6 priscription">

                                            <label style="color: red">Prescription needed for : {{ $order_row->product_name }}</label>
                                            <button class="btn add_prescription" id='add_prescription_{{ $order_row->product_id }}' data-id='{{ $order_row->product_id }}'><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                            <input type="file" id="prescription_file_{{ $order_row->product_id }}" class="prescription_file" data-item='{{ $order_row->product_id }}' style="width:0;height:0">
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                        {{-- @endif
                        @endif --}}

                        {{-- @if ($checkout_type == 'BuyNow')

                            @if (!empty($BuyNow['ProductDetails']))


                                {{-- {{$carts_row}} --}}
                        {{-- @if (isset($BuyNow['ProductDetails']->prescription) && !empty($BuyNow['ProductDetails']->prescription))
                                    @if ($BuyNow['ProductDetails']->prescription == '1')
                                        @php $count=$count+1; @endphp
                                        <div class="col-md-6 priscription">
                                            <label style="color: red">Prescription needed for : {{ $BuyNow['ProductDetails']->product_name }}</label>
                                            <button class="btn add_prescription" id='add_prescription_{{ $BuyNow['ProductDetails']->product_id }}' data-id='{{ $BuyNow['ProductDetails']->product_id }}'><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                            <input type="file" id="prescription_file_{{ $BuyNow['ProductDetails']->product_id }}" class="prescription_file" data-item='{{ $BuyNow['ProductDetails']->product_id }}' style="width:0;height:0">
                                        </div>
                                    @endif
                                @endif --}}

                        {{-- @endif
                        @endif --}}



                        <div class="col-md-12 pt-3" id="finalSubmission">
                            <div class="col-md-12">
                                <div class="w-100" id="myDIV">
                                    <div class="row payment">
                                        <div class="form-check agree-terms">
                                            <input class="form-check-input" type="checkbox" id="termsCondition">
                                            <label class="form-check-label" for="termsCondition">I agree the <a href="{{ route('view.contentpage', 'terms-conditions') }}">Terms and Conditions.</a></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="myElem" class="alert alert-danger" style="display: none"></div>
                </div>
                @php
                    $cart_ids = '';
                    $grand_total = $taxAmt = 0;
                @endphp
                {{-- @if ($checkout_type == 'Cart') --}}
                {{-- {{dd($carts)}} --}}



                <div class="col-md-4" id="priceDetails">
                    {{-- <div class="shadow cart-right p-27 border bg-light-gray-1 pb-45">
                        <div class="price-details-outer">
                            <h5>Apply Coupon</h5>
                            <div class="coupon-block">
                                <input type="text" id="couponCode" placeholder="Coupon Code" class="col-sm-6 form-control border border-secondary" />
                                <button id="btn_applycoupon" class="btn btn-dark text-uppercase font-weight-bold" type="button">Apply</button>
                            </div>
                            <div id="msg_coupen" class="text-danger"></div>
                        </div>
                    </div> --}}

                    <div class="right-cart-page">
                        <div class="main-cart-total">
                            <h5>Price Details</h5>
                            <div class="cart-toatal-amount"><span>Sub Total</span>
                                <h6 id="checkout_subtotal"> {!! $common_settings[$currency_key]['value'] !!}{{ number_format($order->grand_total, 2) }}</h6>
                            </div>
                             @php
                                $pre_grand_total = $grand_total;
                                if ($grand_total < $common_settings[$shipping_charge_limit_key]['value']) {
                                    $grand_total = $pre_grand_total + $common_settings[$shipping_charge_key]['value'];
                                }

                            @endphp
                            <div class="cart-toatal-amount"><span>Shipping Fee</span>
                                <h6>
                                    @if ($pre_grand_total < $common_settings[$shipping_charge_limit_key]['value'] && $common_settings[$shipping_charge_key]['value'] > 0)
                                        {!! $common_settings[$currency_key]['value'] !!}{{ $common_settings[$shipping_charge_key]['value'] }}
                                    @else
                                        Free
                                    @endif
                                </h6>
                            </div>
                            <div class="cart-toatal-amount"><span>Total</span>
                                <h6 id="checkout_totalamt">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($order->grand_total, 2) }}</h6>
                                <input type="hidden" id="hid_totalamount" value="{{ $order->grand_total }}">
                                <input type="hidden" id="hid_grandtotal" value="{{ $order->grand_total }}">
                            </div>

                            @php $key = array_search('company_logo', array_column($common_settings, 'item')); @endphp
                            <input type="hidden" id='company_logo_image' value="{{ $common_settings[$key]['value'] }}">

                            <div class="d-flex justify-content-center checkout-btn" id='chk-id'>
                                <a href="javascript:void(0)" id="checkoutButtonToPay" {{ $order->status == 'initiated' ? '' : 'disabled' }}>Place Order</a>
                            </div>
                            {{-- <a href="javascript:void(0)" id="checkoutButton" style="display: none">Place Order</a> --}}


                            {{-- <div id="hid_prescription"></div> --}}
                            <div></div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!---container--->
@endsection


@section('footer_scripts')
    <script>
        var i = 0;


        $(document).on('click', '#checkoutButtonToPay', function(e) {
            var counts = {{ $count }}
            var elm = $(this).parent();
            var status = "{{ $order->status }}";

            if (status != 'initiated') {
                $("#myElem").html('Payment already Completed').show().delay(2000).fadeOut();
            } else {

                if (counts != i) {
                    $("#myElem").html('Please update prescription file for the medicines which need prescription.').show().delay(4000).fadeOut();
                } else if ($('#termsCondition').prop("checked") == false) {
                    $("#myElem").html('Please agree Terms and Conditions').show().delay(2000).fadeOut();
                } else {

                    $('#chk-id').html('<a href="javascript:void(0)"  id="checkoutButton">Pay Now</a>');
                }
            }
        });


        $(document).on('click', '#checkoutButton', function(e) {
            var counts = {{ $count }};
            var elm = $(this).parent();


            if (counts != i) {
                $("#myElem").html('Please update prescription file for the medicines which need prescription.').show().delay(4000).fadeOut();
            } else if ($('#termsCondition').prop("checked") == false) {
                $("#myElem").html('Please agree Terms and Conditions').show().delay(2000).fadeOut();
            } else {
                elm.next().html(loader_gif + '<div>Please wait till check out complete.</div>');


                var address_type = $("#delivery_address").val();

                var addressId = $("#delivery_address").attr('data-id');
                var company_logo = $("#company_logo_image").val();
                var total_amount = $('#hid_totalamount').val();
                var grandtotal = $('#hid_grandtotal').val();
                var productId = quantity = prescriptionID = 0;

                $.ajax({
                    type: "POST",
                    url: "{{ route('order.payment') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "order_id": "{{ $order->id }}",
                        // 'prescription_ids': prescription_ids,
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.success) {
                            elm.next().html("");

                            var flow_config = {
                                merchantId: "{{ Config::get('constants.payment_constants.merchant_id') }}",
                                bdOrderId: response.jwtPayload.bdorderid,
                                authToken: response.jwtPayload.links[1].headers.authorization,
                                childWindow: true,
                                returnUrl: "{{ route('payment.return', 'order_checkout') }}",
                            }

                            var responseHandler = function(txn) {
                                if (txn.response) {
                                    alert("callback received status:: ", txn.status);
                                    alert("callback received response:: ", txn.response);
                                }
                            };

                            var config = {
                                merchantLogo: logo_image_path + '/' + company_logo,
                                flowConfig: flow_config,
                                flowType: "payments"
                            }

                            window.loadBillDeskSdk(config);

                            // window.location.href = "{{ URL::to('order/invoice') }}/" + response.order.id;
                        } else {
                            alert(response.errorMsg);
                            if (response.checkout_type == "BuyNow") {
                                var redirect = '{{ url()->previous() }}'.replace('&amp;', '&');
                                window.location.href = redirect;
                            }
                            // if (response.checkout_type == "Cart") {
                            //     window.location.href = "{{ URL::to('/products/cart') }}";
                            // } else {
                            //     window.history.back();
                            // }
                        }
                    },
                });
            }
        });
    </script>
@endsection
