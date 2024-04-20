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
    <h1 class="h3 mb-2 text-gray-800">Content Pages</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.contentpages') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Page</strong>
                            <input type="text" name="page" value="{{ old('page') }}"  class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Page Title</strong>
                            <input type="text" name="page_title" value="{{ old('page_title') }}"  class="form-control" >
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Banner/Slider</strong>
                            <select name="choose" class="form-control" id="choose" >
                                <option value="">Choose</option>
                                <option value="slider">Slider</option>
                                <option value="banner">Banner</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8" id="banner_outer" style="display:none">
                        <div class="form-group">
                            <strong>Banner</strong> <input type="file" name="image" id="banner" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8" id="slider_outer" style="display:none">
                        <div class="form-group">
                            <strong>Slider </strong>
                            <select name="slider_title" class="form-control" value="{{ old('slider_title') }}" data-dependent="slider" id="slider">
                            <option value="Null">Select Slider Title</option>
                            @foreach($sliders as $slider_data)
                                <option value='{{ $slider_data->id}}' @if (old('slider_title')==$slider_data->id){{'selected'}} @endif>{{ $slider_data->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Content </strong> <textarea name="content" id="mytextarea" class="form-control" >{{ old('content') }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>SEO Title</strong> <input type="text" name="seo_title" value="{{ old('seo_title') }}"  class="form-control" >
                        </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>SEO Description</strong> <textarea name="seo_description" class="form-control" >{{ old('seo_description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>SEO Keywords </strong> <textarea name="seo_keywords" class="form-control" >{{ old('seo_keywords') }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Check Page Positions</strong>
                            <input type="checkbox" name="chk_position[]" id="chk_position_top" value="Top" {{ (!empty(old('chk_position')) && in_array('Top', old('chk_position'))) ? 'checked' : '' }}><label for="chk_position_top">Top</label>
                            <input type="checkbox" name="chk_position[]" id="chk_position_footer1" value="footer1" {{ (!empty(old('chk_position')) && in_array('footer1', old('chk_position'))) ? 'checked' : '' }}><label for="chk_position_footer1">Footer 1</label>
                            <input type="checkbox" name="chk_position[]" id="chk_position_footer2" value="footer2" {{ (!empty(old('chk_position')) && in_array('footer2', old('chk_position'))) ? 'checked' : '' }}><label for="chk_position_footer2">Footer 2</label>
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
@section('footer_scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.2.2/tinymce.min.js"></script>
<script type="text/javascript">

  $(document).on("change","#choose",function() {
      if($(this).val()=='slider'){
        $("#banner_outer").hide();
        $("#slider_outer").show();
      } else if($(this).val()=='banner') {
        $("#banner_outer").show();
        $("#slider_outer").hide();
      } else {
        $("#banner_outer").hide();
        $("#slider_outer").hide();
      }
  });

    tinymce.init({ selector:'textarea#mytextarea',
    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
    imagetools_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    toolbar_sticky: true,
    autosave_ask_before_unload: true,
    autosave_interval: "30s",
    autosave_prefix: "{path}{query}-{id}-",
    autosave_restore_when_empty: false,
    autosave_retention: "2m",
    image_advtab: true,
    content_css: '//www.tiny.cloud/css/codepen.min.css',
    link_list: [
      { title: 'My page 1', value: 'http://www.tinymce.com' },
      { title: 'My page 2', value: 'http://www.moxiecode.com' }
    ],
    image_list: [
      { title: 'My page 1', value: 'http://www.tinymce.com' },
      { title: 'My page 2', value: 'http://www.moxiecode.com' }
    ],
    image_class_list: [
      { title: 'None', value: '' },
      { title: 'Some class', value: 'class-name' }
    ],
    importcss_append: true,
    height: 400,
    file_picker_callback: function (callback, value, meta) {
      /* Provide file and text for the link dialog */
      if (meta.filetype === 'file') {
        callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
      }

      /* Provide image and alt text for the image dialog */
      if (meta.filetype === 'image') {
        callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
      }

      /* Provide alternative source and posted for the media dialog */
      if (meta.filetype === 'media') {
        callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
      }
    },
    templates: [
          { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
      { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
      { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
    ],
    template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
    template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
    height: 600,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_noneditable_class: "mceNonEditable",
    toolbar_mode: 'sliding',
    contextmenu: "link image imagetools table",
    valid_elements: "*[*]",
    images_upload_handler: function (blobInfo, success, failure) {
    var xhr, formData;
    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', "{{route('content.ajaxtiny')}}");
    xhr.onload = function() {
      var json;

      if (xhr.status != 200) {
      failure('HTTP Error: ' + xhr.status);
      return;
      }
      json = JSON.parse(xhr.responseText);

      if (!json || typeof json.location != 'string') {
      failure('Invalid JSON: ' + xhr.responseText);
      return;
      }
      success(json.location);
    };
    formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
    }
  });

  </script>

@endsection
