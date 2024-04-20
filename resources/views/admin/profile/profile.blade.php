@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ __('messages.Whoops') }}!</strong> {{ __('messages.There were some problems with your input') }}.<br><br>
        </div>
    @endif
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profile</h1>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
    </div>
	<div class="row">
        <div class="col-lg-6">
            <!-- Default Card Example -->
            <div class="card mb-4">
                <div class="card-header">
                    Profile Informations
                </div>
                <div class="card-body">
                    <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ route('admin.profile') }}" >
                        @csrf
                        @include('admin.profile.editform')
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">Change Password </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.changePassword') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="current_password" autocomplete="current-password">
                                @if($errors->has('current_password'))<span class="text-danger">{{ $errors->first('current_password') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_password" class="col-md-4 col-form-label text-md-right">New Password</label>
                            <div class="col-md-6">
                                <input id="new_password" type="password" class="form-control" name="new_password">
                                @if($errors->has('new_password'))<span class="text-danger">{{ $errors->first('new_password') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="confirm_new_password" class="col-md-4 col-form-label text-md-right">Confirm New Password</label>
                            <div class="col-md-6">
                                <input id="confirm_new_password" type="password" class="form-control" name="confirm_new_password">
                                @if($errors->has('confirm_new_password'))<span class="text-danger">{{ $errors->first('confirm_new_password') }}</span>@endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection
