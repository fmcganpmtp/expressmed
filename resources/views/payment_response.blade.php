@extends('layouts.frontview.app')
@section('content')
    {{-- @include('layouts.frontview.topmenubar_frontview') --}}
    <section class="invoice-out">
        <article class="width-container outer-width">
            <div class="top-selling-outer">
                <div class="paymaent-res">
                    <h3>{{ $msg }}</h3>
                </div>
            </div>
        </article>
    </section>
@endsection
@section('footer_scripts')
    <script>

setTimeout(function() {
                window.close();
        }, 500);
    </script>
@endsection
