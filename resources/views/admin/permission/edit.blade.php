@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Edit Permission</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.permissions') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

            <form class="form-horizontal" method="POST" action="{{ route('permissions.edit', $permission->id) }}" enctype="multipart/form-data">
                @csrf
                @include('admin.permission.form')
            </form>
        </div>
    </div>
</div>
@endsection
