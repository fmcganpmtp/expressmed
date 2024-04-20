@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Store</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.stores') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Name:</strong>
                    {{ $data->name }}
                </div>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Description:</strong>
                    {!! $data->description !!}
                </div>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Location:</strong>
                    {{ $data->location }}
                </div>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Address:</strong>
                    {{ $data->address }}
                </div>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Contact Number:</strong>
                    {{ $data->contact_number }}
                </div>
            </div>

            <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong>Map Location:</strong>
                    {!! $data->map_location_code !!}
                </div>
            </div>


        </div>
    </div>
</div>
@endsection
