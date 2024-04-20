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

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Sliders</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.sliders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Slider Title</strong> <input type="text" name="slider_title" value="{{ old('slider_title') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="fields_extent">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Image</strong>
                                <div class="input-group control-group increment">
                                    <input type="file" name="image[]" class="form-control">
                                    <div class="input-group-btn">
                                        <button class="btn btn-success" type="button"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Description </strong> <textarea name="description[]" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Image Target </strong> <input type="text" name="image_target[]" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row clone-group" id="clone" style="display: none;">
                        <div class="row clone-group">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Image</strong>
                                    <div class="input-group control-group increment">
                                        <input type="file" name="image[]" class="form-control">
                                        <div class="input-group-btn">
                                            <button class="btn btn-danger" type="button"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description </strong> <textarea name="description[]" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Image Target </strong> <input type="text" name="image_target[]" value="" class="form-control">
                                </div>
                            </div>
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
@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-success").click(function() {
                var html = $("#clone").html();
                $("#fields_extent").append(html);
            });

            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".clone-group").remove();
            });
        });
    </script>
@endsection
