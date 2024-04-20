@extends('layouts.frontview.app')

@section('content')
@include('layouts.frontview.topmenubar_frontview')

    @if(!empty($errors) && count($errors) > 0)
        <div class="col-md-12">
            <h5 class="text-center text-danger">{{ $errors->all()[0] }}</h5>
        </div>
    @endif

    <div class="width-container cart-page">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls">Shopping Cart</li>
            </ol>
        </nav>
    </div>

    <div class="width-container cart-page row">
        <div class="col-lg-8 col-sm-12 left-cart-page">
            <div class="cart-content-outer">
                <div class="cart-content-name">
                    <div class="cart-heading-one">
                        <h6>Product</h6>
                    </div>
                    <div class="price-product-full d-flex flex-grow-1">
                        <div class="cart-heading-two">
                            <h6>Price</h6>
                        </div>
                        <div class="cart-heading-three">
                            <h6>Quantity</h6>
                        </div>
                        <div class="cart-heading-four">
                            <h6>Sub Total</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart products starts --}}
            <div id="cart_content">
                @php $subtotal = 0; $grandtotal = 0; $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')); @endphp
                @auth('user')
                    @if(!$carts->isEmpty())
                        @foreach($carts as $list)
                            <div class="main-cart-full">
                                <div class="col-md-12 row full-cart-outer">
                                    <div class="cart-content-first">
                                        <div class="delete-cart"><a href="javascript:void(0)" onclick="deleteCart({{ $list->product_id }})"><i class="fas fa-times"></i></a></div>
                                        <div class="cartdesc-img-outer">
                                            <a href="{{ route('shopping.productdetail', $list->product_url) }}">
                                                @if($list->product_image != '')
                                                    <img src="{{ asset('assets/uploads/products/').'/'.$list->product_image }}" class="img-fluid">
                                                @else
                                                    <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="cart-content-first-desc">{{$list->product_name }}</div>
                                    </div>
                                    <div class="quantity-price1">
                                        <div class="item-price cart-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($list->ProductPrice, 2) }}</div>

                                        {{-- Tax section starts --}}
                                        @php $tax_amt=0; $tot_taxAmount = 0; $tot_amount = ($list->quantity * $list->ProductPrice);  @endphp
                                        @if(isset($list->tax_details) && !empty($list->tax_details))
                                            @foreach($list->tax_details as $tax_row)
                                                @php $tax_amt = ($tot_amount * $tax_row->percentage) / 100; @endphp
                                                <small><span class="f-16 mr-1">{{ $tax_row->percentage.'% '.$tax_row->tax_name}}</span></small>
                                                <small>{!! $common_settings[$currency_key]['value'] !!}<span class="f-16 mr-1">{{ number_format($tax_amt, 2) }}</span><br></small>
                                                @php $tot_taxAmount += $tax_amt; @endphp
                                            @endforeach
                                        @endif
                                        {{-- Tax section ends --}}
                                    </div>
                                    <div class="cart-quantity">
                                        <div class="input-group quantity-count">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-number cart-no" data-type="minus" data-field="quant[1]" data-id="{{ $list->product_id }}" {{ $list->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                            <input type="text" name="quant[1]" class="form-control input-number" value="{{ $list->quantity }}" min="1" max="10">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-number cart-no" data-type="plus" data-field="quant[1]" data-id="{{ $list->product_id }}">
                                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="cart-sub-total">
                                        <div class="item-price cart-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($tot_amount + $tot_taxAmount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            @php $grandtotal = $subtotal += ($tot_amount + $tot_taxAmount); @endphp
                        @endforeach
                    @else
                        <div class="main-cart-full">
                            <div class="col-md-12 row full-cart-outer">
                                <h6>Your cart is empty</h6>
                            </div>
                        </div>
                    @endif
                @endauth

                @guest('user')
                    @if(!empty($carts))
                        @foreach($carts as $list)
                            <div class="main-cart-full">
                                <div class="col-md-12 row full-cart-outer">
                                    <div class="cart-content-first">
                                        <div class="delete-cart"><a href="javascript:void(0)" onclick="deleteCart({{ $list['product_id'] }})"><i class="fas fa-times"></i></a></div>
                                        <div class="cartdesc-img-outer">
                                            @if($list['product_image'] != '')
                                                <img src="{{ asset('assets/uploads/products/') }}/{{ $list['product_image'] }}" class="img-fluid">
                                            @else
                                                <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                                            @endif
                                        </div>
                                        <div class="cart-content-first-desc">{{$list['product_name'] }}</div>
                                    </div>
                                    <div class="quantity-price1">
                                        <div class="item-price cart-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($list['ProductPrice'], 2) }}</div>

                                        {{-- Tax section starts --}}
                                        @php $tax_amt = 0; $tot_taxAmount = 0; $tot_amount = ($list['quantity'] * $list['ProductPrice']);  @endphp
                                        @if(isset($list['tax_details']) && !empty($list['tax_details']))
                                            @foreach($list['tax_details'] as $tax_row)
                                                @php $tax_amt = ($tot_amount * $tax_row->percentage) / 100; @endphp
                                                <small><span class="f-16 mr-1">{{ $tax_row->percentage.'% '.$tax_row->tax_name}}</span></small>
                                                <small>{!! $common_settings[$currency_key]['value'] !!}<span class="f-16 mr-1">{{ number_format($tax_amt, 2) }}</span><br></small>
                                                @php $tot_taxAmount += $tax_amt; @endphp
                                            @endforeach
                                        @endif
                                        {{-- Tax section ends --}}
                                    </div>
                                    <div class="cart-quantity">
                                        <div class="input-group quantity-count">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn cart-no minusButton" data-type="minus" data-id="{{ $list['product_id'] }}" {{ $list['quantity'] <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control input-number" value="{{ $list['quantity'] }}" min="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn cart-no plusButton" data-type="plus" data-id="{{ $list['product_id'] }}">
                                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="cart-sub-total">
                                        <div class="item-price cart-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format(($tot_amount + $tot_taxAmount), 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            @php $grandtotal = $subtotal += ($tot_amount + $tot_taxAmount); @endphp
                        @endforeach
                    @else
                        <div class="main-cart-full">
                            <div class="col-md-12 row full-cart-outer">
                                <h6>Your cart is empty</h6>
                            </div>
                        </div>
                    @endif
                @endguest
            </div>
            {{-- Cart products ends --}}

            <div class="col-md-12 shopping-btn">
                <div class="continue-shopping">
                    <a href="{{ route('home') }}">Continue Shopping</a>
                </div>
                <div class="clear-cart">
                    <a href="javascript:void(0)" id="empty_cart">Clear Cart</a>
                </div>
            </div>
        </div>


        <div class="col-lg-4 col-sm-12 right-cart-page">
            <div class="main-cart-total">
                <h5>Cart Total</h5>
                <div class="cart-toatal-amount"><span>Sub Total</span>
                    <h6 id="checkout_subtot">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($subtotal, 2) }}</h6>
                </div>
                <div class="cart-toatal-amount"><span>Shipping</span>
                    <h6>Free</h6>
                </div>
                <div class="cart-toatal-amount"><span>Total</span>
                    <h6 id="checkout_total">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($grandtotal, 2) }}</h6>
                </div>
                <div class="d-flex justify-content-center checkout-btn">
                    <a href="{{ route('product.checkout') }}">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer_scripts')
    <script>

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
    </script>
@endsection
