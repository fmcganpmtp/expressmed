@extends('layouts.admin')

@section('content')

@if(count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Testimonials</h1>
    <div class="card shadow mb-4">
        <div class="card-body">

            <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.testimonials') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    @csrf
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Name</strong> <input type="text" name="name" value="{{ old('name') }}"  class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Company</strong> <input type="text" name="company" value="{{ old('company') }}" class="form-control" >
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Title</strong> <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Profile Pic</strong> <input type="file" name="profile_pic" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Comments</strong> <textarea name="comments" class="form-control">{{ old('comments') }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <input type="submit" value="submit" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
