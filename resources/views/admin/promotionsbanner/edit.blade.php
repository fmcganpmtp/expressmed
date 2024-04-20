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
        <h1 class="h3 mb-2 text-gray-800">Promotion Banner</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.promotionbanner') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Banner Title</strong>
                                <input type="text" name="bannertitle" class="form-control" placeholder="Banner Title" value="{{ old('bannertitle') != '' ? old('bannertitle') : (!empty($promotionbannerdetails) && $promotionbannerdetails->title != '' ? $promotionbannerdetails->title : '') }}">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Status</strong>
                                <select name="status" class="form-control">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : (!empty($promotionbannerdetails) && $promotionbannerdetails->status == 'active' ? 'selected' : '') }}>Active</option>
                                    <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : (!empty($promotionbannerdetails) && $promotionbannerdetails->status == 'disabled' ? 'selected' : '') }}>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Banner Section</strong>
                                <select name="bannersection" id="bannersection" class="form-control" disabled>
                                    <option value="">--Choose Banner Section--</option>
                                    <option value="mainbody" {{ old('bannersection') == 'mainbody' ? 'selected' : (!empty($promotionbannerdetails) && $promotionbannerdetails->section == 'mainbody' ? 'selected' : '') }}>Main Body</option>
                                    <option value="sidebar" {{ old('bannersection') == 'sidebar' ? 'selected' : (!empty($promotionbannerdetails) && $promotionbannerdetails->section == 'sidebar' ? 'selected' : '') }}>Sidebar</option>
                                    <option value="mobile" {{ old('bannersection') == 'mobile' ? 'selected' : (!empty($promotionbannerdetails) && $promotionbannerdetails->section == 'mobile' ? 'selected' : '') }}>Mobile</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Position</strong>
                                <select name="bannerposition" id="bannerposition" class="form-control" disabled>
                                    @if (!empty($promotionbannerdetails))
                                        @if ($promotionbannerdetails->section == 'mainbody')
                                            <option value="0">--Choose Position--</option>
                                            <option value="maintop" {{ $promotionbannerdetails->position == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                            <option value="middle" {{ $promotionbannerdetails->position == 'middle' ? 'selected' : '' }}>Middle</option>
                                            <option value="footer" {{ $promotionbannerdetails->position == 'footer' ? 'selected' : '' }}>Bottom1</option>
                                            <option value="footer2" {{ $promotionbannerdetails->position == 'footer2' ? 'selected' : '' }}>Bottom2</option>
                                            <option value="footer3" {{ $promotionbannerdetails->position == 'footer3' ? 'selected' : '' }}>Bottom3</option>
                                            <option value="footer4" {{ $promotionbannerdetails->position == 'footer4' ? 'selected' : '' }}>Bottom4</option>
                                        @elseif ($promotionbannerdetails->section == 'sidebar')
                                            <option value="0">--Choose Position--</option>
                                            <option value="top" {{ $promotionbannerdetails->position == 'top' ? 'selected' : '' }}>Sidebar Top</option>
                                            <option value="top2" {{ $promotionbannerdetails->position == 'top2' ? 'selected' : '' }}>Sidebar Top 2</option>
                                            <option value="top3" {{ $promotionbannerdetails->position == 'top3' ? 'selected' : '' }}>Sidebar Top 3</option>

                                            <option value="bottom" {{ $promotionbannerdetails->position == 'bottom' ? 'selected' : '' }}>Product Details-Sidebar 1</option>
                                            <option value="bottom2" {{ $promotionbannerdetails->position == 'bottom2' ? 'selected' : '' }}>Product Details-Sidebar 2</option>
                                        @elseif ($promotionbannerdetails->section == 'mobile')
                                            <option value="0">--Choose Position--</option>
                                            <option value="maintop" {{ $promotionbannerdetails->position == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                            <option value="middle" {{ $promotionbannerdetails->position == 'middle' ? 'selected' : '' }}>Middle</option>
                                            <option value="footer" {{ $promotionbannerdetails->position == 'footer' ? 'selected' : '' }}>Bottom</option>
                                        @endif
                                    @else
                                        <option value="">--Choose Banner section--</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Type</strong>
                                <input type="hidden" name="bannertype" id="hid_bannertype" value="{{ old('bannertype') != '' ? old('bannertype') : (!empty($promotionbannerdetails) && $promotionbannerdetails->type != '' ? $promotionbannerdetails->type : '') }}">
                                <input type="text" id="bannertype" class="form-control" value="{{ !empty($promotionbannerdetails) && $promotionbannerdetails->type == 'slider' ? 'Add multiple images for slider' : (!empty($promotionbannerdetails) && $promotionbannerdetails->type == 'plain' ? 'Only one image allowed' : 'Choose any position') }}" readonly>
                            </div>
                        </div>
                        {{-- {{dd($BannerImages)}} --}}
                        @if ($BannerImages->isNotEmpty())
                            <div class="col-xs-12 col-sm-12 col-md-8" id="section_image_block">
                                <strong>Banner Image</strong>
                                <div class="row">
                                    @foreach ($BannerImages as $bannerimage_row)
                                        <div class="col-xs-12 col-sm-4 col-md-4 image_block">
                                            <img src="{{ asset('assets/uploads/promotionbanner/') . '/' . $bannerimage_row->image }}" style="width: 250px">
                                            <div class="show_bannerurl">
                                                <a href="{{ $bannerimage_row->banner_url != '' ? $bannerimage_row->banner_url : '' }}" {{ $bannerimage_row->banner_url != '' ? 'target="_blank"' : '' }}>{{ $bannerimage_row->banner_url != '' ? $bannerimage_row->banner_url : 'no_url' }}</a>
                                                <a href="javascript:void(0)" class="btn_editurl"><i class="fas fa-pencil-alt"></i></a>
                                            </div>
                                            {{-- @if ($bannerimage_row->banner_url != '') --}}
                                            <div class="input-group edit_bannerurl" style="display: none">
                                                <input type="text" class="form-control" value="{{ $bannerimage_row->banner_url }}">
                                                <span class="input-group-btn">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default text-primary" onclick="updateBannerUrl({{ $bannerimage_row->id . ',' . $bannerimage_row->promotionbanner_id }}, this)"><i class="fas fa-save"></i></a></button>
                                                        <button type="button" class="btn btn-default text-danger close_editurl"><i class="fas fa-times"></i></a></button>
                                                    </div>
                                                </span>
                                            </div>
                                            {{-- @endif --}}
                                            <div>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md removebannerimage" onclick="removeimage({{ $bannerimage_row->id . ',' . $bannerimage_row->promotionbanner_id }},this)" title="Remove image & url"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!empty($promotionbannerdetails))
                            <div class="col-xs-12 col-sm-12 col-md-8" id="image_upload" style="{{ ($promotionbannerdetails->type == 'plain' && $BannerImages->isEmpty()) || $promotionbannerdetails->type == 'slider' ? '' : 'display: none' }}">
                                <div class="form-group">
                                    <strong>Upload Image </strong><span class="text-danger" id="imageinfo"></span>
                                    <div class="input-group">
                                        <input type="file" name="bannerimage[]" class="form-control">
                                        <span>&nbsp;</span>
                                        <a class="btn btn-primary btn-circle btn-md" id="addmoreimage" href="javascript:void(0)" title="Add more images" {{ !empty($promotionbannerdetails) && $promotionbannerdetails->type == 'plain' ? 'style=display:none' : '' }}> <i class="fa fa-plus" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <strong>Banner URL</strong> eg:(http://url.com)
                                    <input type="text" name="bannerurl[]" class="form-control" value="{{ old('bannerurl.0') }}" placeholder="http://url.com">
                                </div>
                            </div>
                        @endif

                        <div id="div_bannerimage" class="col-xs-12 col-sm-12 col-md-8">
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <input type="submit" value="Update" class="btn btn-primary">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8 alert alert-success" id="alert_msg" style="display: none"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(document).on('change', '#bannersection', function() {
            var section = $(this).val();
            $('#bannertype').val('Choose any position');
            $('#div_bannerimage').html('');

            var ban_position_option = '';
            if (section == 'mainbody') {
                ban_position_option = '<option value="0">--Choose Position--</option>';
                ban_position_option += '<option value="maintop">Top Main Banner</option>';
                ban_position_option += '<option value="middle">Middle</option>';
                ban_position_option += '<option value="footer">Bottom</option>';
                ban_position_option += '<option value="footer2">Bottom2</option>';
                ban_position_option += '<option value="footer3">Bottom3</option>';
                ban_position_option += '<option value="footer4">Bottom4</option>';
                $('#bannerposition').focus();
            } else if (section == 'sidebar') {
                ban_position_option = '<option value="0">--Choose Position--</option>';
                ban_position_option += '<option value="top">Sidebar Top</option>';
                ban_position_option += '<option value="top2">Sidebar Top 2</option>';
                ban_position_option += '<option value="top3">Sidebar Top 3</option>';
                ban_position_option += '<option value="bottom">Product Details-Sidebar 1</option>';
                ban_position_option += '<option value="bottom2">Product Details-Sidebar 2</option>';
                $('#bannerposition').focus();
            } else if (section == 'mobile') {
                ban_position_option = '<option value="0">--Choose Position--</option>';
                ban_position_option += '<option value="maintop">Top Main Banner</option>';
                ban_position_option += '<option value="middle">Middle</option>';
                ban_position_option += '<option value="footer">Bottom</option>';
                $('#bannerposition').focus();
            } else {
                ban_position_option = '<option value="0">--Choose Banner section--</option>';
            }
            $('#bannerposition').html(ban_position_option);
        });

        $(document).ready(function() {
            var position = '{{ $promotionbannerdetails->position }}';
            $('#bannertype').val('Choose any position');
            $('#div_bannerimage').html('');

            switch (position) {
                case 'maintop':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');
                    $('#imageinfo').text('(Image dimension width:1081 x height:550 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'middle':
                    $('#hid_bannertype').val('plain');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:810 x height:340 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer':
                    $('#hid_bannertype').val('plain');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer2':
                    $('#hid_bannertype').val('plain');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer3':
                    $('#hid_bannertype').val('plain');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer4':
                    $('#hid_bannertype').val('plain');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;


                case 'top':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');
                    $('#imageinfo').text('(Image dimension width:330 x height:387 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'top2':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');
                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'top3':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Only one image allowed');
                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'bottom':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');
                    $('#imageinfo').text('(Image dimension width:330 x height:440 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'bottom2':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');
                    $('#imageinfo').text('(Image dimension width:330 x height:308 pixel)');
                    $('#addmoreimage').show();
                    break;
                default:
                    $('#bannertype').val('Choose any position');
            }
        });

        $(document).on('click', '#addmoreimage', function() {
            var html = '<div class="ban_imagerow">';
            html += '<div class="form-group">';
            html += '<div class="input-group">';
            html += '<input type="file" name="bannerimage[]" class="form-control">';
            html += '<span>&nbsp;</span>';
            html += '<a class="btn btn-danger btn-circle btn-md removebannerimage"  href="javascript:void(0)"><i class="far fa-times-circle"></i></a>';
            html += '</div>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<strong>Banner URL</strong> eg:(http://url.com)';
            html += '<input type="text" name="bannerurl[]" class="form-control" placeholder="http://url.com">';
            html += '</div>';
            html += '</div>';

            $('#div_bannerimage').append(html);
        });

        $(document).on('click', '.removebannerimage', function() {
            $(this).closest('.ban_imagerow').remove();
        });

        $(document).on('click', '.btn_editurl', function() {
            $(this).closest('.show_bannerurl').hide();
            $(this).closest('.image_block').find('.edit_bannerurl').show();

            $(this).closest('.image_block').siblings().find('.edit_bannerurl').hide();
            $(this).closest('.image_block').siblings().find('.show_bannerurl').show();
        });

        $(document).on('click', '.close_editurl', function() {
            $(this).closest('.edit_bannerurl').hide();
            $(this).closest('.image_block').find('.show_bannerurl').show();
        });

        function updateBannerUrl(id = null, bannerId = null, elm) {
            var parentElm = $(elm).closest('.edit_bannerurl');
            if (id != null && bannerId != null) {
                var urlValue = $(parentElm).find('input').val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('promotionbanner.updateurl') }}',
                    data: {
                        id: id,
                        bannerId: bannerId,
                        urlValue: urlValue,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result) {
                            $(parentElm).hide();

                            var html = '<a href="' + urlValue + '" target="_blank">' + urlValue + '</a><a href="javascript:void(0)" class="btn_editurl"><i class="fas fa-pencil-alt"></i></a>';

                            $(elm).closest('.image_block').find('.show_bannerurl').html(html);
                            $(parentElm).find('input').val(urlValue);
                            $(elm).closest('.image_block').find('.show_bannerurl').show();

                            $('#alert_msg').text(response.message);
                            $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-success').removeClass('alert-danger');
                        } else {
                            $('#alert_msg').text(response.message);
                            $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-danger').removeClass('alert-success');
                        }
                    }
                });
            } else {
                $('#alert_msg').text('Sorry... Something went wrong.');
                $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-danger').removeClass('alert-success');
            }
        }

        function removeimage(id = null, bannerid = null, elm) {
            var secBlock = elm.closest('#section_image_block');
            if (confirm('Are you sure do you want to remove the image and url both?')) {
                if (id != null && bannerid != null) {
                    $.ajax({
                        type: 'post',
                        data: {
                            id: id,
                            bannerid: bannerid,
                            '_token': '{{ csrf_token() }}'
                        },
                        url: '{{ route('promotionbanner.removeimage') }}',
                        success: function(response) {
                            if (response.result == 'success') {
                                elm.closest('.image_block').remove();

                                if ($('.image_block').length < 1) {
                                    secBlock.remove();
                                }
                                if (response.type == 'plain') {
                                    $('#image_upload').show();
                                }

                                $('#alert_msg').text(response.message);
                                $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-success').removeClass('alert-danger');
                            } else {
                                $('#alert_msg').text(response.message);
                                $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-danger').removeClass('alert-success');
                            }
                        }
                    });
                } else {
                    $('#alert_msg').text('Failed: Image id or banner id not found');
                    $('#alert_msg').show().delay(1000).fadeOut().addClass('alert-danger').removeClass('alert-success');
                }
            }
        }
    </script>
@endsection
