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
        <h1 class="h3 mb-2 text-gray-800">Brands</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.brands') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

                <form method="post" action="{{ route('brands.update', $data->id) }}" enctype="multipart/form-data">
                    <div class="row">
                        @csrf

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ $data->name }}" autofocus>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div id="image-block">
                                <div class="form-group">
                                    @if ($data->image != '')
                                        <img src="{{ asset('/assets/uploads/brands/' . $data->image) }}" class="img-thumbnail" width="75" />
                                        <a href="javascript:void(0)" onclick="removeImage({{ $data->id }})" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Logo</strong> <span class="text-danger">(Max Image dimension width:150 x height:100 pixel, Max: 1MB)</span>
                                <input type="file" name="image" class="form-control" />
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('footer_scripts')
    <script type="text/javascript">
        function removeImage(id = null){
            if(confirm('Do you want to remove image?')){
                if(id != null){
                    $.ajax({
                        type:'POST',
                        url:'{{ route("brands.removeImage") }}',
                        data:{id: id, '_token':'{{csrf_token()}}'},
                        success:function(response){
                            if(response.result){
                                $('#image-block').replaceWith('<span class="text-success" id="alert_image">Brand image removed successfully.</span>');
                                $('#alert_image').delay(2000).fadeOut();
                                $('#image-block').remove();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                } else {
                    alert('Image remove failed. Something went wrong.');
                }
            }
        }
    </script>
@endsection
