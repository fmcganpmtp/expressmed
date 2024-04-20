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
    <h1 class="h3 mb-2 text-gray-800">Sliders</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.sliders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            <form action="" method="POST" enctype="multipart/form-data" >
                @csrf
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Slider Title</strong>  <input type="text" name="slider_title" value="{{$slider_data->name}}" class="form-control" required="">
                        </div>
                    </div>
                </div>
                <div class="row" id="fields_extent" >

                    <div id="myElem"></div>
                    @foreach($slider_images as $images)
                        <div class="row clone-group" id="outer_media_{{$images->id}}">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Image</strong>
                                    <div class="input-group control-group increment" >
                                        @if($images->image!='')<img src="{{ asset('/assets/uploads/sliders/'.$images->image) }}" class="img-thumbnail" width="175" />@endif
                                        <input type="file" name="image[]" class="form-control">
                                        <div class="input-group-btn">
                                        <button class="btn btn-danger delete_ext" type="button" onclick="removeMedia({{$images->id}})"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Title on Image</strong> <input type="text" name="old_title_on_image[]" value="{{$images->title}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description </strong> <textarea name="old_description[]" class="form-control">{{$images->description}}</textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Image Target </strong> <input type="text" name="old_image_target[]" value="{{$images->target}}" class="form-control">
                                </div>
                            </div>
                            <input type="hidden" name="old_image_id[]" value="{{$images->id}}" />
                        </div>
                    @endforeach
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Image</strong>
                            <div class="input-group control-group increment" >
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
                    <div class="row clone-group" >
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Image</strong>
                                <div class="input-group control-group increment" >
                                    <input type="file" name="image[]" class="form-control">
                                    <div class="input-group-btn">
                                        <button class="btn btn-danger delete_new" type="button"><i class="fa fa-times-circle"></i></button>
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
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $(".btn-success").click(function(){
            var html = $("#clone").html();
            $("#fields_extent").append(html);
        });
        $("body").on("click",".delete_new",function(){
            $(this).parents(".clone-group").remove();
        });

    });
    function removeMedia(cid){

       if(cid){
           var outerhtml =  $("#outer_media_"+cid).html();
           $("#outer_media_"+cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
           $.ajax({

                type:"post",
                data: { id: cid, "_token": "{{ csrf_token() }}" },
                url:"{{ route('slider.removeMedia') }}", //Please see the note at the end of the post**
                success:function(res)
                {
                    if(res.ajax_status=='success'){
                        html = '';
                        $("#outer_media_"+cid).html(html);
                        $("#outer_media_"+cid).remove();
                        $("#myElem").html(res.message);
                        $("#myElem").show().delay(3000).fadeOut();

                    }else{
                        $("#outer_media_"+cid).html(outerhtml);
                        $("#myElem").html(res.message);
                        $("#myElem").show().delay(3000).fadeOut();
                    }
                }

            });
       }
   }
</script>
@endsection
