@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Edit Role</h1>
    <div class="card shadow mb-4">
        <div class="card-body">


            <div class="float-right">
                <a href="{{ route('admin.list') }}" class="btn btn-primary">Accounts</a>
                <a href="{{ route('admin.roles') }}" class="btn btn-primary">Roles</a>
                <a href="{{ route('admin.permissions') }}" class="btn btn-primary">Permissions</a>
            </div>
            <div class="pull-right">
                <br/>
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.roles') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            </div>

            <form class="form-horizontal" method="POST" action="{{ route('roles.edit', $role->id) }}">
                @include('admin.role.form')
            </form>
        </div>

    </div>
</div>
@endsection

