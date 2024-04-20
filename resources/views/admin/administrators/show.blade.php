@extends('layouts.admin')

@section('content')
<div class="modal" id="view_licence"role="dialog">
    <div class="modal-dialog">

    <div class="modal-content"style="width: 800px;;height: 750px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

      <!-- Modal Header -->
      <div class="modal-body">
        <embed src='{{ asset('assets/uploads/admin_licence/') }}/{{ $admin->licence}}'#toolbar=0 width="100%"height="550px">

      </div>

        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>


    </div>
  </div>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Administrator</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="float-right">
                <a href="{{ route('admin.list') }}" class="btn btn-primary">Accounts</a>
                <a href="{{ route('admin.roles') }}" class="btn btn-primary">Roles</a>
                <a href="{{ route('admin.permissions') }}" class="btn btn-primary">Permissions</a>
            </div>
            <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.list') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $admin->name }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Email:</strong>
                        {{ $admin->email }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Phone:</strong>
                        {{ $admin->phone }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Jobtitle:</strong>
                        {{ $admin->job_title }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Bio:</strong>
                        {{ $admin->bio }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Role:</strong>
                        {{$admin->role}}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        @if($admin->licence!='')

                        <a type="button" class='btn btn-success'data-toggle="modal" data-target="#view_licence"><i class='fa fa-eye'></i> View Licence</a>

                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">

                        @if($admin->profile_pic!='')
                        <strong>Profile Picture:</strong><br>
                            <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic}}" alt="{{ $admin->profile_pic}}" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
