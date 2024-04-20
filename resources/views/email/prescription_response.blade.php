<h2>{{ ($status_mode == 'approve' ? 'Your prescription approved' : 'Your prescription rejected') }}</h2>
<h4>Dear {{ $customername }},</h4> <br/>
@if($status_mode == 'approve')
    <p>Your prescription approved you may buy the product <u>{{ $productname }}</u>.</p>

    <p>Product Name : <strong>{{ $productname }}</strong></p>
    <p>Allowed Quantity : <strong>{{ $quantity }}</strong></p>
    <p>Amount : <strong>{{'Rs. '.$productprice }} (exclude all tax rate)</strong></p>

    {{-- <a href="{{ $link }}" target="_blank">Please click the link for purchase</a> --}}
@else
    <p>Your prescription rejected you cannot purchase product <u>{{ $productname }}</u>. Something issue for your prescription please check the valuable prescription and upload again.</p>
@endif

<br/><br/>
kind Regards
@if($adminname != 'Super Admin')
    <br>
    System Admin : {{ $adminname }}
@endif
<br/><br>
Expressmed Team
<br/>
www.expressmed.in
