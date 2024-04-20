@extends('layouts.admin')

@section('content')
<div class="container-fluid">
<h1 class="h3 mb-2 text-gray-800">Teams</h1>
{{-- <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec viverra nunc. Vivamus non orci tempus est interdum elementum id feugiat enim.</p> --}}
<div class="card shadow mb-4">
    <div class="card-body">


        <a class="btn btn-info btn-circle btn-lg" href="{{ route('teams.index') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

                   <form action="" method="POST" enctype="multipart/form-data">
                   <div class="row">
                   @csrf
                   <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong> Name</strong> <input type="text" name="name" value="{{ old('name') }}"  class="form-control" required>
                        </div>
                   </div>

                   <div class="col-xs-8 col-sm-8 col-md-8">
                    <div class="form-group">
                        <strong> Role</strong> <input type="text" name="position" value="{{ old('position') }}"  class="form-control" >
                    </div>
               </div>

                   <div class="col-xs-8 col-sm-8 col-md-8">
                    <div class="form-group">
                        <strong>About </strong> <textarea name="description" class="form-control" >{{ old('description') }}</textarea>
                    </div>
               </div>


                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                        <strong>Profile Pic </strong> <input type="file" name="profile_pic" class="form-control">
                        </div>
                   </div>

                   <div class="col-xs-8 col-sm-8 col-md-8">
                    <div class="form-group">
                        <strong> Instagram</strong> <input type="text" name="instagram" value="{{ old('instagram') }}"  class="form-control" >
                    </div>
               </div>


               <div class="col-xs-8 col-sm-8 col-md-8">
                <div class="form-group">
                    <strong> Facebook</strong> <input type="text" name="facebook" value="{{ old('facebook') }}"  class="form-control" >
                </div>
           </div>


           <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="form-group">
                <strong> Twitter</strong> <input type="text" name="twitter" value="{{ old('twitter') }}"  class="form-control" >
            </div>
       </div>


       <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong> LinkedIn</strong> <input type="text" name="linkedin" value="{{ old('linkedin') }}"  class="form-control" >
        </div>
   </div>

                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                   <input type="submit" value="submit" class="btn btn-primary">
                   </div>
                   </div>
                </form>
                </div>
            </div>

    </div>

@endsection
