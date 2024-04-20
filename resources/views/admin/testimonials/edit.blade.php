@extends('layouts.admin')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong>
            {{ _('messages.There were some problems with your input') }}.<br><br>
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
        <h1 class="h3 mb-2 text-gray-800">Testimonial</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.testimonials') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        {{ csrf_field() }}
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ $testimonials->name }}" required autofocus>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Company:</strong>
                                <input id="company" type="text" placeholder="Company" class="form-control" name="company" value="{{ $testimonials->company_name }}">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Title:</strong>
                                <input id="title" type="text" placeholder="Title" class="form-control" name="title" value="{{ $testimonials->title }}">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div id="image-block">
                                @if ($testimonials->profile_pic != '')
                                    <img src="{{ asset('/assets/uploads/testimonials/') }}/{{ $testimonials->profile_pic }}" alt="{{ $testimonials->profile_pic }}" width="200px" />
                                    <a href="javascript:void(0)" onclick="removeImage({{ $testimonials->id }})" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                @endif
                            </div>
                            <div class="form-group">
                                <input id="profile_pic" type="File" class="course-img" name="profile_pic">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Comments:</strong>
                                <textarea placeholder="Comments" class="form-control" name="comments">{{ $testimonials->comments }}</textarea>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        function removeImage(id = null) {
            if (confirm('Do you want to remove image?')) {
                if (id != null) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('testimonials.removeImage') }}',
                        data: {
                            id: id,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.result) {
                                $('#image-block').parent().prepend('<span class="text-success" id="alert_image">Profile picture removed successfully. You can add new.</span>');
                                $('#alert_image').delay(2000).fadeOut();
                                $('#image-block').remove();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                } else {
                    alert('failed. Something went wrong.');
                }
            }
        }
    </script>
@endsection
