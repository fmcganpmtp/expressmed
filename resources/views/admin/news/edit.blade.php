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
        <h1 class="h3 mb-2 text-gray-800">News</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.news') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        @csrf

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Title:</strong>
                                <input id="title" type="text" placeholder="Title" class="form-control" name="title" value="{{ $news->title }}" autofocus>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Description:</strong>
                                <textarea placeholder="description" class="form-control" id="mytextarea" name="description">{{ $news->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                @if ($news->image != '')
                                    <img src="{{ asset('/assets/uploads/news/') }}/{{ $news->image }}" alt="{{ $news->profile_pic }}" width="200px" />
                                @endif
                                <input id="image" type="File" class="course-img" name="image">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <h2>Medias</h2>
                                <small>* Video file should be format (*.mp4). Max size 50 MB.<br />* Image file should be format (*.jpeg,*.jpg,*.png,*.svg). Max size 2 MB.</small>
                                <div id="myElem"></div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="row">
                                @if(!empty($media))
                                    @foreach ($media as $items)
                                        <div class="col-xs-3 col-sm-3 col-md-3" id="outer_media_{{ $items->id }}">
                                            <div class="news-desc-img">
                                                @if ($items->type == 'image')
                                                    <img src="{{ asset('assets/uploads/news/gallery/') }}/{{ $items->url }}" alt="{{ $items->url }}" width="200px" />
                                                @endif
                                                @if ($items->type == 'youtube')
                                                <iframe width="100%" height="100%" src="{{ 'https://www.youtube.com/embed/'.$items->url }}"></iframe>
                                                @endif
                                                @if ($items->type == 'video')
                                                    <video width="100%" controls>
                                                        <source src="{{ asset('assets/uploads/news/gallery/') }}/{{ $items->url }}" type="video/mp4">
                                                        <source src="movie.ogg" type="video/ogg">
                                                    </video>
                                                @endif
                                                <a href="javascript:void(0)" onclick="removeMedia({{ $items->id }})" class="btn btn-primary btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 " id="gallery_outer">
                            @php $conuter = 0; @endphp
                            @if (!empty(old('type')))
                                @foreach (old('type') as $type)
                                    @if($type)
                                        <div class="row gallery_outer">
                                            <div class="col-xs-5 col-sm-5 col-md-5">
                                                <strong>Choose Type</strong>
                                                <select class="form-control type_select" name="type[]">
                                                    <option value="">--Select Media Type--</option>
                                                    <option value="image" @if ($type == 'image') {{ 'Selected' }} @endif>Image</option>
                                                    <option value="video" @if ($type == 'video') {{ 'Selected' }} @endif>Video</option>
                                                    <option value="youtube" @if ($type == 'youtube') {{ 'Selected' }} @endif>Youtube</option>
                                                </select>
                                            </div>
                                            <div class="col-xs-7 col-sm-7 col-md-7 file_outer" @if ($type == 'youtube') style="display:none" @endif>
                                                <strong>File</strong>
                                                <input type="file" name="file_gallery[]" class="form-control" />
                                            </div>
                                            <div class="col-xs-7 col-sm-7 col-md-7 url_outer" @if ($type != 'youtube') style="display:none" @endif>
                                                <strong>Url</strong> <i>(Open Youtube video->click on share->copy url. (eg: https://youtu.be/xxx)</i>
                                                <input type="text" name="txt_url[]" class="form-control" value="{{ old('txt_url')[$conuter] }}" />
                                            </div>
                                        </div>
                                    @endif
                                    @php $conuter++; @endphp
                                @endforeach
                            @else
                                <div class="row gallery_outer">
                                    <div class="col-xs-5 col-sm-5 col-md-5">
                                        <strong>Choose Type</strong>
                                        <select class="form-control type_select" name="type[]">
                                            <option value="">--Select Media Type--</option>
                                            <option value="image" @if (old('type') == 'image') {{ 'Selected' }} @endif>Image</option>
                                            <option value="video" @if (old('type') == 'video') {{ 'Selected' }} @endif>Video</option>
                                            <option value="youtube" @if (old('type') == 'youtube') {{ 'Selected' }} @endif>Youtube</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-7 col-sm-7 col-md-7 file_outer" style="display:none">
                                        <strong>File</strong>
                                        <input type="file" name="file_gallery[]" class="form-control" />
                                    </div>
                                    <div class="col-xs-7 col-sm-7 col-md-7 url_outer" style="display:none">
                                        <strong>Url</strong> <i>(Open Youtube video->click on share->copy url. (eg: https://youtu.be/xxx)</i>
                                        <input type="text" name="txt_url[]" class="form-control" value="" />
                                    </div>
                                </div>
                            @endif
                            <a class="btn btn-primary btn-circle btn-md" id="addmoregallery" href="javascript:void(0)"> <i class="fa fa-plus" aria-hidden="true"></i></a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.2.2/tinymce.min.js"></script>

    <script>
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
            html += '<div class="col-xs-5 col-sm-5 col-md-5">';
            html += '<strong>Choose Type</strong>';
            html += '<select class="form-control type_select" name="type[]">';
            html += '<option value="image">Image</option>';
            html += '<option value="video">Video</option>';
            html += '<option value="youtube">Youtube</option>';
            html += '</select>';
            html += '</div>';
            html += '<div class="col-xs-7 col-sm-7 col-md-7 file_outer" >';
            html += '<strong>File</strong>';
            html += '<input type="file" name="file_gallery[]" class="form-control" />';
            html += '</div>';
            html += '<div class="col-xs-7 col-sm-7 col-md-7 url_outer"  style="display:none">';
            html += '<strong>Url</strong> <i>(Open Youtube video->click on share->copy url. (eg: https://youtu.be/xxx)</i>';
            html += '<input type="text" name="txt_url[]" class="form-control" value="" />';
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

        $(document).on('change', '.type_select', function(e) {
            if($(this).val() != ''){
                if ($(this).val() == 'youtube') {
                    $(this).closest('div.gallery_outer').children('.file_outer').hide();
                    $(this).closest('div.gallery_outer').children('.url_outer').show();
                } else {
                    $(this).closest('div.gallery_outer').children('.url_outer').hide();
                    $(this).closest('div.gallery_outer').children('.file_outer').show();
                }
            } else {
                $(this).closest('div.gallery_outer').children('.url_outer').hide();
                $(this).closest('div.gallery_outer').children('.file_outer').hide();
            }
        });

        function removeMedia(cid) {
            if(confirm("Are you sure do you want to delete?")){
                if (cid) {
                    var outerhtml = $("#outer_media_" + cid).html();
                    $("#outer_media_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')

                    $.ajax({
                        type: "post",
                        data: {
                            id: cid,
                            "_token": "{{ csrf_token() }}"
                        },
                        url: "{{ route('news.removeMedia') }}",
                        success: function(res) {
                            if (res.ajax_status == 'success') {
                                html = '';
                                $("#outer_media_" + cid).html(html);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else {
                                $("#outer_media_" + cid).html(outerhtml);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection
