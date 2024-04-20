@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="map">
        @php $key = array_search('location_map', array_column($common_settings, 'item')) @endphp
        {!! $common_settings[$key]['value'] !!}
    </div>

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('view.contact_us') }}">Contact us</a></li>
            </ol>
        </nav>
    </div>
    <section class="contact-page">
        <div class="width-container contact-container">
            <div class="row">
                <!-- <div class="col-md-4 left-contact">
                        <h3>Contact Us</h3>
                        <img src="images/contact-img.jpg" class="img-fluid" alt="">
                      </div> -->

                <div class="col-md-6 right-contact">
                    @guest('user')
                        <div class="cont-log">


                            <a data-toggle="collapse" class="drop" id="log-btn-id" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin" id="btn_login"> <button name="login" class="btn login-button cont-log-btn User_login_drop">Login</button></a>


                            <a href="{{ route('register.view') }}"><button class="btn cont-reg-btn">Register</button></a>
                        </div>
                    @endguest

                    <div class="cont-whats-login">
                        @php $key = array_search('whatsapp', array_column($common_settings, 'item')) @endphp
                        <div class="whats-login-para">
                            <p>We are actively working on partnerships with a patient first goal. Please do get in touch with us and efficient healthcare ecosystem for all your health care needs.</p>
                        </div>
                        <div class="whats-login-btn">
                            <a href="https://api.whatsapp.com/send?phone={{ $common_settings[$key]['value'] }}&text=Hey,%20I%20am%20looking%20for%20your%20services"target="_blank"><button class="btn whats-chat"><i class="fa fa-whatsapp" aria-hidden="true"></i> Chat</button></a>
                            <button class="btn norm-chat" id="normal-chat">Chat</button>
                        </div>
                    </div>

                    <div class="cont-address">
                        @php $key = array_search('company_address', array_column($common_settings, 'item')) @endphp
                        <span class="cont-icon cont-icon1">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                        </span>
                        <span class="cont-dtls">
                            <h6>Address: </h6>
                            <p>{!! $common_settings[$key]['value'] !!}
                            </p>
                        </span>
                    </div>
                    <div class="cont-phone">
                        @php $key = array_search('phone_number', array_column($common_settings, 'item')) @endphp
                        <span class="cont-icon cont-icon2"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        <span class="cont-dtls">
                            <h6>Phone No:</h6>
                            <p><a href="tel:{{ $common_settings[$key]['value'] }}">{{ $common_settings[$key]['value'] }}</a></p>
                        </span>
                    </div>
                    <div class="cont-mail">
                        @php $key = array_search('company_email', array_column($common_settings,'item')) @endphp
                        <span class="cont-icon cont-icon3"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        <span class="cont-dtls">
                            <h6>E-mail:</h6>
                            <p><a href="mailto:{{ $common_settings[$key]['value'] }}">{{ $common_settings[$key]['value'] }}</a></p>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <div class="col social-icons">
                            <div class="row">
                                @foreach ($socialmediaicons as $icons)
                                    <a href="{{ $icons->link }}" target="_blank">
                                        @if ($icons->type == 'image')
                                            @if ($icons->icon != '')
                                                <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid pr-1" alt="{{ $icons->name }}">
                                            @endif
                                        @else
                                            {!! $icons->icon !!}
                                        @endif
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="inner">

                        <h3>Write to Us</h3>
                        <div class="form-row">
                            <div class="form-wrapper">
                                <label for="">Name: <span>*</span></label>
                                <input type="text" class="form-control name-input" name="fullname" placeholder="Your Name" id="fullname">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-wrapper">
                                <label for="">Email Id: <span>*</span></label>
                                <input type="text" class="form-control email-input" name="email" placeholder="Your email" id="email">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-wrapper">
                                <label for="">Message: <span>*</span></label>
                                <textarea id="message" name="message" rows="5" placeholder="Message" class="w-100 border comment" style="resize: none;" id="message"></textarea>
                            </div>
                        </div>
                        <div id="contact_outer">
                            <button data-text="Apply" class="reg-btn mt-2" id="btn_contact">
                                <span>Submit</span>
                            </button>
                        </div>
                        <div id="contact_alerts" class="alert alert-danger" style="display:none;"></div>
                        <div id="contact_success" class="alert alert-success" style="display:none;"></div>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('footer_scripts')
    <script>
        $(document).on("click", "#btn_contact", function(e) {
            e.preventDefault();
            var fullname = $("#fullname").val();
            var email = $("#email").val();
            var message = $("#message").val();

            if (fullname == '') {
                $("#contact_alerts").html('Enter Your Name');
                $("#contact_alerts").show().delay(3000).fadeOut();
            } else if (email == '') {
                $("#contact_alerts").html('Enter Your Email address');
                $("#contact_alerts").show().delay(3000).fadeOut();
            } else if (message == '') {
                $("#contact_alerts").html('Enter Your Message');
                $("#contact_alerts").show().delay(3000).fadeOut();
            } else {
                outerhtml = $("#contact_outer").html();
                $("#contact_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
                $.ajax({
                    url: '{{ route('sent.Contactus') }}',
                    method: "POST",
                    data: {
                        fullname: fullname,
                        email: email,
                        message: message,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.ajax_status == 'success') {
                            $("#fullname").val('');
                            $("#email").val('');
                            $("#message").val('');

                            $("#contact_success").html(data.message);
                            $("#contact_success").show().delay(3000).fadeOut();
                        } else {
                            $("#contact_alerts").html(data.message);
                            $("#contact_alerts").show().delay(3000).fadeOut();
                        }
                        $("#contact_outer").html(outerhtml);
                    }
                });
            }
        });
        $(document).on("click", '#normal-chat', function(e) {
            e.preventDefault();
            $(".desktop-closed-message-avatar").trigger("click");
        });
        //         $(document).on("click", "#btn_contact", function(e) {

        // e.preventDefault();
        // var fullname = $("#fullname").val();
        // var sub = $("#sub").val();
        // var email = $("#email").val();
        // var phone = $("#phone").val();
        // var messages = $("#message_contact").val();
        // if (fullname == '') {
        //     $("#contact_alerts").html('Enter Your Name');
        //     $("#contact_alerts").show().delay(3000).fadeOut();
        // } else if (sub == '') {
        //     $("#contact_alerts").html('Enter Your Subject');
        //     $("#contact_alerts").show().delay(3000).fadeOut();
        // } else if (phone == '') {
        //     $("#contact_alerts").html('Enter Your Phone number');
        //     $("#contact_alerts").show().delay(3000).fadeOut();
        // } else if (email == '') {
        //     $("#contact_alerts").html('Enter Your Email address');
        //     $("#contact_alerts").show().delay(3000).fadeOut();
        // } else {
        //     outerhtml = $("#contact_outer").html();
        //     $("#contact_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}">');
        //     $.ajax({
        //         url: '{{ url('contact/team') }}',
        //         method: "POST",
        //         data: {
        //             fullname: fullname,
        //             sub: sub,
        //             email: email,
        //             phone: phone,
        //             messages: messages,
        //             _token: "{{ csrf_token() }}"
        //         },
        //         dataType: "json",
        //         success: function(data) {
        //             if (data.ajax_status == 'success') {
        //                 $("#fullname").val('');
        //                 $("#sub").val('');
        //                 $("#email").val('');
        //                 $("#phone").val('');
        //                 $("#message_contact").val('');
        //                 $("#contact_alerts").html(data.message);
        //                 $("#contact_alerts").show().delay(3000).fadeOut();
        //             } else {
        //                 $("#contact_alerts").html(data.message);
        //                 $("#contact_alerts").show().delay(3000).fadeOut();
        //             }
        //             $("#contact_outer").html(outerhtml);
        //         }
        //     });
        // }
        // });
    </script>
@endsection
