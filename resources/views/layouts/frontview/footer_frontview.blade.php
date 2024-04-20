<footer>

    <div class="width-container footer-inner-container flex-wrap d-flex justify-content-between">
        {{-- @php $key = array_search('footer_logo', array_column($common_settings, 'item')) @endphp
        @if ($common_settings[$key]['value'])
            <div class="footer-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('/assets/uploads/logo/' . $common_settings[$key]['value']) }}" class="img-responsive" alt="" title="" />
                </a>
            </div>
        @endif --}}
        <div class="footer-links payment-footer">
            @php $key = array_search('footer_logo', array_column($common_settings, 'item')) @endphp
            @if ($common_settings[$key]['value'])
                <div class="footer-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('/assets/uploads/logo/' . $common_settings[$key]['value']) }}" class="img-fluid" alt="" title="" />
                    </a>
                </div>
            @endif
            <h6>Download our app</h6>
            <div class="footer-download">
                <a href="#"><img src="{{ asset('front_view/images/footer-download-01.png') }}" class="img-fluid" alt=""></a>
                <a href="https://play.google.com/store/search?q=expressmed&c=apps"><img src="{{ asset('front_view/images/footer-download-02.png') }}" class="img-fluid" alt=""></a>
            </div>
            <h6 class="secured-pay">Secured Payment</h6>
            <div class="secured-payment">
                <a href="#"><img src="{{ asset('front_view/images/footer-payment-01.png') }}" class="img-fluid" alt=""></a>
                <a href="#"><img src="{{ asset('front_view/images/footer-payment-02.png') }}" class="img-fluid" alt=""></a>
                <a href="#"><img src="{{ asset('front_view/images/footer-payment-03.png') }}" class="img-fluid" alt=""></a>
                <a href="#"><img src="{{ asset('front_view/images/footer-payment-04.png') }}" class="img-fluid" alt=""></a>
                <a href="#"><img src="{{ asset('front_view/images/footer-payment-05.png') }}" class="img-fluid" alt=""></a>
            </div>


        </div>

        {{-- <div class="footer-links footerlink-a">
            <h6>For Customers</h6>
            <ul>
                <li><a href="#">Medica Store</a></li>
                <li><a href="#">Health Products</a></li>
                <li><a href="#">Doctor Consultation</a></li>
                <li><a href="#">Lab Test</a></li>
                <li><a href="#">Find a Doctor</a></li>
                <li><a href="#">Health Articles</a></li>
                <li><a href="#">Offers/Coupons</a></li>
            </ul>
        </div> --}}

        <div class="footer-links footerlink-b">
            <h6>Know Expressmed</h6>
            <ul>
                @if (!empty($ContentPages))
                    @foreach ($ContentPages as $ContentPages_Row)
                        @php $PagePosition = explode(',', $ContentPages_Row->page_position); @endphp
                        @if (in_array('footer1', $PagePosition))
                            <li><a href="{{ route('view.contentpage', $ContentPages_Row->seo_url) }}">{{ $ContentPages_Row->page }}</a></li>
                        @endif
                    @endforeach
                @endif
                <li> <a href="{{ route('page.teams') }}">Our Team</a></li>
                <li> <a href="{{ route('view.career.jobs') }}">Careers</a></li>

                <li><a href="{{ route('view.contact_us') }}">Contact Us</a></li>
                <li> <a href="{{ route('news_evets') }}">News & Events</a></li>
            </ul>
        </div>
        @if ($AllCategories->isNotEmpty())
            <div class="footer-links footerlink-c">
                <h6>Popular Categories</h6>
                <ul>
                    @foreach ($AllCategories->take(7) as $AllCategories_Row)
                        @if ($AllCategories_Row->name == 'All Medicines')
                            <li><a href="{{ route('list.all-medicines') }}" title="{{ $AllCategories_Row->name }}">{{ ucfirst($AllCategories_Row->name) }}</a></li>
                        @else
                            <li><a href="{{ route('shopping.productlisting', $AllCategories_Row->name) }}" title="{{ $AllCategories_Row->name }}">{{ ucfirst($AllCategories_Row->name) }}</a></li>
                            {{-- {{ Str::limit($AllCategories_Row->name, 20, '..') }} --}}
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="footer-links footerlink-d">
            <h6>Policy Info</h6>
            <ul>
                @if (!empty($ContentPages))
                    @foreach ($ContentPages as $ContentPages_Row)
                        @php $PagePosition = explode(',', $ContentPages_Row->page_position); @endphp
                        @if (in_array('footer2', $PagePosition))
                            <li><a href="{{ route('view.contentpage', $ContentPages_Row->seo_url) }}">{{ $ContentPages_Row->page }}</a></li>
                        @endif
                    @endforeach

                @endif
            </ul>
        </div>
        <div class="footer-links payment-footer">

            <div class="item-slider">
                <h6 class="text-white text-center">News Letter Subscription</h6>
                <p class="text-white text-center">Get all the latest information on Events, Sales and Offers.</p>
                <div class="news-search flex-grow-1">
                    <input id="newsletter_email" class="news-letter-search" name="search" type="text" placeholder="Enter e-mail" />
                </div>
                <div id="subscribe_button_outer" class="news-letter-subscribe">
                    <button type="button" id="newsleter_submit" class="btn">Subscribe</button>
                </div>
                <p id="newsletter_alert" style="display: none"></p>
            </div>
            @if ($socialmediaicons->isNotEmpty())
                <h6>Follow Us</h6>
                <div class="social-media">
                    @foreach ($socialmediaicons as $icons)
                        <a href="{{ $icons->link }}" target="_blank">
                            @if ($icons->type == 'image')
                                @if ($icons->icon != '')
                                    <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="{{ $icons->name }}" width="30">
                                @endif
                            @else
                                {!! $icons->icon !!}
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <section class="py-5 footer-security-outer">

            <div class="footer-security row">
                <div class="col-md-4 left-secure-outer">
                    <div class="secure-outer">
                        <div class="left-rela">
                            <img src="{{ asset('front_view/images/trust.png') }}" class="img-fluid" alt="">
                        </div>
                        <div class="right-rela">
                            <h6>Reliable</h6>
                            <p>All products displayed on expressmed are procured from verified and licensed pharmacies and licensed doctors.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 left-secure-outer">
                    <div class="secure-outer">
                        <div class="left-rela">
                            <img src="{{ asset('front_view/images/shield.png') }}" class="img-fluid" alt="">
                        </div>
                        <div class="right-rela">
                            <h6>Secure</h6>
                            <p>Expressmed uses Secure Sockets Layer (SSL) 256SHA encryption and is Payment Card Industry Data Security Standard (PCI DSS) compliant.</p>
                        </div>
                    </div>

                </div>
                {{-- <div class="item-slider">
                    <h6 class="text-white text-center">News Letter Subscription</h6>
                    <p class="text-white text-center">Get all the latest information on Events, Sales and Offers.</p>
                    <div class="news-search flex-grow-1">
                        <input id="newsletter_email" class="news-letter-search" name="search" type="text" placeholder="Enter e-mail" />
                    </div>
                    <div id="subscribe_button_outer" class="news-letter-subscribe">
                        <button type="button" id="newsleter_submit" class="btn">Subscribe</button>
                    </div>
                    <p id="newsletter_alert" style="display: none"></p>
                </div> --}}

                <div class="col-md-4 left-secure-outer">
                    <div class="secure-outer">
                        <div class="left-rela">
                            <img src="{{ asset('front_view/images/affordable.png') }}" class="img-fluid" alt="">
                        </div>
                        <div class="right-rela">
                            <h6>Affordable</h6>
                            <p>Find affordable medicine substitutes, save up to 75% on health products.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="terms-condition">
                <p>In compliance with Drugs and Cosmetics Act, 1940 and Drugs and Cosmetics Rules, 1945, we don't process requests for Schedule X and other habit forming drugs.</p>
            </div>
            <div class="copyright">Copyright © 2022 <a href="https://www.impressmed.in/" target="_blank">Impressmed.</a> All Rights Reserved | Powered by <a href="https://www.techoriz.com/" target="_blank">Techoriz.</a>

            </div>

        </section>


    </div>

</footer>
<div class="cont-whats-login">
    @php $key = array_search('whatsapp', array_column($common_settings, 'item')) @endphp
    <div class="footer-whatsapp">
        <a href="https://api.whatsapp.com/send?phone={{ $common_settings[$key]['value'] }}&text=Hey,%20I%20am%20looking%20for%20your%20services" class="float" target="_blank"> <i class="fa fa-whatsapp  my-float" aria-hidden="true"></i> </a>
    </div>
</div>
@php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp


<input type="hidden" id="csrftoken" value="{{ csrf_token() }}" />

{{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/assets/css/chat.min.css"> --}}

<script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>

<script src="{{ asset('front_view/js/cart.js') }}"></script>


<script type="text/javascript">
    var botmanWidget = {
        introMessage: '✋ Hi! Welcome to Expressmed customer service. How may i help you? say hi',
        title: 'Expressmed Customer Support',
        mainColor: '#1c8ece',
        bubbleBackground: '#216d9f',
        aboutText: '',
        Background: '#216d9f',

        chatServer: '{{ url('/botman') }}',
        frameEndpoint: '{{ url('/botman/chat') }}',
    };
    var cart = [];
    var cart_path = '{{ route('product.cart') }}';
    var checkoutUrl = '{{ route('product.checkout') }}';
    var product_image_path = '{{ asset('assets/uploads/products') }}';
    var logo_image_path = '{{ asset('assets/uploads/logo') }}';
    var loader_gif = '<img src="{{ asset('img/ajax-loader.gif') }}">';
    var assets_path = '{{ asset('img') }}';
    var currencyIcon = '{!! $common_settings[$currency_key]['value'] !!}';
    var whatsapp = '{!! $common_settings[$key]['value'] !!}';
    // var noImage = '<img src="{{ asset('img/no-image.jpg') }}">';
    var list_products_url = '{{ route('list.allproductlisting') }}'
    var url_addToCart = '{{ route('product.addTocart') }}';
    var url_deleteCart = '{{ route('product.deleteFromcart') }}';
    var url_productdetails = '{{ url('/item/') }}';



    var cookiechatID = 0;

    setInterval(loadChatMessage, 10000);

    $(document).ready(function() {
        loadChatMessage();
    });

    function loadChatMessage() {
        cookiechatID = getCookie("chat_flag"); //--get cookie variable with defined function getCookie()--

        var chatmsg_custid = (typeof($('.chatbot_customer').last().attr('data-id')) != "undefined" && $('.chatbot_customer').last().attr('data-id') != null) ? $('.chatbot_customer').last().attr('data-id') : 0;
        var chatmsg_excecutiveid = (typeof($('.chatbot_executive').last().attr('data-id')) != "undefined" && $('.chatbot_executive').last().attr('data-id') != null) ? $('.chatbot_executive').last().attr('data-id') : 0;

        var chatmsg_id = (parseInt(chatmsg_custid) > parseInt(chatmsg_excecutiveid) ? parseInt(chatmsg_custid) : parseInt(chatmsg_excecutiveid));

        var Chatbotman = $("#botmanWidgetRoot").html();
        if (cookiechatID != 0) {
            var chatMode = (typeof($('#chat_mode').val()) != "undefined" && $('#chat_mode').val() != null) ? 1 : 0;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ route('chat_message.load') }}',
                data: {
                    chatID: cookiechatID,
                    chatmsg_id: chatmsg_id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.result == 'success') {
                        var html = '';
                        if (chatMode == 0) {

                            html = '<div class="chat-outer">'
                            html +=
                                '<div class="chat-header-section"><div style="display: flex; align-items: center; padding: 0px 30px 0px 0px; font-size: 15px; font-weight: normal; color: rgb(51, 51, 51);">Medcliq Customer Support</div><div><svg fill="#FFFFFF" height="15" viewBox="0 0 15 15" width="15" xmlns="http://www.w3.org/2000/svg" style="margin-right: 15px; margin-top: 6px; vertical-align: middle;"><line x1="1" y1="15" x2="15" y2="1" stroke="white" stroke-width="1"></line><line x1="1" y1="1" x2="15" y2="15" stroke="white" stroke-width="1"></line></svg></div></div>';
                            html += '<div id="botmanChatRoot" style="background-color:#f9f9f9;">';
                            html += '<input type="hidden" id="chat_mode" value="1">';
                            html += '<div>';
                            html += '<div id="messageArea" class="chatMsgArea">';
                            html += '<ol class="chat" style="height: 359px; overflow-y: scroll;list-style-type: none;">';
                            jQuery.each(response.chat_messages, function(i, val) {
                                if (val.type == 'customer') {
                                    html += '<li data-message-id="" class="chatbot_customer visitor" style="margin-left:210px" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">';
                                    html += '<div class="msg me-chat">';
                                    html += '<div>';
                                    html += '<p>' + val.text_message + '</p>';
                                    html += '</div>';
                                    // html += '<div class="time">17:19</div>';
                                    html += '</div>';
                                    html += '</li>';
                                }

                                if (val.type == 'executive') {
                                    html += '<li data-message-id="" class="chatbot_executive chatbot" style="" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">';
                                    html += '<div class="msg">';
                                    html += '<div>';
                                    html += '<p>' + val.text_message + '</p>';
                                    html += '</div>';
                                    // html += '<div class="time">17:19</div>';
                                    html += '</div>';
                                    html += '</li>';
                                }
                            });
                            html += '</ol>';
                            html += '</div><input id="userText" class="form-control" type="text" placeholder="Send a message...">';
                            html += '<button type="button" id="send_msg" class="btn btn-primary" value="">Send</button>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';

                            $("#botmanWidgetRoot").html(html);
                        } else if (chatMode == 1) {
                            jQuery.each(response.chat_messages, function(i, val) {
                                if (val.type == 'customer') {
                                    html += '<li data-message-id="" class="chatbot_customer visitor" style="margin-left:210px" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">';
                                    html += '<div class="msg me-chat">';
                                    html += '<div>';
                                    html += '<p>' + val.text_message + '</p>';
                                    html += '</div>';
                                    // html += '<div class="time">17:19</div>';
                                    html += '</div>';
                                    html += '</li>';
                                }

                                if (val.type == 'executive') {
                                    html += '<li data-message-id="" class="chatbot_executive chatbot" style="" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">';
                                    html += '<div class="msg">';
                                    html += '<div>';
                                    html += '<p>' + val.text_message + '</p>';
                                    html += '</div>';
                                    // html += '<div class="time">17:19</div>';
                                    html += '</div>';
                                    html += '</li>';
                                }
                            });

                            $("#messageArea").find('ol').append(html);
                        }
                    }
                }
            });
        } else {
            // alert('cookie end');
            // console.log(Chatbotman);
            // $("#botmanWidgetRoot").html(Chatbotman);
        }
    }
    $(document).on('click', '.chat-header-section', function() {
        $('#botmanChatRoot').toggle();
    });

    $(document).on('click', '.desktop-closed-message-avatar', function() {

        var iframe = document.getElementById("chatBotManFrame");

        iframe.addEventListener('load', function() {
            var htmlFrame = this.contentWindow.document.getElementsByTagName("html")[0];
            var bodyFrame = this.contentWindow.document.getElementsByTagName("body")[0];
            var headFrame = this.contentWindow.document.getElementsByTagName("head")[0];

            var image = ""

            htmlFrame.style.backgroundImage = "url(" + image + ")";
            bodyFrame.style.backgroundImage = "url(" + image + ")";
        });
    });

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    $(document).on('change', '#country_top', function(e) {
        var country_id = $(this).val();
        var country_code = $('option:selected', this).attr('data-code');
        outerhtml = '';
        if ((country_code != '') && (country_code != undefined)) {
            $("#country_code_top").val('+' + country_code);

        } else {
            $("#country_code_top").val('');

        }
    });
    $(document).on('click', '.drop', function() {
        var x = document.getElementById("cart-btn-id").getAttribute("aria-expanded");
        var y = document.getElementById("log-btn-id").getAttribute("aria-expanded");
        if (x == "true") {
            $('#collapseLogin').removeClass("show");
        }
        if (y == "true") {
            $('#collapsecart').removeClass("show");
        }
    });

    $(document).on('click', '#send_msg', function() {
        if (cookiechatID != 0) {
            var message = $.trim($('#userText').val());
            var html = '';
            if (message != '') {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '{{ route('chat_message.send') }}',
                    data: {
                        chatID: cookiechatID,
                        message: message,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result == 'success') {
                            $('#userText').val('');

                            html += '<li data-message-id="" class="chatbot_customer visitor" style="margin-left:210px" data-chatid="' + cookiechatID + '" data-id="' + response.chatmsgID + '">';
                            html += '<div class="msg me-chat">';
                            html += '<div>';
                            html += '<p>' + response.last_message + '</p>';
                            html += '</div>';
                            // html += '<div class="time">17:19</div>';
                            html += '</div>';
                            html += '</li>';

                            $("#messageArea").find('ol').append(html);
                        } else {
                            $('#alert_message').show().find('p').text(response.message);
                            $('#alert_message').delay(1000).fadeOut();
                        }
                    }
                });
            }
        }
    });

    $("document").ready(function($) {
        var nav = $('#main-nav-outer');

        $(window).scroll(function() {
            if ($(this).scrollTop() > 125) {
                nav.addClass("f-nav");
            } else {
                nav.removeClass("f-nav");
            }
        });
    });


    @auth('user')
        @foreach ($carts as $value)
            cart.push({
                product_id: {{ $value->product_id }},
                product_url: '{{ $value->product_url }}',
                product_image: '{{ isset($value->product_image) && $value->product_image != '' ? $value->product_image : '' }}',
                product_name: '{{ $value->product_name }}',
                quantity: {{ $value->quantity }},
                price: {{ $value->ProductPrice }},
                offer_price: {{ $value->offer_price }},
                original_price: {{ $value->original_price }},
                tax_details: {!! $value->tax_details != '' ? $value->tax_details : 0 !!},
                offer_percent: {{ $value->offer_percent != '' ? $value->offer_percent : 0 }},
                total_amount: {{ $value->ProductPrice * $value->quantity }},
            });
        @endforeach

        @if (!empty($rejectedItems) && is_array($rejectedItems))
            // @foreach ($rejectedItems as $value)
            // rejectedItems.push({{ $value }});
            //
        @endforeach
        //
    @endif
    @endauth

    @guest('user')
        @foreach ($carts as $value)
            cart.push({
                product_id: {{ $value['product_id'] }},
                product_url: '{{ $value['product_url'] }}',
                product_image: '{{ $value['product_image'] != '' ? $value['product_image'] : null }}',
                product_name: '{{ $value['product_name'] }}',
                quantity: '{{ $value['quantity'] }}',
                price: '{{ $value['ProductPrice'] }}',
                offer_price: '{{ $value['offer_price'] }}',
                original_price: '{{ $value['original_price'] }}',
                offer_percent: '{{ $value['offer_percent'] }}',
                tax_details: {!! isset($value['tax_details']) ? $value['tax_details'] : 0 !!},
                total_amount: '{{ $value['ProductPrice'] * $value['quantity'] }}',
            });
        @endforeach
    @endguest

    countcart = cart.length;
    DisplayCart();


    //----------------Login script--

    function loginpasswordShow() {
        var x = document.getElementById("password");
        document.getElementById('login-pass').classList.toggle('fa-eye-slash');
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
    $(document).on('click', '#login_button', function(e) {


        outerhtml = '';
        var username = $("#username").val();
        var password = $("#password").val();
        outerhtml = $("#login_button_outer").html();
        if (username != '' && password != '') {
            $("#btn_contact").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
            $.ajax({
                type: "post",
                data: {
                    email: username,
                    password: password,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: 'json',
                url: "{{ route('user.login') }}", //Please see the note at the end of the post**
                success: function(res) {

                    if (res.status == 'success') {
                        var currenturl = document.URL;
                        var current_url = currenturl.split("/");
                        if (current_url.includes('createuser')) {
                            window.location.href = "{{ URL::to('/myaccount') }}";
                        } else {
                            window.location.reload();
                        }
                    } else {
                        $("#login_alerts").show()
                        $("#login_alerts").text(res.message).delay(3000).fadeOut();
                        $("#login_button_outer").html(outerhtml);
                    }
                }
            });
        } else {
            $("#login_alerts").html('Enter your Email & Password').delay(3000).fadeOut();
            $("#login_button_outer").html(outerhtml);
        }
    });

    $(document).on('click', '#otp_request_button,#resend_otp', function(e) {
        var mobile = $("#mobile").val();
        var country = $("#country_top").val();
        var otphtml = '';
        if (mobile != '') {
            $("#otp_login_alerts").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
            $.ajax({
                type: "post",
                data: {
                    phone: mobile,
                    country: country,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: 'json',
                url: "{{ route('user.otp_request') }}", //Please see the note at the end of the post**
                success: function(res) {

                    if (res.result) {
                        var toggle = "Menu1";
                        otphtml += '<div class="login-fields">';
                        otphtml += '<input name="country_top" id="country_top" hidden type="text" value="' + res.country + '" />';
                        otphtml += '<input name="mobile" id="mobile" hidden type="text" value="' + res.phone + '" />';
                        otphtml += '<input name="otp" id="otp" type="text" placeholder="Enter 6 Digit OTP" />';
                        otphtml += '<a href="javascript:void(0)" id="resend_otp">Resend OTP</a>';
                        otphtml += '</div>';

                        otphtml += '<div class="login-alerts text-danger"><small id="otp_login_alerts"></small></div>';
                        otphtml += '<div class="login-fields" id="login_button_outer">';
                        otphtml += '<button name="login" class="login-button otp-request-button" id="otp_login_button">Login</button>';
                        otphtml += '</div>';
                        otphtml += '<div class="create-account text-left">';
                        // otphtml += '<a href="#" onclick="toggleVisibility('+toggle+');">Login With Username & Password </a>';
                        otphtml += '</div>';
                        $("#Menu2").html(otphtml);

                        // var currenturl = document.URL;
                        // var current_url = currenturl.split("/");
                        // if (current_url.includes('createuser')) {
                        //     window.location.href = "{{ URL::to('/myaccount') }}";
                        // } else {
                        //     window.location.reload();
                        // }
                    } else {
                        $("#otp_login_alerts").show()
                        $("#otp_login_alerts").text(res.message).delay(3000).fadeOut();

                    }
                }
            });
        } else {
            $("#otp_login_alerts").show()
            $("#otp_login_alerts").html('Enter your Mobile Number').delay(3000).fadeOut();
        }
    });

    $(document).on('click', '#otp_login_button', function(e) {
        outerhtml = '';
        var mobile = $("#mobile").val();
        var otp = $("#otp").val();

        outerhtml = $("#login_button_outer").html();
        if (otp != '') {
            $("#btn_contact").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
            $.ajax({
                type: "post",
                data: {
                    phone: mobile,
                    otp: otp,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: 'json',
                url: "{{ route('user.otp_login') }}", //Please see the note at the end of the post**
                success: function(res) {
                    if (res.status == 'success') {
                        var currenturl = document.URL;
                        var current_url = currenturl.split("/");
                        if (current_url.includes('createuser')) {
                            window.location.href = "{{ URL::to('/myaccount') }}";
                        } else {
                            window.location.reload();
                        }
                    } else {
                        $("#otp_login_alerts").show()
                        $("#otp_login_alerts").text(res.message).delay(3000).fadeOut();
                        $("#login_button_outer").html(outerhtml);
                    }
                }
            });
        } else {
            $("#otp_login_alerts").html('Please Enter your OTP').delay(3000).fadeOut();
            $("#otp_login_alerts").html(outerhtml);
        }
    });

    $(document).on('click', '#create_account, #register_account', function() {
        $('#btn_signup').trigger('click');
    });

    $(document).on('click', '.User_login_drop', function() {
        $('#btn_login').trigger('click');
    });

    //----------------Search products script--
    $(document).on('click', '.search_category', function() {
        var categoryId = $(this).attr('data_id');
        var categoryName = $(this).text().trim();
        var categoryName_val = '';

        if (categoryName.length > 14) {
            categoryName_val = categoryName.substring(0, 14) + '..';
        } else {
            categoryName_val = categoryName;
        }
        if (categoryId != '') {
            $('#hid_searchCategory').val(categoryId);
            $('#dropdownMenuButton').text(categoryName_val);
            $('#hid_searchCategoryname').val(categoryName);
        }
    });

    //----------------Wishlist script--
    $(document).on('click', '.add_wishlist', function(e) {
        var elm = $(this);
        var product_id = elm.attr("data_item");

        if (product_id != '') {
            var wishlistcontainer = elm.parent();
            $.ajax({
                type: "post",
                data: {
                    "_token": "{{ csrf_token() }}",
                    product_id: product_id,
                },
                dataType: 'json',
                url: "{{ route('add.wishlist') }}",
                success: function(res) {
                    if (res.status == 'success') {
                        swal({
                            title: res.mode.toUpperCase(),
                            text: res.message,
                            type: 'success',
                            timer: 1500,
                            showCancelButton: false,
                            showConfirmButton: false
                        });

                        if (res.mode == 'removed') {
                            elm.html('<img src="{{ asset('front_view/images/wishlist.png') }}">');
                        } else if (res.mode == 'added') {
                            elm.html('<img src="{{ asset('front_view/images/star-icon.png') }}">');
                        }
                    } else {
                        swal({
                            title: 'Failed',
                            text: res.message,
                            type: 'error',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false
                        });
                    }
                }

            });
        } else {
            swal({
                title: 'Failed',
                text: 'Something went wrong',
                type: 'error',
                timer: 2000,
                showCancelButton: false,
                showConfirmButton: false
            });
        }
    });

    $(document).ready(function() {

        $("#search_keyword").autocomplete(

            {
                source: function(request, response) {
                    $.getJSON("{{ url('autocomplete/itemsearch') }}", {
                            categoryid: $('#hid_searchCategory').val(),
                            term: $('#search_keyword').val()
                        },
                        response);
                },
                minLength: 2,
                search: function() {
                    $(this).addClass('working-loader');
                },
                open: function() {
                    $(this).removeClass('working-loader');
                },
                focus: function(event, ui) {
                    $("#search_keyword").val(ui.item.title); // uncomment this line if you want to select value to search box
                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length == 0) {
                        $(this).removeClass('working-loader');
                        $('.no-pro').remove();
                        $('.header-search').parent().append("<div class='no-pro'> <li class='ui-menu-item'>Oops we could't find what you were looking for! Please <a href='https://api.whatsapp.com/send?phone=" + whatsapp + "&text=Hey,%20I%20am%20looking%20for%20your%20services' target='_blank'>whatsapp</a> us with your looking products details. We will check in our store and get back to you. Also You can check in our <a href=" + list_products_url + ">list.</a></li></div>");

                    }
                },
                select: function(event, ui) {
                    window.location.href = ui.item.product_url;
                }
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
            $('.no-pro').html("");
            // alert(item.content.length);
            // console.log(item);
            var offer_price = parseFloat(item.offer_price);
            var price = parseFloat(item.price);
            var product_url = "{{ url('item') }}" + '/' + item.product_url

            var inner_html = '<a href="' + product_url + '" ><div class="list_item_container"><div class="image">';
            if (item.product_image != null && item.product_image != '') {
                inner_html += '<img src="' + product_image_path + '/' + item.product_image + '" >';
            } else {
                inner_html += '<img src="' + assets_path + '/no-image.jpg">';
            }
            inner_html += '</div><div class="label"><h4><b>' + item.product_name + '</b></h4></div>';

            if (offer_price == 0) {
                inner_html += '<div class="search_amount">' + currencyIcon + ' ' + price.toFixed(2) + '</div>';
            } else {
                inner_html += '<div class="search_amount">' + currencyIcon + ' ' + offer_price.toFixed(2) + '<div class="search_old_price old-price" style="color:#848484">' + currencyIcon + ' ' + price.toFixed(2) + '</div></div>';
            }
            inner_html += '</div></a></div>';

            return $("<li></li>")
                .data("item.autocomplete", item)
                .append(inner_html)
                .appendTo(ul);
        };

    });

    $(document).ready(function() {
        $('#search_keyword').keyup(function() {
            if ($('#search_keyword').val() === '') {
                $(this).removeClass('working-loader');
            }
        });
    });

    var divs = ["Menu1", "Menu2", "Menu3", "Menu4"];
    var visibleDivId = null;

    function toggleVisibility(divId) {
        if (visibleDivId === divId) {} else {
            visibleDivId = divId;
        }
        hideNonVisibleDivs();
    }

    function hideNonVisibleDivs() {
        var i, divId, div;
        for (i = 0; i < divs.length; i++) {
            divId = divs[i];
            div = document.getElementById(divId);
            if (visibleDivId === divId) {
                div.style.display = "block";
                // $(".login-two").css({display: "block"});
            } else {
                // $(".login-one").css({display: "none"});
                div.style.display = "none";

            }
        }
    }

    $(document).on('keyup', '#search_medicine_keyword', function(e) {
        var search = $(e.target).val();
        var all_med_cat_id = $('#all_med_category').val();
        if (search != '') {
            $.getJSON("{{ url('autocomplete/itemsearch') }}", {
                categoryid: all_med_cat_id,
                term: search
            }, function(data) {
                // document.getElementById('#sear').inner_html = '';
                var inner_html = '';
                if (data.length > 0) {
                    $.each(data, function(key, item) {
                        var offer_price = parseFloat(item.offer_price);
                        var price = parseFloat(item.price);
                        var product_url = "{{ url('item') }}" + '/' + item.product_url

                        inner_html += '<div class="list_item_container">';
                        inner_html += '<div class="image">';
                        if (item.product_image != null && item.product_image != '') {
                            inner_html += '<img src="' + product_image_path + '/' + item.product_image + '" >';
                        } else {
                            inner_html += '<img src="' + assets_path + '/no-image.jpg">';
                        }
                        inner_html += '</div><div class="label"><h4><b>' + item.product_name + '</b></h4>';
                        inner_html += '</div>';

                        if (offer_price == 0) {
                            inner_html += '<div class="search_amount">' + currencyIcon + ' ' + price.toFixed(2) + '</div>';
                        } else {
                            inner_html += '<div class="search_amount">' + currencyIcon + ' ' + offer_price.toFixed(2) + '<div class="search_old_price old-price" style="color:#848484">' + currencyIcon + ' ' + price.toFixed(2) + '</div></div>';

                        }


                        inner_html += '<div class="product-incre-decre-outer sec_productadd">';
                        inner_html += '<div class="product-incre-decre">';
                        inner_html += '<div class="input-group">';
                        inner_html += '<input type="text" hidden class="form-control input-number" value="1" min="1" max="100" name="quant[1]">';
                        inner_html += '</div>';
                        inner_html += '</div>';
                        inner_html += '<div class="sin-outer">';
                        inner_html += '<a href="javascript:void(0)" class="add-cart-list add-cart-list_' + item.id + ' ' + (item.flag == 1 || item.not_for_sale == 1 ? 'disable' : '') + '" id="add-cart-list_' + item.id + '" value="' + item.id + '"><i class="fas fa-shopping-cart"></i>Add</a>';
                        inner_html += '</div>';
                        inner_html += '</div>';


                        inner_html += '</div>';
                        inner_html += '</div>';



                    });
                } else {
                    inner_html += '<span>No results found</span>';
                }
                $('#sear').html(inner_html);


            });
        } else {
            $('#sear').html('');
        }
    });

    $(document).on('click', '#newsleter_submit', function(event) {
        var email = $('#newsletter_email').val();
        if (email != '') {
            // var outerhtml = $("#subscribe_button_outer").html();
            $.ajax({
                url: "{{ route('newsletter.subscribe') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    email: email,
                },
                beforeSend: function() {
                    $("#subscribe_button_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#subscribe_button_outer").html('<button type="button" id="newsleter_unsubscibe" class="btn">Unsubscribe</button>');
                        $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                    } else {
                        if (response.subscribe_status == 'subscribed') {
                            $("#subscribe_button_outer").html('<button type="button" id="newsleter_unsubscibe" class="btn">Unsubscribe</button>');
                            $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                        } else {
                            $("#subscribe_button_outer").html('<button type="button" id="newsleter_submit" class="btn">Subscribe</button>');
                            $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                        }
                    }
                },
            });
        } else {
            $('#newsletter_alert').show().text('Please enter your email.').delay(3000).fadeOut();
        }
    });
    $(document).on('click', '#newsleter_unsubscibe', function(event) {
        var email = $('#newsletter_email').val();
        if (email != '') {
            $("#subscribe_button_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
            $.ajax({
                url: "{{ route('newsletter.unsubscribe') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    email: email,
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#subscribe_button_outer").html('<button type="button" id="newsleter_submit" class="btn">Subscribe</button>');
                        $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                    } else {
                        if (response.subscribe_status == 'subscribed') {
                            $("#subscribe_button_outer").html('<button type="button" id="newsleter_unsubscibe" class="btn">Unsubscribe</button>');
                            $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                        } else {
                            $("#subscribe_button_outer").html('<button type="button" id="newsleter_submit" class="btn">Subscribe</button>');
                            $('#newsletter_alert').show().text(response.message).delay(3000).fadeOut();
                        }
                    }
                },
            });
        }
    });

    (function() {
        "use strict";
        var carousels = function() {
            $(".owl-carousel1").owlCarousel({
                loop: true,
                center: true,
                margin: 0,
                responsiveClass: true,
                nav: false,

                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    680: {
                        items: 2,
                        nav: false,
                        loop: false
                    },
                    1000: {
                        items: 3,
                        nav: true
                    }
                }
            });
        };

        (function($) {
            carousels();
        })(jQuery);
    })();
</script>
