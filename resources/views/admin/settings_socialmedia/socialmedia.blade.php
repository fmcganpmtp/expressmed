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
        <h1 class="h3 mb-2 text-gray-800">Social Media</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    {{-- {{dd($errors)}} --}}
                    <a href="javascript:void(0)" class="btn btn-success btn-circle btn-lg" data-toggle="modal" data-target="#socialcreateModal"><i class="fas fa-plus"></i></a>
                    <div class="modal fade" id="socialcreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Create Social Media</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="{{ route('socialmedia.create') }}" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="col-md-12">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{old('name')}}">
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Link</label>
                                            <input type="text" name="link" class="form-control" value="{{old('link')}}">
                                            <span class="text-danger">{{ $errors->first('link') }}</span>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                            <label>Icon</label><br>
                                            Icon<input type="radio" name="file_type" value="icon" {{ (old('file_type') != '' && old('file_type') == 'icon') ? 'checked' : 'checked' }}>
                                            File<input type="radio" name="file_type" value="image" {{ (old('file_type') != '' && old('file_type') == 'image') ? 'checked' : '' }}>
                                            <input type="text" name="icon" class="form-control texttype"  {{ (old('file_type') != '' && old('file_type') == 'icon') ? '' : (empty(old('file_type')) ? '' : 'style=display:none') }}>
                                            <input type="file" name="icon" class="form-control filetype" {{ (old('file_type') != '' && old('file_type') == 'image') ? '' : (empty(old('file_type')) ? 'style=display:none' : 'style=display:none') }}>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <th>Link</th>
                            <th>Icon</th>
                            <th></th>
                        </tr>
                        @foreach ($social_media as $key => $media_data)
                            <tr>
                                <td>{{ $media_data->name }}</td>
                                <td><a href="{{ $media_data->link }}" target="_blank">{{ $media_data->link }}</a></td>
                                <td>
                                    @if ($media_data->type == 'image')<img src="{{ asset('assets/uploads/socialmedia/' . $media_data->icon) }}" width="50px">@else {!! $media_data->icon !!} @endif
                                </td>
                                <td>
                                    <form action="{{ route('socialmedia.destroy', $media_data->id) }}" method="post">
                                        <button type="button" class="btn btn-warning btn-circle btn-md" data-toggle="modal" data-target="#exampleModal{{ $media_data->id }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete {{ $media_data->name }}?')"><i class="fas fa-trash"></i></button>
                                    </form>

                                </td>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal{{ $media_data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Social Media</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" action="{{ route('socialmedia.update') }}" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    @csrf
                                                    <input type="hidden" name="table_id" value="{{ $media_data->id }}">
                                                    <div class="col-md-12">
                                                        <label>Name</label>
                                                        <input type="text" name="name" value="{{ $media_data->name }}" class="form-control">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Link</label>
                                                        <input type="text" name="link" value="{{ $media_data->link }}" class="form-control">
                                                    </div>
                                                    <br>
                                                    <div class="col-md-12">
                                                        Current Icon:
                                                        @if ($media_data->type == 'image')
                                                            <img src="{{ asset('assets/uploads/socialmedia/') }}/{{ $media_data->icon }}" alt="{{ $media_data->icon }}" width="200px" /><br>
                                                        @else
                                                            {!! $media_data->icon !!}
                                                        @endif
                                                        <br><label>Icon : {{$media_data->type}}</label><br>
                                                        Icon<input type="radio" name="file_type" value="icon" {{ $media_data->type == 'icon' ? 'checked' : '' }}>
                                                        File<input type="radio" name="file_type" value="image" {{ $media_data->type == 'image' ? 'checked' : '' }}>
                                                        <input type="text" name="icon" value="{{ $media_data->type == 'icon' ? $media_data->icon : '' }}" class="form-control texttype" placeholder="" style="{{ $media_data->type == 'icon' ? '' : 'display:none' }}">
                                                        <input type="file" name="icon" class="form-control filetype" style="{{ $media_data->type == 'image' ? '' : 'display:none' }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $("input[type='radio']").change(function() {
                var par_elm = $(this).closest('.modal-body');
                if ($(this).val() == "icon") {
                    par_elm.find('.filetype').hide();
                    par_elm.find('.texttype').show();
                } else {
                    par_elm.find('.filetype').show();
                    par_elm.find('.texttype').hide();
                }
            });
        });
    </script>
@endsection
