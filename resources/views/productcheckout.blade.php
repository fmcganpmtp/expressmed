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
        $shipping_charge_149_key = array_search('shipping_charge_149', array_column($common_settings, 'item'));
        $shipping_charge_499_key = array_search('shipping_charge_499', array_column($common_settings, 'item'));

    @endphp
    <div class="container container-m">
        <div class="col-md-12 py-5">
            <div class="row">
                <div class="col-md-8">
                    <div id="deliveryaddressModule">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="bg-cyan p-11 font-weight-bold text-white text-center text-uppercase f-20 gray mb-0"> Delivery Address </p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="register-form">
                                    <div class="form">
                                        <div class="form-content">
                                            <form id="checkout-form" method="POST">
                                                @csrf
                                                <input type="hidden" name="checkouttype" value="{{ isset($_GET['checkouttype']) && $_GET['checkouttype'] != '' ? 'direct_buy' : 'cart' }}">
                                                <input type="hidden" name="product_id" value="{{ isset($_GET['product_id']) && $_GET['product_id'] != '' ? $_GET['product_id'] : 0 }}">
                                                <div class="row">
                                                    <div class="col-md-6 checkout-input choose-addresstype">
                                                        <p class="f-18 pl-21 font-weight-bold mb-2">Choose your address</p>
                                                        @php $default = array('primary', 'home', 'work') @endphp
                                                        @foreach ($default as $row)
                                                            <a href="javascript:void(0)" id="{{ $row }}" class="btn btn-success btn-sm address-choose">{{ ucfirst($row) }}</a>
                                                        @endforeach
                                                    </div>
                                                    <input type="hidden" name="delivery_address" id="delivery_address" value="primary" />
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Name <span class="text-danger">*</span></p>
                                                            <input type="text" id="address_name" name="address_name" class="form-control" placeholder="Your Name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Email <span class="text-danger">*</span></p>
                                                            <input type="text" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ Auth::guard('user')->user() ? Auth::guard('user')->user()->email : '' }}" {{ Auth::guard('user')->user() ? 'disabled' : '' }} />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Phone <span class="text-danger">*</span></p>
                                                            <input type="text" id="address_phone" name="address_phone" class="form-control" placeholder="Your Phone" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Location <span class="text-danger">*</span></p>
                                                            <input type="text" id="address_location" name="address_location" class="form-control" placeholder="Your Location" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group checkout-address-outer">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Address <span class="text-danger">*</span></p>
                                                            <textarea name="address_address" id="address_address" class="form-control" placeholder="Your Address"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">City <span class="text-danger">*</span></p>
                                                            <input type="text" id="address_city" name="address_city" class="form-control" placeholder="Your City" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Postal Code <span class="text-danger">*</span></p>
                                                            <input type="text" id="address_pin" name="address_pin" class="form-control" placeholder="Postal Code" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 my-1 checkout-input-inner">
                                                        <div class="checkout-input checkout-country-outer">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Country <span class="text-danger">*</span></p>
                                                            <select name="country" id="country" class="form-control checkout-country">
                                                                <option value="">--Choose Country--</option>
                                                                @foreach ($countries as $country)
                                                                    @if ($country->name == 'India')
                                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 my-1 checkout-input-inner">
                                                        <div class="checkout-input checkout-state-outer">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">State <span class="text-danger">*</span></p>
                                                            <div id="outer_ajaxstate" class="checkout-state">
                                                                <select name="state" id="state" class="form-control">
                                                                    <option value="">--Choose State--</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <p class="f-18 pl-21 font-weight-bold mb-2">Landmark</p>
                                                            <input type="text" id="address_landmark" name="address_landmark" class="form-control" placeholder="Landmark" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="proceed-button">
                                            <button type="button" id="checkoutProceedBtn" class="btn btn-primary bg-blue">Proceed</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="nextpageLoader" class="row" style="display: none">

                        <div class="col-md-12 pt-3">
                            <p class="bg-cyan p-11 font-weight-bold text-white text-center text-uppercase f-20 gray mb-0"> Payment Options </p>
                            <div class="col-md-12">
                                <div class="w-100" id="myDIV">
                                    <div class="row payment">
                                        <div class="col-md-3 border pt-5 pb-3 tn active" id="profile" tabindex="1">
                                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start pay-method-btn" method="online" type="bank"><img src="{{ asset('front_view/images/bank.png') }}" class="img-fluid mx-auto d-block"></a>
                                            <p class="text-center pt-2 text-uppercase">Bank Transfer </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="2">
                                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start pay-method-btn" method="online"type="card"><img src="{{ asset('front_view/images/card.png') }}" class="img-fluid mx-auto d-block"></a>
                                            <p class="text-center pt-2 text-uppercase">Card Payment </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="3">
                                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start pay-method-btn" method="online"type="upi"><img src="{{ asset('front_view/images/upi.png') }}" class="img-fluid mx-auto d-block"></a>
                                            <p class="text-center pt-2 text-uppercase"> UPI Payments </p>
                                        </div>
                                        <div class="col-md-3 border pt-5 pb-3 tn" id="profile" tabindex="4">
                                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start pay-method-btn" method="cod"type="cod"><img src="{{ asset('front_view/images/cashon.png') }}" class="img-fluid mx-auto d-block"></a>
                                            <p class="text-center pt-2 text-uppercase">Cash on Delivery </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @if ($product->prescription == 1 && $enablePrescription === true && $allowPurchase === false)

                            @endif --}}


                        @php
                            $count = 0;
                            $pre_products = [];
                        @endphp
                        @if ($checkout_type == 'Cart')
                            @if (!Session::has('session_data'))

                                @if (!empty($carts))
                                    @foreach ($carts as $carts_row)
                                        {{-- {{$carts_row}} --}}

                                        @if (isset($carts_row['prescription']) && !empty($carts_row['prescription']))
                                            @if ($carts_row['prescription'] == '1')
                                                @php
                                                    array_push($pre_products, $carts_row['product_name']);
                                                    $count = $count + 1;
                                                @endphp

                                                {{-- <div class="col-md-6 priscription">

                                                    <label style="color: red">Prescription needed for : {{ $carts_row['product_name'] }}</label>
                                                    <button class="btn add_prescription" id='add_prescription_{{ $carts_row['product_id'] }}' data-id='{{ $carts_row['product_id'] }}'><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                                    <input type="file" id="prescription_file_{{ $carts_row['product_id'] }}" class="prescription_file" data-item='{{ $carts_row['product_id'] }}' style="width:0;height:0">
                                                </div> --}}
                                            @endif
                                        @endif
                                    @endforeach
                                    @if ($count > 0)
                                        <div class="col-md-6 priscription">

                                            <label style="color: red">Prescription needed for : {{ implode(',', $pre_products) }}</label>
                                            <button class="btn add_prescription" id='add_prescription_0' data-id='0' file_name=''><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                            <input type="file" id="prescription_file_0" class="prescription_file" data-item='0' style="width:0;height:0">

                                            <button class="btn btn-success add_more_btn" type="button"><i class="glyphicon glyphicon-plus"></i><i class="fa fa-plus text-white"></i></button>
                                            <div class="fields_extent"></div>

                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif

                        @if ($checkout_type == 'BuyNow')

                            @if (!empty($BuyNow['ProductDetails']))


                                {{-- {{$carts_row}} --}}
                                @if (isset($BuyNow['ProductDetails']->prescription) && !empty($BuyNow['ProductDetails']->prescription))
                                    @if ($BuyNow['ProductDetails']->prescription == '1')
                                        @php $count=$count+1; @endphp
                                        <div class="col-md-6 priscription">
                                            <label style="color: red">Prescription needed for : {{ $BuyNow['ProductDetails']->product_name }}</label>
                                            <button class="btn add_prescription" id='add_prescription_{{ $BuyNow['ProductDetails']->product_id }}' data-id='{{ $BuyNow['ProductDetails']->product_id }}'><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>
                                            <input type="file" id="prescription_file_{{ $BuyNow['ProductDetails']->product_id }}" class="prescription_file" data-item='{{ $BuyNow['ProductDetails']->product_id }}' style="width:0;height:0">
                                        </div>
                                    @endif
                                @endif

                            @endif
                        @endif



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
                    $grand_total = $taxAmt = $total_tax = $total_amount = $total_tax_percent = $totalvalue = 0;
                @endphp
                @if ($checkout_type == 'Cart')
                    {{-- {{dd($carts)}} --}}
                    @auth('user')
                        @if (!$carts->isEmpty())
                            @foreach ($carts as $carts_row)
                                @php
                                    $total_tax_percent = 0;
                                    if (isset($carts_row['tax_details']) && !empty($carts_row['tax_details'])) {
                                        foreach ($carts_row['tax_details'] as $taxdetails) {
                                            $total_tax_percent = $total_tax_percent + $taxdetails->percentage;

                                            // $taxAmt += ($carts_row['quantity'] * $carts_row['ProductPrice'] * $taxdetails->percentage) / 100;
                                        }

                                        $total_tax_percent_value = ($carts_row['ProductPrice'] * 100) / ($total_tax_percent + 100);
                                        $totalvalue = $totalvalue + ($carts_row['ProductPrice'] - $total_tax_percent_value) * $carts_row['quantity'];
                                    }
                                    $total_amount += $carts_row['quantity'] * $carts_row['ProductPrice'] - $totalvalue;
                                    $grand_total += $carts_row['quantity'] * $carts_row['ProductPrice'];
                                    $total_tax = $totalvalue;
                                    $cart_ids = ',' . $cart_ids . ',' . $carts_row->id;
                                    $cart_ids = ltrim($cart_ids, $cart_ids[0]);

                                    // $cart_ids = ','.$cart_ids .','. $carts_row->id;
                                    // $cart_ids = ltrim($cart_ids, $cart_ids[0]);

                                @endphp


                                @php
                                    // if (isset($carts_row['tax_details']) && !empty($carts_row['tax_details'])) {
                                    //     foreach ($carts_row['tax_details'] as $taxdetails) {
                                    //         $taxAmt += ($carts_row->quantity * $carts_row->ProductPrice * $taxdetails->percentage) / 100;
                                    //     }
                                    // }
                                    // $total_amount += $carts_row->quantity * $carts_row->ProductPrice - $taxAmt;
                                    // $grand_total += $carts_row->quantity * $carts_row->ProductPrice;
                                    // $total_tax = $total_tax + $taxAmt;

                                    // $taxAmt = 0;
                                @endphp
                            @endforeach
                        @endif
                    @endauth

                    @guest('user')
                        @if (!empty($carts))
                            {{-- {{dd($carts)}} --}}





                            @foreach ($carts as $carts_row)
                                @php
                                    $total_tax_percent = 0;
                                    if (isset($carts_row['tax_details']) && !empty($carts_row['tax_details'])) {
                                        foreach ($carts_row['tax_details'] as $taxdetails) {
                                            $total_tax_percent = $total_tax_percent + $taxdetails->percentage;

                                            // $taxAmt += ($carts_row['quantity'] * $carts_row['ProductPrice'] * $taxdetails->percentage) / 100;
                                        }

                                        $total_tax_percent_value = ($carts_row['ProductPrice'] * 100) / ($total_tax_percent + 100);
                                        $totalvalue = $totalvalue + ($carts_row['ProductPrice'] - $total_tax_percent_value) * $carts_row['quantity'];
                                    }
                                    $total_amount += $carts_row['quantity'] * $carts_row['ProductPrice'] - $totalvalue;
                                    $grand_total += $carts_row['quantity'] * $carts_row['ProductPrice'];
                                    $total_tax = $totalvalue;

                                    // $cart_ids = ','.$cart_ids .','. $carts_row->id;
                                    // $cart_ids = ltrim($cart_ids, $cart_ids[0]);

                                @endphp
                            @endforeach
                        @endif
                    @endguest
                @elseif($checkout_type == 'BuyNow' && !empty($BuyNow))
                    @php
                        $total_tax_percent = 0;

                        if (isset($BuyNow['tax_details']) && !empty($BuyNow['tax_details'])) {
                            foreach ($BuyNow['tax_details'] as $taxdetails) {
                                $total_tax_percent = $total_tax_percent + $taxdetails->percentage;
                                // $taxAmt += ($BuyNow['quantity'] * $BuyNow['ProductDetails']->ProductPrice * $taxdetails->percentage) / 100;
                            }

                            $total_tax_percent_value = ($BuyNow['ProductDetails']->ProductPrice * 100) / ($total_tax_percent + 100);
                              $totalvalue = $totalvalue + ($BuyNow['ProductDetails']->ProductPrice - $total_tax_percent_value) * $BuyNow['quantity'];
                        }

                        $total_amount += $BuyNow['quantity'] * $BuyNow['ProductDetails']->ProductPrice - $totalvalue;

                        $grand_total += $BuyNow['quantity'] * $BuyNow['ProductDetails']->ProductPrice;
                        $total_tax = $total_tax + $totalvalue;

                    @endphp
                @endif


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
                                <h6 id="checkout_subtotal"> {!! $common_settings[$currency_key]['value'] !!} {{ number_format($total_amount, 2) }}</h6>
                                <input type="hidden" id="hid_totalamount" value="{{ $total_amount }}">
                            </div>

                            <div class="cart-toatal-amount"><span>Tax</span>
                                <h6 id="checkout_subtotal"> {!! $common_settings[$currency_key]['value'] !!}{{ number_format($total_tax, 2) }}</h6>
                                <input type="hidden" id="hid_tax" value="{{ $total_tax }}">
                            </div>
                            @php
                                $pre_grand_total = $grand_total;

                                if ($grand_total < 149) {
                                    $grand_total = $pre_grand_total + $common_settings[$shipping_charge_149_key]['value'];
                                } elseif ($grand_total >= 149 && $grand_total < 499) {
                                    $grand_total = $pre_grand_total + $common_settings[$shipping_charge_499_key]['value'];
                                }

                            @endphp
                            <div class="cart-toatal-amount" id='shipping'><span>Shipping Fee</span>
                                <h6>
                                    @if ($pre_grand_total < 149 && $common_settings[$shipping_charge_149_key]['value'] > 0)
                                        {!! $common_settings[$currency_key]['value'] !!}{{ number_format($common_settings[$shipping_charge_149_key]['value'], 2) }}
                                        <input type="hidden" id="hid_shippingfee" value="{{ $common_settings[$shipping_charge_149_key]['value'] }}">
                                        @php $shipping_charge= $common_settings[$shipping_charge_149_key]['value']; @endphp
                                    @elseif($pre_grand_total >= 149 && $pre_grand_total < 499 && $common_settings[$shipping_charge_499_key]['value'] > 0)
                                        {!! $common_settings[$currency_key]['value'] !!}{{ number_format($common_settings[$shipping_charge_499_key]['value'], 2) }}
                                        <input type="hidden" id="hid_shippingfee" value="{{ $common_settings[$shipping_charge_499_key]['value'] }}">
                                        @php $shipping_charge=$common_settings[$shipping_charge_499_key]['value']; @endphp
                                    @else
                                        Free
                                        <input type="hidden" id="hid_shippingfee" value="0">
                                        @php $shipping_charge=0; @endphp
                                    @endif
                                </h6>
                            </div>
                            <div class="cart-toatal-amount"><span>Total</span>
                                <h6 id="checkout_totalamt">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($grand_total, 2) }}</h6>

                                <input type="hidden" id="hid_grandtotal" value="{{ $grand_total }}">
                            </div>

                            @php $key = array_search('company_logo', array_column($common_settings, 'item')); @endphp
                            <input type="hidden" id='company_logo_image' value="{{ $common_settings[$key]['value'] }}">
                            <input type="hidden" id='sel-payment-method' value="">
                            <input type="hidden" id='sel-payment-gateway' value="">
                            <input type="hidden" id='sel-payment-type' value="">

                            <div class="cart-toatal-amount" id='showmethod'style="display: none">

                            </div>
                            <div class="pay-image" id='payment-images'style="display: none">

                            </div>


                            {{-- <div class="pay-method" id='pay-method-types'style="display: none"> --}}
                            {{-- <button class="btn pay-method-btn" type="button" method="cod">COD</button>
                                <button class="btn pay-method-btn" type="button" method="online">ONLINE</button> --}}
                            {{-- </div> --}}



                            <div class="w-100" id="pickup" style="display: none">
                                <div class="row pickup">
                                    <div class="form-check agree-terms">
                                        <input class="form-check-input" type="checkbox" name="checkme" id="pickup_store">
                                        <label class="form-check-label" for="termsCondition">Pickup Store</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center checkout-btn" id='chk-id'>

                                <a href="javascript:void(0)" id="checkoutButtonToPay" style="display: none">Place Order</a>
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
        var primary_address = [];
        var home_address = [];
        var work_address = [];
        var i = 0;

        @if (isset($user_details['primary']))
            primary_address.push({
                name: '{{ $user_details['primary']['name'] }}',
                email: '{{ $user_details['primary']['email'] }}',
                phone: '{{ $user_details['primary']['phone'] }}',
                pin: '{{ $user_details['primary']['pin'] }}',
                location: '{{ $user_details['primary']['location'] }}',
                address: '{{ preg_replace("/\r|\n/", ',', $user_details['primary']['address']) }}',
                city: '{{ $user_details['primary']['city'] }}',
                state_id: '{{ $user_details['primary']['state_id'] }}',
                country_id: '{{ $user_details['primary']['country_id'] }}',
                landmark: '{{ $user_details['primary']['landmark'] }}',
                type: '{{ $user_details['primary']['type'] }}',
            });
        @endif
        @if (isset($user_details['home']))
            home_address.push({
                name: '{{ $user_details['home']['name'] }}',
                email: '{{ $user_details['home']['email'] }}',
                phone: '{{ $user_details['home']['phone'] }}',
                pin: '{{ $user_details['home']['pin'] }}',
                location: '{{ $user_details['home']['location'] }}',
                address: '{{ preg_replace("/\r|\n/", ',', $user_details['home']['address']) }}',
                city: '{{ $user_details['home']['city'] }}',
                state_id: '{{ $user_details['home']['state_id'] }}',
                country_id: '{{ $user_details['home']['country_id'] }}',
                landmark: '{{ $user_details['home']['landmark'] }}',
                type: '{{ $user_details['home']['type'] }}',
            });
        @endif
        @if (isset($user_details['work']))
            work_address.push({
                name: '{{ $user_details['work']['name'] }}',
                email: '{{ $user_details['work']['email'] }}',
                phone: '{{ $user_details['work']['phone'] }}',
                pin: '{{ $user_details['work']['pin'] }}',
                location: '{{ $user_details['work']['location'] }}',
                address: '{{ preg_replace("/\r|\n/", ',', $user_details['work']['address']) }}',
                city: '{{ $user_details['work']['city'] }}',
                state_id: '{{ $user_details['work']['state_id'] }}',
                country_id: '{{ $user_details['work']['country_id'] }}',
                landmark: '{{ $user_details['work']['landmark'] }}',
                type: '{{ $user_details['work']['type'] }}',
            });
        @endif

        $(document).on('click', '.add_prescription', function(e) {
            var pid = $(this).attr('data-id');
            e.preventDefault();
            $('#prescription_file_' + pid).trigger("click");

        });

        $(document).on('click', '#checkoutButtonToPay', function(e) {
            var counts = {{ $count }}
            var elm = $(this).parent();
            if (counts > 0 && i < 1) {
                $("#myElem").html('Please update prescription file for the medicines which need prescription.').show().delay(4000).fadeOut();
            } else if ($('#termsCondition').prop("checked") == false) {
                $("#myElem").html('Please agree Terms and Conditions').show().delay(2000).fadeOut();
            } else {
                if ($('#pickup_store').is(':checked') == true) {
                    if ($('#stores :selected').val() == '') {
                        alert('Please choose pickup store');
                        return false;
                    }
                }

                var address_type = $("#delivery_address").val();
                var paymethod = $('#sel-payment-method').val();
                var paygateway = $('#sel-payment-gateway').val();

                var addressId = $("#delivery_address").attr('data-id');
                var cart_ids = '{{ $checkout_type == 'Cart' ? $cart_ids : 0 }}';
                var total_amount = $('#hid_totalamount').val();
                var total_tax_amount = $('#hid_tax').val();
                var shipping_charge = $('#hid_shippingfee').val();
                var grandtotal = $('#hid_grandtotal').val();
                var productId = quantity = prescriptionID = 0;
                @if ($checkout_type == 'BuyNow' && !empty($BuyNow) && isset($BuyNow['ProductDetails']))
                    productId = '{{ $BuyNow['ProductDetails']->product_id }}';
                    quantity = '{{ $BuyNow['quantity'] }}';;
                    prescriptionID = '{{ !empty($prescriptiondetails) ? $prescriptiondetails->id : 0 }}';
                @endif
                if (paymethod == 'cod') {
                    $('#chk-id').html('<a href="javascript:void(0)" style="display:none" id="checkoutButton">Pay Now</a>');
                    document.getElementById("checkoutButton").click();
                } else {
                    $('#chk-id').html('<a href="javascript:void(0)"  id="checkoutButton">Pay Now</a>');
                }
            }
        });

        $(document).on('change', '.prescription_file', function(e) {
            var pid = $(this).attr('data-item');
            formData = new FormData();

            var file = document.getElementById('prescription_file_' + pid);
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
                    console.log(res);
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
                        $('#add_prescription_' + pid).attr('file_name', res.file_name)

                        if ((res.type == "disable")) {
                            i = i + 1;
                            document.getElementById("add_prescription_" + pid).disabled = true;
                        }
                        // if (res.prescription_id) {
                        //     $('#hid_prescription').append('<input hidden class="prescriptions" name="hid_prescription[]"" value=' + res.prescription_id + '>');

                        // }

                        // document.getElementById("add_prescription_"+pid).disabled = true;
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
                        if ((res.type == "disable")) {

                            i = i + 1;
                            document.getElementById("add_prescription_" + pid).disabled = true;
                        }
                        // if (res.prescription_id) {
                        //     $('#hid_prescription').append('<input hidden class="prescriptions" name="hid_prescription[]"" value=' + res.prescription_id + '>');

                        // }
                    }
                }
            });
        });



        $(document).on("click", ".address-choose", function(e) {
            address_type = $(this).attr("id");
            $(this).addClass('active').siblings().removeClass('active');

            updateAddress(address_type);
            e.preventDefault();
            $("#address_type").val(address_type);
        });

        function updateAddress(address_type) {
            $("#delivery_address").val(address_type);
            if (address_type == 'primary' && primary_address.length > 0) {
                $("#address_name").val(primary_address[0]['name']);
                $("#address_phone").val(primary_address[0]['phone']);
                $("#address_pin").val(primary_address[0]['pin']);
                $("#address_location").val(primary_address[0]['location']);
                $("#address_address").val(primary_address[0]['address']);
                $("#address_city").val(primary_address[0]['city']);
                $("#address_landmark").val(primary_address[0]['landmark']);
                $("#country").val(primary_address[0]['country_id']);
                //$("#edit_hid_address_id").val(primary_address.id);
                loadState(primary_address[0]['country_id'], primary_address[0]['state_id']);
            } else if (address_type == 'home' && home_address.length > 0) {
                $("#address_name").val(home_address[0]['name']);
                $("#address_phone").val(home_address[0]['phone']);
                $("#address_pin").val(home_address[0]['pin']);
                $("#address_location").val(home_address[0]['location']);
                $("#address_address").val(home_address[0]['address']);
                $("#address_city").val(home_address[0]['city']);
                $("#address_landmark").val(home_address[0]['landmark']);
                $("#country").val(home_address[0]['country_id']);
                loadState(home_address[0]['country_id'], home_address[0]['state_id']);
            } else if (address_type == 'work' && work_address.length > 0) {
                $("#address_name").val(work_address[0]['name']);
                $("#address_phone").val(work_address[0]['phone']);
                $("#address_pin").val(work_address[0]['pin']);
                $("#address_location").val(work_address[0]['location']);
                $("#address_address").val(work_address[0]['address']);
                $("#address_city").val(work_address[0]['city']);
                $("#address_landmark").val(work_address[0]['landmark']);
                $("#country").val(work_address[0]['country_id']);
                loadState(work_address[0]['country_id'], work_address[0]['state_id']);
            } else {
                $("#address_name").val('');
                $("#address_phone").val('');
                $("#address_pin").val('');
                $("#address_location").val('');
                $("#address_address").val('');
                $("#address_city").val('');
                $("#address_landmark").val('');
                $("#country").val('');
                var state = '<option value="">--Choose State--</option>';
                $("#ajx_state").html(state);
            }
        }

        updateAddress('primary');
        $('#primary').addClass('active');

        function loadState(country_id, state_id) {
            outerhtml = $("#outer_ajaxstate").html();
            outerhtml = $("#outer_ajaxstate").html(loader_gif);
            $.ajax({
                type: "post",
                data: {
                    id: country_id,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: 'json',
                url: "{{ route('ajax.stateLoader') }}", //Please see the note at the end of the post**
                success: function(res) {
                    if (res.ajax_status == 'success') {
                        var state_html = '';
                        state_html += '<select name="state" class="form-control" id="ajx_state">';
                        state_html += '<option value="">--Choose State--</option>';
                        $.each(res.states, function(index, item) {
                            if (state_id == item.id) {
                                state_html += '<option value="' + item.id + '" selected>' + item.name + '</option>';
                            } else {
                                state_html += '<option value="' + item.id + '">' + item.name + '</option>';
                            }
                        });
                        state_html += '</select>';
                        $("#outer_ajaxstate").html(state_html);
                    } else {
                        $("#outer_ajaxstate").html(outerhtml);
                        $("#myElem").html(res.message);
                        $("#myElem").show().delay(3000).fadeOut();
                    }
                }
            });
        }

        $(document).on('change', '#country', function(e) {
            var country_id = $(this).val();
            outerhtml = '';
            if (country_id != '') {
                outerhtml = $("#outer_ajaxstate").html();
                $("#outer_ajaxstate").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        id: country_id,
                        "_token": "{{ csrf_token() }}"

                    },
                    dataType: 'json',
                    url: "{{ route('ajax.stateLoader') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            var state_html = '';
                            state_html += '<select name="state" class="form-control" id="ajx_state">';
                            state_html += '<option value="">--Choose State--</option>';
                            $.each(res.states, function(index, item) {
                                state_html += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            state_html += '</select>';
                            $("#outer_ajaxstate").html(state_html);
                        } else {
                            $("#outer_ajaxstate").html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            } else {
                var state_html = '<select name="state" class="form-control" id="ajx_state">';
                state_html += '<option value="">--Choose State--</option>';
                state_html += '</select>';
                $("#outer_ajaxstate").html(state_html);
            }
        });

        $('#checkoutProceedBtn').click(function() {
            var datastring = $('#checkout-form').serialize();
            $.ajax({
                type: "POST",
                url: "{{ route('checkout.updateAddress') }}",
                data: datastring,
                dataType: "json",
                success: function(response) {
                    if (response.result) {
                        $('#deliveryaddressModule').hide();
                        $('#nextpageLoader').show();
                        $('#pay-method-types').show();
                        $('#pickup').show();

                        let addressId = (response.addressId != '' ? response.addressId : 0);
                        $('#delivery_address').attr('data-id', addressId);
                    } else {
                        $('#delivery_address').attr('data-id', 0);
                        $("#myElem").html(response.errorMsg);
                        $("#myElem").show().delay(3000).fadeOut();
                        // if (response.checkout_type == 'cart') {
                        //     location.href = '{{ url('/products/cart') }}' + '?rejecteditems=' + response.RejectedItems;
                        // } else {
                        //     alert(response.errorMsg);
                        //     window.history.back();
                        // }
                    }
                },
            });
        });

        $(document).on('click', '#checkoutButton', function(e) {
            var counts = {{ $count }};

            var checkout_type = "{{ $checkout_type }}";
            var elm = $(this).parent();
            // var prescription_ids = $('input[name="hid_prescription[]"]').map(function() {
            //     return this.value;
            // }).get();

            // var users = $('input:text.prescriptions').serialize();
            if (counts > 0 && i < 1) {
                $("#myElem").html('Please update prescription file for the medicines which need prescription.').show().delay(4000).fadeOut();
            } else if ($('#termsCondition').prop("checked") == false) {
                $("#myElem").html('Please agree Terms and Conditions').show().delay(2000).fadeOut();
            } else {
                if ($('#pickup_store').is(':checked') == true) {
                    if ($('#stores :selected').val() == '') {
                        alert('Please choose pickup store');
                        return false;
                    }
                }

                elm.next().html('<div class="loader">' + loader_gif + '<div>Please wait till check out complete.</div></div>');


                var address_type = $("#delivery_address").val();


                var addressId = $("#delivery_address").attr('data-id');
                var company_logo = $("#company_logo_image").val();
                var cart_ids = '{{ $checkout_type == 'Cart' ? $cart_ids : 0 }}';

                var total_amount = {{ $total_amount }};
                var paymethod = $('#sel-payment-method').val();
                var paygateway = $('#sel-payment-gateway').val();
                var paymenttype = $('#sel-payment-type').val();
                var total_tax_amount = {{ $total_tax }}
                if ($('#pickup_store').is(':checked') == true) {
                    var grandtotal = ({{ $grand_total - $shipping_charge }}).toFixed(2);
                    var shipping_charge = 0;
                    var store_id = $('#stores :selected').val();
                } else {
                    var shipping_charge = {{ $shipping_charge }};
                    var grandtotal = {{ $grand_total }}
                    var store_id = '';
                }
                var productId = quantity = prescriptionID = 0;

                @if ($checkout_type == 'BuyNow' && !empty($BuyNow) && isset($BuyNow['ProductDetails']))
                    productId = '{{ $BuyNow['ProductDetails']->product_id }}';
                    quantity = '{{ $BuyNow['quantity'] }}';;
                    prescriptionID = '{{ !empty($prescriptiondetails) ? $prescriptiondetails->id : 0 }}';
                @endif
                $.ajax({
                    type: "POST",
                    url: "{{ $checkout_type == 'Cart' ? route('product.placeorder') : route('product.placeorder.buynow') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "address_type": address_type,
                        "addressId": addressId,
                        "total_amount": total_amount,
                        "total_tax_amount": total_tax_amount,
                        "shipping_charge": shipping_charge,
                        "grandtotal": grandtotal,
                        "cart_ids": cart_ids,
                        "productId": productId,
                        "quantity": quantity,
                        "payment_method": paymethod,
                        "payment_gateway": paygateway,
                        "checkout_type": '{{ $checkout_type }}',
                        "store_id": store_id,
                        "prescriptionID": prescriptionID,
                        // 'prescription_ids': prescription_ids,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            if (response.payment_method == 'cod') {
                                window.location.href = "{{ URL::to('order/invoice') }}/" + response.order.id;

                            } else if (response.payment_method == 'online') {
                                if (response.payment_gateway == 'razorpay') {

                                    var checkout_type = "{{ $checkout_type }}";
                                    var options = {
                                        "key": "{{ config('constants.RAZORPAY_KEY') }}",
                                        "amount": ((parseFloat(grandtotal).toFixed(2)) * 100),
                                        "name": "ExpressMed",
                                        "description": "Payment",
                                        "image": "{{ asset('/img/logo.png') }}",
                                        "handler": function(res) {
                                            $('#checkoutButton').css({
                                                display: 'none'
                                            });
                                            window.location.href = '{{ url('razorpay-payment/response') }}?checkout_type=' + checkout_type + '&razor_payment_id=' + res.razorpay_payment_id + '&order_id=' + response.order.id;
                                        },
                                        "prefill": {
                                            "name": response.useraddress.name,
                                            "contact": response.useraddress.phone,
                                            "email": response.useraddress.email,
                                        },
                                        "theme": {
                                            "color": "#528FF0"
                                        },
                                        "order_id": response.razor_order_response_id,
                                        "modal": {
                                            "ondismiss": function() {
                                                $('.loader').html('');

                                            }
                                        }

                                    };

                                    var rzp1 = new Razorpay(options);
                                    rzp1.open();
                                    rzp1.on('payment.failed', function(response) {

                                    });
                                    e.preventDefault();
                                } else if (response.payment_gateway == 'billdesk') {

                                    elm.next().html("");
                                    var checkout_type = "{{ $checkout_type }}";
                                    var url = "{{ route('payment.return', ['checkout_type' => ':type']) }}";
                                    url = url.replace(':type', checkout_type);

                                    var flow_config = {
                                        merchantId: "{{ Config::get('constants.payment_constants.merchant_id') }}",
                                        bdOrderId: response.jwtPayload.bdorderid,
                                        authToken: response.jwtPayload.links[1].headers.authorization,
                                        childWindow: true,
                                        returnUrl: url,

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
                                }
                            }
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
        $(document).on('click', '.pay-method-btn', function(e) {

            var listItems = $(".list-group-item");
            // Remove 'active' tag for all list items
            for (let i = 0; i < listItems.length; i++) {
                listItems[i].classList.remove("active");
            }
            // Add 'active' tag for currently selected item
            this.classList.add("active");
            var selected_method = $(this).attr('method');
            var selected_type = $(this).attr('type');

            var html = '';
            var gatehtml = '';
            if (selected_method == '') {
                alert('Please select Payment Method')
            } else {
                html += '<span>Payment Method</span><h6>' + selected_method.toUpperCase() + '</h6>'
                if (selected_method == 'online') {
                    if (selected_type == 'bank') {
                        gatehtml += '<span>Payment Gateway</span><h6><img src="{{ asset('front_view/images/billdesk.jpg') }}"></h6>';
                        $('#sel-payment-gateway').val('billdesk');
                        // gatehtml += '<a href="javascript:void(0)" class="payment_gateway" value="billdesk"><img src="{{ asset('front_view/images/billdesk.jpg') }}"></a>';
                    } else if ((selected_type == 'card') || (selected_type == 'upi')) {
                        gatehtml += '<span>Payment Gateway</span><h6><img src="{{ asset('front_view/images/razorpay.jpg') }}"></h6>';
                        $('#sel-payment-gateway').val('razorpay');
                        // gatehtml += '<a href="javascript:void(0)"class="payment_gateway"value="razorpay"><img src="{{ asset('front_view/images/razorpay.jpg') }}"></a>';
                    }
                    $('#sel-payment-type').val(selected_type);
                    $('#payment-images').show();
                    // $('#payment-images').html(imagehtml);
                    $('#payment-images').addClass('cart-toatal-amount');
                    $('#payment-images').html(gatehtml);
                    $('#checkoutButtonToPay').hide();
                    $('#chk-id').html('');
                } else if (selected_method == 'cod') {
                    $('#payment-images').hide();
                    $('#payment-images').html('');
                    $('#pickup').show();
                    $('#checkoutButtonToPay').show();

                }
                $('#pickup').show();
                $('#chk-id').html('<a href="javascript:void(0)"id="checkoutButtonToPay">Place Order</a>');

                $('#sel-payment-method').val(selected_method);
                // $('#pay-method-types').hide();

                $('#showmethod').show();

                $('#showmethod').html(html);
            }
        });

        $('#pickup_store').click(function() {
            var elm = $(this).parent();
            var shipping_charge = {{ $shipping_charge }}
            var html = '';
            if ($('#pickup_store').is(':checked') == true) {

                var grand_total = {{ $grand_total - $shipping_charge }}
                $('#shipping').html(' <span>Shipping Fee</span><h6>Free</h6>');
                $('#checkout_totalamt').html(currencyIcon + grand_total.toFixed(2));
                $.ajax({
                    url: "{{ route('stores.list') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.length > 0) {
                            html += '<select class=" btn dropdown-toggle banner-select" name="stores" id="stores">';
                            html += '<option value="">-- Select Store --</option>';

                            $.each(data, function(key, value) {
                                html += '<option style="color: black;" value="' + value.id + '">' + value.name + '</option>';
                            });
                            html += '</select>';
                            $(html).insertAfter('#pickup');
                        } else {
                            alert('No pickup stores Available');
                            $('#stores').remove();
                            $('#pickup_store').prop('checked', false);
                            // $('#pickup_store').trigger('click');
                            var grand_total = {{ $grand_total }}
                            $('#shipping').html(' <span>Shipping Fee</span><h6>' + currencyIcon + shipping_charge.toFixed(2) + '</h6>')
                            $('#checkout_totalamt').html(currencyIcon + grand_total.toFixed(2));
                            $('#stores').remove();

                            // $('#pickup_store').click();




                        }
                    }
                });

            } else if ($('#pickup_store').is(':checked') == false) {
                var grand_total = {{ $grand_total }}
                $('#shipping').html(' <span>Shipping Fee</span><h6>' + currencyIcon + shipping_charge.toFixed(2) + '</h6>')
                $('#checkout_totalamt').html(currencyIcon + grand_total.toFixed(2));
                $('#stores').remove();


            }
        });

        $(document).ready(function() {
            var pre_count = 0;
            $(".add_more_btn").click(function() {
                pre_count = pre_count + 1;
                var html = '<div class="control-group input-group" style="margin-top:10px">';
                html += '  <button class="btn add_prescription" id="add_prescription_' + pre_count + '" data-id="' + pre_count + '"file_name=""><i class="fas fa-upload" aria-hidden="true"></i> Prescription</button>';
                html += ' <input type="file" id="prescription_file_' + pre_count + '" class="prescription_file" data-item="' + pre_count + '" style="width:0;height:0">';
                html += '<div class="input-group-btn">';
                html += ' <button class="btn btn-danger" id="prescription_delete_' + pre_count + '" delete-id="' + pre_count + '" type="button"><i class="glyphicon glyphicon-remove"></i><i class="fa fa-close text-white"></i></button>';
                html += '</div>';
                html += ' </div>';
                $(".fields_extent").append(html);
            });

            $("body").on("click", ".btn-danger", function() {
                var id = $(this).attr('delete-id');
                formData = new FormData();

                var file_name = $('#add_prescription_' + id).attr('file_name');
                formData.append('file_name', file_name);
                // formData.append('file', file.files[0]);
                formData.append("_token", "{{ csrf_token() }}");
                $.ajax({
                    type: "post",
                    data: formData,
                    enctype: 'multipart/form-data',
                    contentType: false,
                    processData: false,
                    url: '{{ route('prescription.upload.delete') }}',
                    success: function(res) {}
                });
                $(this).parents(".control-group").remove();
            });
        });
    </script>
@endsection
