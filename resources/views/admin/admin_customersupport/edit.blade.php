@extends('layouts.admin')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Edit Customer Support</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('customersupport.index') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        {{ csrf_field() }}

                        {{-- @php
                            if(!empty($customersupport)){
                                dd('true');
                            } else {
                                dd('false');
                            }
                        @endphp --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Name</strong> <input type="text" name="name" value="{{ old('name') != '' ? old('name') : (!empty($customersupport) ? $customersupport->name : '') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Email</strong> <input type="email" name="email" value="{{ old('email') != '' ? old('email') : (!empty($customersupport) ? $customersupport->email : '') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Password</strong> <input type="password" name="password" value="{{ old('password') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Phone</strong> <input type="text" name="phone" value="{{ old('phone') != '' ? old('phone') : (!empty($customersupport) ? $customersupport->phone : '') }}" class="form-control">
                            </div>
                        </div>
                        @if ($customersupport->profile_pic != '')
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                    <img src="{{ asset('/assets/uploads/customer_support/') }}/{{ $customersupport->profile_pic }}" alt="{{ $customersupport->profile_pic }}" width="200px" />
                                </div>
                            </div>
                        @endif
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Profile Pic</strong> <input type="file" name="profile_pic" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Status</strong>
                                <select name="status" class="form-control">
                                    <option value="active" {{ $customersupport->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="disabled" {{ $customersupport->status == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary input-lg" value="Update" />
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
@endsection
