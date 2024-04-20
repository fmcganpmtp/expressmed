<html>

<head>
    <title>Invoice Print</title>
</head>
<link href="{{ asset('front_view/css/font-awesome.min.css') }}" type="text/css" rel="stylesheet">

<body>
    @if (!empty($orders))
        <div>
            <div>
                <div class="invoice-print">
                    @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')); @endphp
                    @php $company_logo_key = array_search('company_logo', array_column($common_settings, 'item'));  @endphp
                    <div class="invoice-logo">
                        <img src="{{ asset('/assets/uploads/logo/' . $common_settings[$company_logo_key]['value']) }}" style="height:70px" class="img-responsive" alt="" title="company logo" />
                    </div>
                    <h3 style="text-center">Invoice Details</h3>
                    <p><strong style="float-right">Order ID :{{ $orders->order_id }}</strong></p>
                    <p><strong style="float-right">Date : {{ date('d-m-Y h:ia', strtotime($orders->order_date)) }}</strong></p>
                    <p><strong>{{ $orders->name }}</strong></p>
                    @if($orders->delivery_type=="direct")
                    <p><strong>Delivery Address</strong></p>
                    <p>Address: {{ $orders->address }}</p>
                    <p>Phone: {{ $orders->phone }}</p>
                    <p>Email: {{ $orders->email }}</p>
                    <p>Pin: {{ $orders->pin }}</p>
                    <p>City: {{ $orders->city }}</p>
                    <p>Location: {{ $orders->location }}</p>
                    <p>Landmark: {{ $orders->landmark }}</p>
                    <p>State: {{ $orders->state_name }}</p>
                    <p>Country: {{ $orders->country_name }}</p>
                    @elseif($orders->delivery_type=="pickup")
                    <p><strong>Pickup Store Address</strong></p>
                    <p>Name: {{ $orders->store_name }}</p>
                    <p>Address: {{ $orders->store_address }}</p>
                    <p>Location: {{ $orders->store_location }}</p>
                    <p>Contact Number: {{ $orders->store_contact_number }}</p>
                    @endif
                    <table class="table" width="100%" border="1" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price Per Item</th>
                                <th>Tax Per Item</th>
                                <th>Total Amount(Including Tax)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sub_total = 0;
                                $grand_total = 0;
                            @endphp

                            @foreach ($orders->order_details as $key => $order)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        {{-- @if ($order->product_image)
                                                <img src="{{asset('assets/uploads/products/'.$order->product_image)}}" width="50px">
                                            @else
                                                <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" style="width: 50px"/>
                                            @endif --}}
                                        {{ $order->product_name }}
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td> {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->price, 2) }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($order->total_tax/$order->quantity, 2) }}</td>
                                    <td> {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->amount, 2) }}</td>
                                </tr>
                                @php
                                    $sub_total += $order->quantity * $order->price + $order->total_tax;
                                @endphp
                            @endforeach

                        </tbody>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>
                                <h5>Sub Total(Excluding Tax)</h5>
                            </td>
                            <td>
                                <h5>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format(($orders->total_amount), 2) }}</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>
                                <h5>Total Tax</h5>
                            </td>
                            <td>
                                <h5>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format(($orders->total_tax_amount), 2) }}</h5>
                            </td>
                        </tr>
                        @if ($userType == 'customer')

                            <tr>
                                <td colspan="4"></td>
                                <td>
                                    <h5>Shipping Fee</h5>
                                </td>
                                <td>
                                    @if ($orders->shipping_charge > 0)
                                        {!! $common_settings[$currency_icon]['value'] !!}{{number_format($orders->shipping_charge,2) }}
                                    @else
                                        Free
                                    @endif
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td class="text-success">
                                    <h4>Grand Total</h4>
                                </td>
                                <td class="text-success">
                                    <h4>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orders->grand_total, 2) }}</h4>
                                </td>
                            </tr>
                        @endif

                    </table>
                </div>
            </div>
        </div>
    @else
        <h2>Error: Something went wrong</h2>
        @if (isset($error))
            <div class="alert alert-danger">
                <ul>
                    <li>{{ $error }}</li>
                </ul>
            </div>
        @endif
    @endif
</body>
<script src="{{ asset('front_view/js/jquery.min.js') }}"></script>
<script src="{{ asset('front_view/js/fontawesome.js') }}"></script>
<script>
    $(document).ready(function() {
        window.print();
        // window.close();
        return false;
    });
</script>

</html>
