@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-12">
                    <div class="card-body">
                        <div class="alert alert-danger text-center">Sorry... Something went wrong. Page not found.</div>

                        @if(count($errors) > 0)
                            {{-- <ul> --}}
                                @foreach($errors->all() as $value)
                                    <h3>{{ $value }}</h3>
                                @endforeach
                            {{-- </ul> --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
