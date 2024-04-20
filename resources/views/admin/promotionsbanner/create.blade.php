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
                                <input type="text" name="bannertitle" class="form-control" placeholder="Banner Title" value="{{ old('bannertitle') }}">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Banner Section</strong>
                                <select name="bannersection" id="bannersection" class="form-control">
                                    <option value="">--Choose Banner Section--</option>
                                    <option value="mainbody" {{ old('bannersection') == 'mainbody' ? 'selected' : '' }}>Main Body</option>
                                    <option value="sidebar" {{ old('bannersection') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                    <option value="mobile" {{ old('bannersection') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Position</strong>
                                <select name="bannerposition" id="bannerposition" class="form-control">
                                    @if (old('bannersection') != '' && old('bannersection') == 'mainbody')
                                        <option value="">--Choose Position--</option>
                                        <option value="maintop" {{ old('bannerposition') == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                        <option value="middle" {{ old('bannerposition') == 'middle' ? 'selected' : '' }}>Middle</option>
                                        <option value="footer" {{ old('bannerposition') == 'footer' ? 'selected' : '' }}>Bottom1</option>
                                        <option value="footer2" {{ old('bannerposition') == 'footer2' ? 'selected' : '' }}>Bottom2</option>
                                        <option value="footer3" {{ old('bannerposition') == 'footer3' ? 'selected' : '' }}>Bottom3</option>
                                        <option value="footer4" {{ old('bannerposition') == 'footer4' ? 'selected' : '' }}>Bottom4</option>
                                    @elseif (old('bannersection') != '' && old('bannersection') == 'sidebar')
                                        <option value="">--Choose Position--</option>
                                        <option value="top" {{ old('bannerposition') == 'top' ? 'selected' : '' }}>Sidebar Top</option>
                                        <option value="top2" {{ old('bannerposition') == 'top2' ? 'selected' : '' }}>Sidebar Top 2</option>
                                        <option value="top3" {{ old('bannerposition') == 'top3' ? 'selected' : '' }}>Sidebar Top 3</option>
                                        <option value="top3" {{ old('bannerposition') == 'top4' ? 'selected' : '' }}>Sidebar Top 4</option>


                                        <option value="bottom" {{ old('bannerposition') == 'bottom' ? 'selected' : '' }}>Product Details-Sidebar 1</option>
                                        <option value="bottom2" {{ old('bannerposition') == 'bottom2' ? 'selected' : '' }}>Product Details-Sidebar 2</option>
                                    @elseif (old('bannersection') != '' && old('bannersection') == 'mobile')
                                        <option value="">--Choose Position--</option>
                                        <option value="maintop" {{ old('bannerposition') == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                        <option value="middle" {{ old('bannerposition') == 'middle' ? 'selected' : '' }}>Middle</option>
                                        <option value="footer" {{ old('bannerposition') == 'footer' ? 'selected' : '' }}>Bottom</option>
                                    @else
                                        <option value="">--Choose Any Banner Section--</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <strong>Type</strong>
                                <input type="hidden" name="bannertype" id="hid_bannertype" value="{{ old('bannertype') }}">
                                <input type="text" id="bannertype" class="form-control" value="{{ old('bannertype') == 'slider' ? 'Add multiple images for slider' : (old('bannertype') == 'plain' ? 'Only one image allowed' : 'Choose any position') }}" readonly>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <strong>Upload Image </strong><span class="text-danger" id="imageinfo"></span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-8">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="file" name="bannerimage[]" class="form-control">
                                    <span>&nbsp;</span>
                                    <a class="btn btn-primary btn-circle btn-md" id="addmoreimage" href="javascript:void(0)" title="Add more images" style="display: none"> <i class="fa fa-plus" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="form-group">
                                <strong>Banner URL</strong> eg:(http://url.com)
                                <input type="text" name="bannerurl[]" class="form-control" value="{{ old('bannerurl.0') }}" placeholder="http://url.com">
                            </div>
                        </div>

                        <div id="div_bannerimage" class="col-xs-12 col-sm-12 col-md-8">
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-8">
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
    <script>
        $(document).ready(function() {
            var pos = '{{ old('bannerposition') != '' ? old('bannerposition') : '' }}';

            switch (pos) {
                case 'maintop':
                    $('#imageinfo').text('(Image dimension width:1081 x height:550 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'middle':
                    $('#imageinfo').text('(Image dimension width:810 x height:340 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer':
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer2':
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer3':
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'footer4':
                    $('#imageinfo').text('(Image dimension width:330 x height:281 pixel)');
                    $('#addmoreimage').hide();
                    break;
                case 'top':
                    $('#imageinfo').text('(Image dimension width:330 x height:387 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'top2':
                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'top3':
                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'top4':
                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'bottom':
                    $('#imageinfo').text('(Image dimension width:330 x height:440 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'bottom2':
                    $('#imageinfo').text('(Image dimension width:330 x height:308 pixel)');
                    $('#addmoreimage').show();
                    break;
                default:
                    $('#bannertype').val('Choose any position');
            }
        });

        $(document).on('change', '#bannersection', function() {
            var section = $(this).val();
            $('#bannertype').val('Choose any position');
            $('#div_bannerimage').html('');
            $('#imageinfo').text('');
            $('#addmoreimage').hide();

            var ban_position_option = '';
            if (section == 'mainbody') {
                ban_position_option = '<option value="0">--Choose Position--</option>';
                ban_position_option += '<option value="maintop">Top Main Banner</option>';
                ban_position_option += '<option value="middle">Middle</option>';
                ban_position_option += '<option value="footer">Bottom1</option>';
                ban_position_option += '<option value="footer2">Bottom2</option>';
                ban_position_option += '<option value="footer3">Bottom3</option>';
                ban_position_option += '<option value="footer4">Bottom4</option>';
                $('#bannerposition').focus();
            } else if (section == 'sidebar') {
                ban_position_option = '<option value="0">--Choose Position--</option>';
                ban_position_option += '<option value="top">Sidebar Top</option>';
                ban_position_option += '<option value="top2">Sidebar Top 2</option>';
                ban_position_option += '<option value="top3">Sidebar Top 3</option>';
                ban_position_option += '<option value="top4">Sidebar Top 4</option>';


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

        $(document).on('change', '#bannerposition', function() {
            var position = $(this).val();
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
                    $('#imageinfo').text('(Image dimension width:506 x height:218 pixel)');
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
                    $('#addmoreimage').show();
                    break;
                case 'top3':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');

                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').show();
                    break;
                case 'top4':
                    $('#hid_bannertype').val('slider');
                    $('#bannertype').val('Add multiple images for slider');

                    $('#imageinfo').text('(Image dimension width:330 x height:420 pixel)');
                    $('#addmoreimage').show();
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
    </script>
@endsection
