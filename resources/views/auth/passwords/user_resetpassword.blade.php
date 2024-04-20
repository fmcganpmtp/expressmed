@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.navbar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls">{{ isset($page_title) ? $page_title : 'Reset Password' }}</li>
            </ol>
        </nav>
    </div>

    <div class="width-container">

        @if (\Session::has('error'))
            <div class="alert alert-danger">
                <ul>
                    <li>{!! \Session::get('error') !!}</li>
                </ul>
            </div>
        @endif


        @if (session('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{!! session('success') !!}</li>
                </ul>
            </div>
        @endif

        <div class="register">
            <h5>{{ isset($page_title) ? $page_title : 'Reset Password' }}</h5>
            <div class="regiter-content">
                <form id="form_resetpassword" action="" method="post">
                    @csrf
                    @if (isset($type) && $type == 'mail_confirmation')
                        <div class="input-group register-form">
                            <input type="text" id="register_email" name="register_email" value="{{ old('register_email') }}" class="form-control" placeholder="E-mail*" autocomplete="off" />
                        </div>
                        @if ($errors->has('register_email'))
                            <span class="text-danger"><small>{{ $errors->first('register_email') }}</small></span>
                        @endif
                    @endif

                    @if (isset($type) && $type == 'reset_password')
                        <div class="input-group register-form">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password*" autocomplete="off" />
                        </div>
                        @if ($errors->has('password'))
                            <span class="text-danger"><small>{{ $errors->first('password') }}</small></span>
                        @endif

                        <div class="input-group register-form">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password*" autocomplete="off" />
                        </div>
                        @if ($errors->has('confirm_password'))
                            <span class="text-danger"><small>{{ $errors->first('confirm_password') }}</small></span>
                        @endif

                    @endif

                    <div id="register_button_outer" class="d-flex justify-content-center Edit-butn">
                        <button class="btn btn-md btn-primary" id="submit_button">{{ isset($Submit_button) ? $Submit_button : 'Submit' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('footer_scripts')
    <script>
        $(document).on('click', '#submit_button', function() {
            $('#form_resetpassword').submit();
            $(this).attr('disabled', 'disabled').text('loading..');
        });
    </script>
@endsection
