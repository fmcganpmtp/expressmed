@extends('layouts.frontview.app')
@section('content')
    @include('layouts.frontview.topmenubar_frontview')
    <section class="invoice-out">
        @if (session('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{{ session('success') }}</li>
                    @if ($payment_details)
                        <li>Date&Time: {{ $payment_details->transaction_date }}</li>
                        <li>Order ID: {{ $payment_details->order_id }}</li>
                        <li>Payment Method: online payment</li>
                        <li>Transaction ID: {{ $payment_details->transaction_id }}</li>
                    @endif
                </ul>
            </div>
        @endif
        <article class="width-container outer-width">
            <div class="top-selling-outer">
                <h3>Your order has been placed successfully</h3>
                <div class="col-md-12 cart-container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 invoice-order-outer">
                            <div class="deliveryaddressModule">
                                <div class="checkout-head active">
                                    <button type="button" value="{{ $orders->id }}" class="btn btn-sm print_invoice" title="Print Invoice"><i class="fa fa-print"></i> Print Invoice</button>
                                </div>
                                <div class="checkout-fields-outer table-responsive">
                                    <div class="form-row align-items-center ml-0 mr-0 checkout-input-outer">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Price per item</th>
                                                    <th>Tax per item</th>
                                                    <th>Total(Including Tax)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')); @endphp
                                                @php
                                                    $sub_total = 0;
                                                    $grand_total = 0;
                                                @endphp
                                                @foreach ($order_details as $key => $order)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            @if ($order->product_image)
                                                                <img src="{{ asset('assets/uploads/products/' . $order->product_image) }}" style="width: 50px;height:50px">
                                                            @else
                                                                <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" style="width: 50px;height:50px" />
                                                            @endif

                                                            <div class="section-product-name">
                                                                {{ $order->product_name }}
                                                                @if ($order->variant_type != '')
                                                                    <p> <strong>Styles:</strong> {{ $order->variant_type }}</p>
                                                                @endif
                                                                @if ($order->variant_unit != '')
                                                                    <p> <strong>Size:</strong> {{ $order->variant_unit }}</p>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ $order->quantity }}</td>
                                                        <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($order->price, 2) }}</td>
                                                        <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($order->total_tax/$order->quantity, 2) }}</td>
                                                        <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($order->amount, 2) }}</td>
                                                    </tr>
                                                    @php
                                                        $sub_total += $order->quantity * $order->price + $order->total_tax;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td>Sub Total(Excluding Tax)</td>
                                                <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format(($orders->total_amount), 2) }}</td>
                                                {{-- {{ number_format(($sub_total-$orders->total_tax_amount), 2) }} --}}
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td>Total Tax</td>
                                                <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format(($orders->total_tax_amount), 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td>Shipping Fee</td>
                                                <td>
                                                    @if ($orders->shipping_charge > 0)
                                                    {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->shipping_charge,2) }}
                                                @else
                                                    Free
                                                @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td class="text-success">
                                                    <h4>Grand Total</h4>
                                                </td>
                                                <td class="text-success">
                                                    <h4>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($orders->grand_total, 2) }}</h4>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
@section('footer_scripts')
    <script>
        //----print-Order-Invoice--
        $('.print_invoice').on('click', function() {
            var orderID = $(this).val();
            window.open('{{ url('/order/invoice/print/') }}/' + orderID, 'name', 'width=1000,height=800');
        });
    </script>
@endsection
