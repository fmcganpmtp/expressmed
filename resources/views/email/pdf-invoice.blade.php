<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Some Random Title</title>
    <style>
        body {
            font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace !important;
            letter-spacing: -0.3px;
        }

        .invoice-wrapper {
            width: 700px;
            margin: auto;
        }

        .nav-sidebar .nav-header:not(:first-of-type) {
            padding: 1.7rem 0rem .5rem;
        }

        .logo {
            font-size: 50px;
        }

        .sidebar-collapse .brand-link .brand-image {
            margin-top: -33px;
        }

        .content-wrapper {
            margin: auto !important;
        }

        .billing-company-image {
            width: 50px;
        }

        .billing_name {
            text-transform: uppercase;
        }

        .billing_address {
            text-transform: capitalize;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 10px;
            vertical-align: top;
        }

        .row {
            display: block;
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .table-hover thead tr {
            background: #eee;
        }

        .table-hover tbody tr:nth-child(even) {
            background: #fbf9f9;
        }

        address {
            font-style: normal;
        }
    </style>
</head>

<body>
    <div class="row invoice-wrapper">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">

                    @php $company_logo_key = array_search('company_logo', array_column($common_settings, 'item'));  @endphp
                    <div class="invoice-logo">
                        <img src="{{ asset('/assets/uploads/logo/' . $common_settings[$company_logo_key]['value']) }}" style="height:40px" class="img-responsive" alt="" title="company logo" />
                    </div>

                    <table class="table">
                        <tr>
                            <td>
                                <h3><b>Invoice Details</b></h3>
                            </td>

                            <td class="text-right">
                                <strong>Date: {{ date('d-m-Y h:ia', strtotime($orders->order_date)) }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td class="text-right">
                                <strong>Invoice#: <b>{{ $orders->invoice_number }}</b></strong>
                            </td>
                        </tr>

                        <tr>
                            <td>

                                <div class="">
                                    <p><strong>{{ $orders->name }}</strong></p>
                                    <br>
                                    @if ($orders->delivery_type == 'direct')
                                        <p>Delivery Address</p>
                                        <address>
                                            {{-- <p><strong style="float-right">Order ID :{{ $orders->order_id }}</strong></p> --}}
                                            <p>{{ $orders->phone }}</p>
                                            <p>{{ $orders->address }}</p>
                                            <p>{{ $orders->city }}</p>
                                            <p>{{ $orders->location }}</p>
                                            <p>{{ $orders->landmark }}</p>
                                            <p>{{ $orders->state_name }}</p>
                                            <p>{{ $orders->country_name }}</p>
                                            <p>{{ $orders->pin }}</p>
                                        </address>
                                    @elseif($orders->delivery_type == 'pickup')
                                        <p>Pickup Store Address</p>
                                        <p>{{ $orders->store_name }}</p>
                                        <p>{{ $orders->store_address }}</p>
                                        <p>{{ $orders->store_location }}</p>
                                        <p>{{ $orders->store_contact_number }}</p>
                                    @endif

                                </div>

                            </td>
                            <td class="text-right">
                                <strong>Order ID :<b>{{ $orders->order_id }}</b></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row invoice-info">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>

                            </td>

                            <td>

                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Qty</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Total Tax</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                            @php $company_logo_key = array_search('company_logo', array_column($common_settings, 'item')) @endphp
                            @php
                                $sub_total = 0;
                                $grand_total = 0;
                            @endphp

                            @foreach ($orders->order_details as $key => $order)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ $order->product_name }}</td>
                                    <td class="text-right">&#8377; {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->price, 2) }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->total_tax, 2) }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->amount, 2) }}</td>
                                </tr>
                                @php
                                    $sub_total += $order->quantity * $order->price + $order->total_tax;
                                @endphp
                            @endforeach
                            {{-- <tr>
                                <td colspan="3" class="text-right">Sub Total</td>
                                <td class="text-right"><strong>&#8377; 1000</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">TAX (18%)</td>
                                <td class="text-right"><strong>&#8377; 180</strong></td>
                            </tr> --}}

                            <tr>
                                <td colspan="4" class="text-right">Sub Total</td>
                                <td class="text-right"><strong>&#8377; {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right">Total Tax</td>
                                <td class="text-right"><strong>&#8377; {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->total_tax_amount, 2) }}</strong></td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-right">Shipping Fee</td>
                                <td class="text-right"><strong>&#8377;
                                        @if ($orders->shipping_charge > 0)
                                            {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->shipping_charge, 2) }}
                                        @else
                                            Free
                                        @endif
                                    </strong></td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-right">Total Payable</td>
                                <td class="text-right"><strong>&#8377; {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->grand_total, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <br><br><br>
            <div>
                <small><small>NOTE: This is system generate invoice no need of signature</small></small>
            </div>
        </div>
    </div>

</body>

</html>
