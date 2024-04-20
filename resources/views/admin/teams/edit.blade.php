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


@if(session('success'))
<div class="alert alert-success">
    <ul>
    <li>{{session('success')}}</li>
    </ul>
</div>
@endif


<div class="container-fluid">
<h1 class="h3 mb-2 text-gray-800">Teams</h1>
{{-- <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec viverra nunc. Vivamus non orci tempus est interdum elementum id feugiat enim.</p> --}}
<div class="card shadow mb-4">
    <div class="card-body">


        <a class="btn btn-info btn-circle btn-lg" href="{{ route('teams.index') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>


<form action="" method="POST" enctype="multipart/form-data">
<div class="row">
{{ csrf_field() }}

<div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Name:</strong>
            <input id="name" type="text" placeholder="Title" class="form-control" name="name" value="{{ $teams->name }}" required autofocus>
        </div>
    </div>


<div class="col-xs-8 col-sm-8 col-md-8">
	<div class="form-group">

     @if($teams->image!='')
    <img src="{{ asset('/assets/uploads/teams/') }}/{{ $teams->image}}" alt="{{ $teams->image}}" width="200px" />
    @endif
    <input id="profile_pic" type="File"   class="course-img" name="profile_pic"   >
    </div>
</div>

<div class="col-xs-8 col-sm-8 col-md-8">
    <div class="form-group">
        <strong>Role:</strong>
        <input id="name" type="text" placeholder="Position" class="form-control" name="position" value="{{ $teams->position }}"  autofocus>
    </div>
</div>


    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>About:</strong>
            <textarea placeholder="Description" class="form-control" name="description" >{{ $teams->description }}</textarea>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Instagram:</strong>
            <input id="name" type="text" placeholder="Instagram" class="form-control" name="instagram" value="{{ $teams->instagram }}"  autofocus>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Facebook:</strong>
            <input id="name" type="text" placeholder="Facebook" class="form-control" name="facebook" value="{{ $teams->facebook }}"  autofocus>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Twitter:</strong>
            <input id="name" type="text" placeholder="Twitter" class="form-control" name="twitter" value="{{ $teams->twitter }}"  autofocus>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>LinkedIn:</strong>
            <input id="name" type="text" placeholder="LinkedIn" class="form-control" name="linkedIn" value="{{ $teams->linkedin }}"  autofocus>
        </div>
    </div>



<div class="col-xs-8 col-sm-8 col-md-8">

            <button type="submit" class="btn btn-info">Update</button>

    </div>

</div>

</div>

                </form>

            </div>

</div>
@endsection
