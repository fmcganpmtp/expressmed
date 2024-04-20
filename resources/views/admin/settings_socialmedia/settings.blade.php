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
        <h1 class="h3 mb-2 text-gray-800">General Settings </h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form class="form-horizontal" method="POST" action="{{ route('admin.settings') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        @foreach ($settings as $row)

                            @if ($row->item == 'company_logo')
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong>{{ $row->display_name }}</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-6">
                                            @if($row->value != '')
                                                <div class="image-block">
                                                    <img src="{{ asset('assets/uploads/logo/') }}/{{ $row->value }}" alt="{{ $row->value }}" width="200px" />
                                                    <a href="javascript:void(0)" onclick="removeImage({{ $row->id }}, this)" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                                </div>
                                            @endif
                                            <input type="file" name="{{ $row->item }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            @elseif($row->item == 'footer_logo')
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong>{{ $row->display_name }}</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-6">
                                            @if($row->value != '')
                                                <div class="image-block">
                                                    <img src="{{ asset('assets/uploads/logo/') }}/{{ $row->value }}" alt="{{ $row->value }}" width="200px" />
                                                    <a href="javascript:void(0)" onclick="removeImage({{ $row->id }}, this)" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                                </div>
                                            @endif
                                            <input type="file" name="{{ $row->item }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                @elseif($row->item =='company_address')
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>{{ $row->display_name }}:</strong>
                                        <textarea id="{{ $row->item }}" placeholder="{{ $row->display_name }}" class="form-control" name="{{ $row->item }}">{{ $row->value }}</textarea>
                                    </div>
                                </div>
                            @else
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>{{ $row->display_name }}:</strong>
                                        <input id="{{ $row->item }}" type="text" placeholder="{{ $row->display_name }}" class="form-control" name="{{ $row->item }}" value="{{ (old($row->item) != '' ? old($row->item) : $row->value) }}">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        function removeImage(id = null, elm){
            if(confirm('Do you want to remove image?')){
                if(id != null){
                    var imgBlock = elm.closest('.image-block');
                    $.ajax({
                        type:'POST',
                        url:'{{ route("admin.settings.removeImage") }}',
                        data:{id: id, '_token':'{{csrf_token()}}'},
                        success:function(response){
                            if(response.result){
                                $(imgBlock).parent().prepend('<span class="text-success" id="alert_image">Image removed successfully. You can add new.</span>');
                                $('#alert_image').delay(3000).fadeOut();
                                imgBlock.remove();
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
