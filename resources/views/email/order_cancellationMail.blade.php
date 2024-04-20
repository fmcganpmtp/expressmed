<h3>{{ $subject }}</h3>
@if ($usertype == 'Admin')
    Hi,
@elseif($usertype == 'Customer')
    Dear {{ $usertype }},
@endif
<br />
{{-- Notification mail content start --}}
@switch($mode)
    @case('Customer_Manageorder')
        <h4>Order ID: {{ $orderid }}</h4>

        @if ($usertype == 'Customer')
            <p>Your order {{ $status }}.</p>

        @elseif($usertype =='Admin')
            <p>Customer successfully {{ $status }} as order from your store.</p>
            <p>Customer name : {{ $customername }}</p>
        @endif
        {{-- <p>Cancelled product is {{ $productname }}.</p> --}}

        <br>
    @break
    @case('Admin_Manageorder')
    <h4>Order ID: {{ $orderid }}</h4>

    @if ($usertype == 'Customer')
        <p>Your order {{ $status }}.</p>
        <p>For Further Details Please Contact Our Customercare.</p>

    @elseif($usertype =='admin')
        <p>successfully {{ $status }} this order.</p>
        <p>Customer name : {{ $customername }}</p>
    @endif
    {{-- <p>Cancelled product is {{ $productname }}.</p> --}}

    <br>
@break

    @default
        <p>Something went wrong, please try again.</p>
@endswitch

<br />

<br />
{{-- Notification mail content start --}}
<p>
    @if ($usertype == 'Admin')
        Thanks
    @elseif($usertype == 'Customer')
        Kind Regards
        <br />
        Expressmed Team
        <br /><br />
        visit our website:<br /><br />
        www.expressmed.in
    @endif
</p>
