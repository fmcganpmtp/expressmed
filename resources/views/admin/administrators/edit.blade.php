@extends('layouts.admin')

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        <ul>
            <li>{{session('error')}}</li>
        </ul>
    </div>
@endif
<div class="container-fluid">
<h1 class="h3 mb-2 text-gray-800">Edit Administrator</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="float-right">
            <a href="{{ route('admin.list') }}" class="btn btn-primary">Accounts</a>
            <a href="{{ route('admin.roles') }}" class="btn btn-primary">Roles</a>
            <a href="{{ route('admin.permissions') }}" class="btn btn-primary">Permissions</a>
        </div>
    <div class="table-responsive">
    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.list') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

    <form class="form-horizontal" method="POST" action="{{ route('admin.edit', $admin->id) }}"  enctype="multipart/form-data" >
        @include('admin.administrators.editform2')
    </form>

@endsection
