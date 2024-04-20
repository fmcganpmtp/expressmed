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
        <h1 class="h3 mb-2 text-gray-800">Content Pages</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.contentpages') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Page</strong> <input type="text" name="page" value="{{ $contents->page }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Page Title</strong> <input type="text" name="page_title" value="{{ $contents->title }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Banner/Slider</strong>
                                <select name="choose" class="form-control" id="choose">
                                    <option value="">Choose</option>
                                    <option value="slider" @if ($contents->banner_type == 'slider'){{ 'selected' }}@endif>Slider</option>
                                    <option value="banner" @if ($contents->banner_type == 'banner'){{ 'selected' }}@endif>Banner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8" id="banner_outer" @if ($contents->banner_type != 'banner')style="display:none"@endif>
                            <div class="form-group">
                                @if ($contents->banner != '')
                                    <div id="image-block">
                                        <img src="{{ asset('/assets/uploads/contents/' . $contents->banner) }}" class="img-thumbnail" width="175" />
                                        <a href="javascript:void(0)" onclick="removeImage({{ $contents->id }})" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                    </div>
                                @endif
                                <strong>Banner</strong>
                                <input type="file" name="image" id="banner" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8" id="slider_outer" @if ($contents->banner_type != 'slider')style="display:none"@endif>
                            <div class="form-group">
                                <strong>Slider </strong><select name="slider_title" class="form-control" data-dependent="slider" id="slider">
                                    <option value="Null">Select Slider Title</option>
                                    @foreach ($sliders as $slider_data)
                                        <option value='{{ $slider_data->id }}' @if ($contents->slider == $slider_data->id){{ 'selected' }} @endif>{{ $slider_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Content </strong> <textarea name="content" id="mytextarea" class="form-control">{{ $contents->page_content }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>SEO Title</strong> <input type="text" name="seo_title" value="{{ $contents->seo_title }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>SEO Description</strong> <textarea name="seo_description" class="form-control">{{ $contents->seo_description }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>SEO Keywords </strong> <textarea name="seo_keywords" class="form-control">{{ $contents->seo_keywords }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Check Page Positions</strong>
                                @php
                                    $position_array = [];
                                    $position_array = explode(',', $contents->page_position);
                                @endphp
                                <input type="checkbox" name="chk_position[]" id="chk_position_top" value="Top" @if (in_array('Top', $position_array)) checked @endif>Top
                                <input type="checkbox" name="chk_position[]" id="chk_position_footer1" value="footer1" @if (in_array('footer1', $position_array)) checked @endif>Footer 1
                                <input type="checkbox" name="chk_position[]" id="chk_position_footer2" value="footer2" @if (in_array('footer2', $position_array)) checked @endif>Footer 2
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <input type="submit" value="Update" class="btn btn-primary">
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
        $(document).on("change", "#choose", function() {
            if ($(this).val() == 'slider') {
                $("#banner_outer").hide();
                $("#slider_outer").show();
            } else if ($(this).val() == 'banner') {
                $("#banner_outer").show();
                $("#slider_outer").hide();
            } else {
                $("#banner_outer").hide();
                $("#slider_outer").hide();
            }
        });

        tinymce.init({
            selector: 'textarea#mytextarea',
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
            link_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function(callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', {
                        text: 'My text'
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', {
                        alt: 'My alt text'
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                    callback('movie.mp4', {
                        source2: 'alt.ogg',
                        poster: 'https://www.google.com/logos/google.jpg'
                    });
                }
            },
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
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
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('content.ajaxtiny') }}");
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

        $(document).on('click', '#addmoregallery', function(e) {
            var html = '';
            html += '<div class="row gallery_outer">';
            html += '<div class="col-xs-5 col-sm-5 col-md-5 file_outer" >';
            html += '<strong>File</strong>';
            html += '<input type="file" name="file_gallery[]" class="form-control" />';
            html += '</div>';
            html += '<div class="col-xs-2 col-sm-2 col-md-2" >';
            html += '<a class="btn btn-danger btn-circle btn-md removemoregallery"  href="javascript:void(0)" > <i class="far fa-times-circle"></i></a>';
            html += '</div>';
            html += '</div>';
            $("#gallery_outer").append(html);
        });

        $(document).on('click', '.removemoregallery', function(e) {
            $(this).closest('div.gallery_outer').remove();
        });

        function removeImage(id = null){
            if(confirm('Do you want to remove image?')){
                if(id != null){
                    $.ajax({
                        type:'POST',
                        url:'{{ route("contentpages.removeImage") }}',
                        data:{id: id, '_token':'{{csrf_token()}}'},
                        success:function(response){
                            if(response.result){
                                $('#image-block').parent().prepend('<div class="text-success" id="alert_image">'+(response.message)+'</div>');
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
