@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->


    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('generalprescription.create') }}">Upload prescription</a></li>
            </ol>
        </nav>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-md-12 col-md-12 upload-pre">

            <div class="row">


                {{-- <div class="field">
                    <h6>Upload Prescriptoion</h6>
                    <form method="POST" action="{{ route('generalprescription.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="files" name="files[]" multiple />
                        <br>
                        <button type="submit" id="save-place-order" class="viewall-butn" disabled>CONTINUE</button>
                    </form>
                </div> --}}
                <div class="form-group">
                    <h6>Upload Prescription</h6>
                    <form id="pre-submit" method="POST" action="{{ route('generalprescription.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group control-group increment" required>
                            <input type="file" name="files[]" class="form-control">
                            <div class="input-group-btn pl-3">
                                <button class="btn btn-success add_more_btn" type="button"><i class="glyphicon glyphicon-plus"></i><i class="fa fa-plus text-white"></i></button>
                            </div>
                        </div>
                        <div class="fields_extent"></div>
                        <div class="clone hide" style="display: none;">
                            <div class="control-group input-group" style="margin-top:10px">
                                <input type="file" name="files[]" class="form-control">

                                <div class="input-group-btn">
                                    <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i><i class="fa fa-close text-white"></i></button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <a data-toggle="collapse" class="drop" id="log-btn-id" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin" id="btn_login">
                            <button name="login" @auth('user') id="save-place-order" @endauth class="viewall-butn continue_btn btn login-button cont-log-btn User_login_drop" disabled="true">CONTINUE</button>
                        </a>

                    </form>
                </div>

            </div>
        </div>
        <br>
    </div>
@endsection

@section('footer_scripts')
    <script language="javascript" type="text/javascript">
        document.querySelector("input[type=file]").onchange = ({
            target: {
                value
            },
        }) => {
            $('.continue_btn').removeAttr('disabled');
        };

        $(document).ready(function() {
            $('.alert-danger').delay(3000).fadeOut();

        });

        $(document).on('click ', '#save-place-order', function() {
            // var orderstatus = $(this).val();
            document.getElementById('pre-submit').submit();
        });
        var pdf_file_icon = '<img class=\"imageThumb\" src="{{ asset('img/pdf.png') }}">';
        $(document).ready(function() {
            if (window.File && window.FileList && window.FileReader) {
                $("#files").on("change", function(e) {


                    var files = e.target.files,
                        filesLength = files.length;
                    var extension = $(this).val().split('.').pop().toLowerCase();
                    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.pdf)$/;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[i]
                        console.log(f);
                        if (regex.test(f.name.toLowerCase())) {
                            var fileReader = new FileReader();
                            fileReader.onload = (function(e) {
                                var file = e.target;
                                if (extension != 'pdf') {
                                    $("<span class=\"pip\">" +
                                        "<input type='file' hidden id='files1' name='files1[]' multiple />" +
                                        "<img name='sd'class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                                        "<br/><span class=\"remove\">X</span>" +
                                        "</span>").insertAfter("#files");
                                    $(".remove").click(function() {
                                        $(this).parent(".pip").remove();

                                    });
                                } else {
                                    $("<span class=\"pip\">" +
                                        pdf_file_icon +
                                        "<br/><span class=\"remove\">X</span>" +
                                        "</span>").insertAfter("#files");
                                    $(".remove").click(function() {
                                        $(this).parent(".pip").remove();
                                    });
                                }

                            });
                            fileReader.readAsDataURL(f);
                        } else {
                            alert(f.name + " is not a valid  file.");
                            return false;
                        }
                    }
                    if (filesLength > 0) {
                        $('#save-place-order').removeAttr('disabled');

                    }
                });
            } else {
                alert("Your browser doesn't support to File API")
            }
        });


        $(document).ready(function() {
            $(".add_more_btn").click(function() {
                var html = $(".clone").html();
                $(".fields_extent").append(html);
            });
            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".control-group").remove();
            });
        });
    </script>
@endsection
