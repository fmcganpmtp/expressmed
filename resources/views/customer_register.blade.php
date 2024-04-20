@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')

    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls">Create Account</li>
            </ol>
        </nav>
    </div>

    <div class="width-container">
        <div class="register">
            <h5>Create Account</h5>
            <div class="regiter-content" id="register-block">
                <div class="input-group register-form">
                    <input type="text" id="register_name" class="form-control" placeholder="First Name*" aria-label="Search" aria-describedby="search-addon" />
                </div>
                <div class="input-group register-form">
                    <input type="email" id="register_email" class="form-control" placeholder="E-mail*" aria-label="Search" aria-describedby="search-addon" />
                </div>



                <div class="input-group register-form ">
                    <select class="form-control selectpicker" id="country" name="country" data-live-search="true" required>
                        <option value="">Choose Your Country</option>
                        @foreach ($countries as $row)
                            <option value="{{ $row->id }}" data-code="{{ $row->phonecode }}" data-tokens="{{ $row->name }}" data-content="<div class='flag-outer'><img src='{{ asset('assets/uploads/countries_flag') . '/' . $row->flag_icon }}'></div> <span class='text-dark'>{{ $row->name }}</span>">{{ $row->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group register-form">
                    <input type="text" id="country_code" class="form-control" autocomplete="off" placeholder="Code" disabled />
                    <input type="text" id="register_phone" class="form-control" autocomplete="off" placeholder="Phone no*" />
                </div>

                <div class="input-group register-form eye-icon">
                    <input type="password" id="register_password" class="form-control" placeholder="Password*" />
                    <i onclick="passwordShow()" class="fa fa-eye" id="pass"></i>
                </div>
                <div class="input-group register-form eye-icon">
                    <input type="password" id="register_confirmpassword" class="form-control" placeholder="Confirm Password*" />
                    <i onclick="cpasswordShow()" class="fa fa-eye" id="cpass"></i>
                </div>
                <div id="register_alerts"></div>
                <div id="register_button_outer" class="d-flex justify-content-center Edit-butn">
                    <a href="javascript:void(0)" id="register_button">Create now</a>
                </div>
                <div class="already-account">Already have an account? <a data-toggle="collapse" class="drop User_login_drop" id="log-btn-id" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin" id="btn_login">Login</a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
        });
        $(document).on('keydown', 'input', function() {
            $("#register_alerts").text('');
            $("#register_alerts").hide();
        });

        function passwordShow() {
            var x = document.getElementById("register_password");
            document.getElementById('pass').classList.toggle('fa-eye-slash');
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function cpasswordShow() {
            var x = document.getElementById("register_confirmpassword");
            document.getElementById('cpass').classList.toggle('fa-eye-slash');
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        $(document).on('click', '#register_button', function(e) {
            outerhtml = '';
            var register_name = $("#register_name").val();
            var register_email = $("#register_email").val();
            var register_phone = $("#register_phone").val();

            var register_country = $("#country").val();
            var register_password = $("#register_password").val();
            var register_confirmpassword = $("#register_confirmpassword").val();

            if (register_name != '' && register_email != '' && register_phone != '' && register_password != '') {
                outerhtml = $("#register_button_outer").html();
                $("#register_button_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
                $.ajax({
                    type: "post",
                    data: {
                        name: register_name,
                        email: register_email,
                        country: register_country,
                        phone: register_phone,
                        password: register_password,
                        confirm_password: register_confirmpassword,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    url: "{{ route('register.user') }}",
                    success: function(res) {
                        if (res.status == 'success') {
                            $("#register_alerts").show();
                            swal({
                                title: 'Success',
                                text: 'Your account created successfully. Please login to use the services.',
                                type: 'success',
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                            // $("#register_alerts").html('<small class="text-success">Your account created successfully. Please login to use the services.</small>').delay(2000).fadeOut();
                            $("#register_button_outer").html(outerhtml);
                            $("#register-block").find('input').val('');
                        } else {
                            $("#register_alerts").show();
                            $("#register_alerts").html('<small class="text-danger">' + res.message + '.</small>');
                            $("#register_button_outer").html(outerhtml);
                        }
                    }
                });
            } else {
                $("#register_alerts").show();
                $("#register_alerts").html('<small class="text-danger">All informations required. Please complete the form before submit.</small>').delay(3000).fadeOut();
            }

        });
        $(document).on('change', '#country', function(e) {
            var country_id = $(this).val();
            var country_code = $('option:selected', this).attr('data-code');
            outerhtml = '';
            if ((country_code != '') && (country_code != undefined)) {
                $("#country_code").val('+' + country_code);

            } else {
                $("#country_code").val('');

            }
        });
    </script>
@endsection
