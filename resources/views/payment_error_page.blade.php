@extends('layouts.frontview.app')
@section('content')
    @include('layouts.frontview.topmenubar_frontview')
    <section class="invoice-out">
        <article class="width-container outer-width">
            <div class="top-selling-outer">
                <div class="paymaent-res">
                    @if (isset($errors))
                        <div class="alert alert-danger">

                            <h6>{{ $errors }}</h6>

                        </div>
                    @endif
                    <ul>
                        @if ($jwtresponse)
                            <li>Status Description: {{ $jwtresponse->transaction_error_desc }}</li>
                            <li>Amount: RS {{ $jwtresponse->amount }}</li>
                            <li>Date&Time: {{ $jwtresponse->transaction_date }}</li>
                            <li>Order ID: {{ $jwtresponse->orderid }}</li>
                            <li>Payment Method: online payment</li>
                            <li>Transaction ID: {{ $jwtresponse->transactionid }}</li>
                        @endif
                    </ul>
                    <p>Please keep a note of the Transaction ID and Order ID for future reference.</p>
                </div>
            </div>
        </article>
    </section>
@endsection
@section('footer_scripts')
@endsection
