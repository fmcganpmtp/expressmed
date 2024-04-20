@extends('layouts.frontview.app')
@section('content')
    @include('layouts.frontview.topmenubar_frontview')


    <section class="prof-account">
        <div class="width-container prof-container">
            <div class="col-md-12 full-prof">
                <div class="row">
                    <div class="col-md-4 left-prof">
                        <div class="panel">

                            <div class="prf-cont-outer">
                                <div class="profile-pic-outer" id="profile_pic">
                                    <a href="javascript:void(0)" id="edit_profile_pic"><i class="fas fa-pencil-alt"></i></a>
                                    @if (Auth::guard('user')->user()->profile_pic)
                                        <img src="{{ asset('/assets/uploads/profile/' . Auth::guard('user')->user()->profile_pic) }}" class="img-fluid mx-auto d-block rounded-circle box-shadow" width="50%" alt="Profile Pic">
                                    @else
                                        <img src="{{ asset('/front_view/images/profile-dummy.jpg') }}" class="img-fluid mx-auto d-block rounded-circle box-shadow" width="50%" alt="Profile Pic">
                                    @endif
                                </div>
                                <input type="file" name="profile_pic_fld" id="profile_pic_fld" style="width:0;height:0" />

                                <h6 class="prof-name">{{ Auth::guard('user')->user()->name }}</h6>
                                <p class="login-text prof-address">

                                    @if (isset($primary_address))
                                        <span><i class="fas fa-map-marker-alt"></i>
                                            {{ ucfirst($primary_address->location)}}{{($primary_address->city)?', '.ucfirst($primary_address->city):'' }}
                                        </span>
                                    @endif

                                </p>
                            </div>
                            <!-- <hr class="prof-line"> -->
                            <div class="prof-edit-btn">
                                <a href="javascript:void(0)" class="btn btn-primary" id="editProfile">Edit</a>

                            </div>
                            <div id="profile_messages"></div>

                            <div id="profile_information" class="pdetails">
                                <h6>Personal Details</h6>
                                <p class="pdetails-name"><span>Name : </span>{{ ucfirst(Auth::guard('user')->user()->name) }}</p>
                                <p class="pdetails-mail"><span>Email : </span>{{ Auth::guard('user')->user()->email }} {!! Auth::guard('user')->user()->verified == 1 ? '<i class="fas fa-check-circle"></i><small>Verified</small>' : '' !!}</p>
                                <p class="pdetails-address"><span>Phone : </span>{{ isset($country_details->phonecode) ? '+' . $country_details->phonecode : '' }} {{ Auth::guard('user')->user()->phone }}</p>
                            </div>

                            <div id="profile_information_edit" class="personal-dtls" style="display:none;">
                                <h6>Personal Details</h6>
                                <div class="profile-details">
                                    <label>Name :</label><input name="register_name" id="register_name" class="profile-form" type="text" placeholder="Name" value="{{ Auth::guard('user')->user()->name }}" />
                                </div>
                                <div class="profile-details">
                                    <label>Email :</label><input name="register_email" id="register_email" class="profile-form" type="email" placeholder="Email ID" value="{{ Auth::guard('user')->user()->email }}" />
                                </div>
                                {{-- <div class="profile-details"> --}}

                                <div class="input-group register-form edit-flag">
                                    <label>Country :</label>
                                    <select class="form-control selectpicker" id="edit_country" name="register_country" data-live-search="true" required>
                                        <option value="">Choose Your Country</option>
                                        @foreach ($countries as $row)
                                            <option value="{{ $row->id }}" data-code="{{ $row->phonecode }}" data-tokens="{{ $row->name }}" @if (Auth::guard('user')->user()->country_id == $row->id) selected @endif data-content="<div class='flag-outer'><img src='{{ asset('assets/uploads/countries_flag') . '/' . $row->flag_icon }}'></div> <span class='text-dark'>{{ $row->name }}</span>">{{ $row->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <input name="register_country" id="  " class="profile-form" type="email" placeholder="Country" value="" /> --}}
                                {{-- </div> --}}
                                <div class="profile-details profile-phone">
                                    <label>Phone :</label>

                                    <input type="text" id="edit_country_code" class="form-control" value="{{ isset($country_details->phonecode) ? $country_details->phonecode : '' }}" autocomplete="off" placeholder="Code" disabled />

                                    <input name="register_phone" id="register_phone" class="profile-form" type="tel" placeholder="Phone" value="{{ Auth::guard('user')->user()->phone }}" />
                                </div>
                                <div class="profile-details" id="profile_btn_outer">
                                    <button type="button" class="btn btn-success" id="profile_save">Save</button>
                                    <a href="javascript:void(0)" class="btn btn-primary" id="cancel_profile_edit">cancel</a>
                                </div>
                            </div>

                            <div class="prof-order-tab-outer">
                                <!--  <nav class="prof-order-tab"> -->
                                <!-- <div class="nav nav-tabs nav-fill border-0" id="nav-tab" role="tablist"> -->
                                <a class="nav-item nav-link text-uppercase {{ isset($subview_page) && $subview_page == 'frontview_customer.my_profile' ? 'active' : '' }}" id="nav-profile-tab" href="{{ route('user.myaccount') }}" role="tab" aria-controls="nav-home" aria-selected="true">My Profile</a>
                                <!-- {{-- <span class="text-white"> | </span> --}} -->
                                {{-- <a class="nav-item nav-link text-uppercase {{ isset($subview_page) && $subview_page == 'frontview_customer.wishlist' ? 'active' : '' }}" id="nav-wishlist-tab" href="{{ route('myaccount.wishlist') }}" role="tab" aria-controls="nav-wishlist" aria-selected="false">Wishlist</a> --}}
                                <!-- <span class="text-white"> | </span> -->
                                <a class="nav-item nav-link text-uppercase {{ isset($subview_page) && $subview_page == 'frontview_customer.order_history' ? 'active' : '' }}" id="nav-orderhistory-tab" href="{{ route('myaccount.orderhistory') }}" role="tab" aria-controls="nav-orderhistory" aria-selected="false">Order History</a>
                                <a class="nav-item nav-link text-uppercase {{ isset($subview_page) && $subview_page == 'frontview_customer.change_password' ? 'active' : '' }}" id="nav-orderhistory-tab" href="{{ route('myaccount.changepassword') }}" role="tab" aria-controls="change-password" aria-selected="false">Change Password</a>
                                <a class="nav-item nav-link text-uppercase" href="{{ route('myaccount.delete') }}" onclick="return confirm('Are you sure you want to delete this Account?');" role="tab" aria-controls="change-password" aria-selected="false">Delete Account</a>

                                <!-- </div> -->
                                <!-- </nav> -->
                            </div>

                        </div>
                    </div>
                    <div class="col-md-8 right-prof">
                        <div class="login-banner">
                            <p> <span>{{ $tot_Purchase }}</span><br> Purchases</p>
                        </div>


                        <div class="col-md-12 mtop-41 prof-tab" id="tabs">
                            <div class="col-md-12 alert alert-success" id="alert-success" style="display:none"></div>
                            <div class="row">

                                <div class="tab-content prof-tab-cont" id="nav-tabContent">
                                    @if(Session::has('success'))
                                    <div class="alert alert-success" id="success_id">

                                      {{ session('success') }}

                                    </div>
                                @endif
                                    @include($subview_page)

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!---container--->
    </section>
@endsection

@section('footer_scripts')
    <script>
           $("#success_id").show().delay(3000).fadeOut();
        $(document).on("click", "#editProfile", function(e) {
            e.preventDefault();
            $("#profile_information").toggle();
            $("#profile_information_edit").toggle();

        });
        $(document).on("click", "#cancel_profile_edit", function(e) {
            e.preventDefault();
            // var name = '{{ Auth::guard('user')->user()->name }}'
            // var email = '{{ Auth::guard('user')->user()->email }}'
            // var phone = '{{ Auth::guard('user')->user()->phone }}'
            // var phonecode='{{ isset($country_details->phonecode) ? $country_details->phonecode : '' }}'

            // $('#register_name').val(name);
            // $('#register_email').val(email);
            // $('#register_phone').val(phone);
            // $('#edit_country_code').val(phonecode);

            $("#profile_information").toggle();
            $("#profile_information_edit").toggle();

        });

        $('#register_name, #register_email, #register_phone').on('keyup', function() {
            $("#profile_messages").delay(200).fadeOut();
        });

        $(document).on('change', '#edit_country', function(e) {
            var country_id = $(this).val();
            var country_code = $('option:selected', this).attr('data-code');
            outerhtml = '';
            if ((country_code != '') && (country_code != undefined)) {
                $("#edit_country_code").val('+' + country_code);

            } else {
                $("#edit_country_code").val('');

            }
        });

        $(document).on("click", "#profile_save", function(e) {
            var outerhtml = $("#profile_btn_outer").html();

            var name = $('#register_name').val();
            var email = $('#register_email').val();
            var country = $('#edit_country').val();
            var phone = $('#register_phone').val();
            if (name == '') {
                $("#profile_messages").html('<span class="text-danger">Enter your name</span>').show();
            } else if (email == '') {
                $("#profile_messages").html('<span class="text-danger">Enter your email address</span>').show();
            } else if (phone == '') {
                $("#profile_messages").html('<span class="text-danger">Enter your phone number</span>').show();
            } else if (country == '') {
                $("#profile_messages").html('<span class="text-danger">Please choose your country</span>').show();
            } else {
                $("#profile_btn_outer").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        name: name,
                        email: email,
                        phone: phone,
                        country: country,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('update.profile') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            $("#profile_btn_outer").html(outerhtml);
                            $("#profile_messages").html('<span class="text-success">' + res.message + '</span>');
                            $("#profile_messages").show().delay(2000).fadeOut();
                            $("#profile_information").toggle();
                            $("#profile_information_edit").toggle();
                            location.reload().delay(3000);
                        } else {

                            $("#profile_btn_outer").html(outerhtml);
                            $("#profile_messages").html('<span class="text-danger">' + res.message + '</span>').show();
                        }
                    }

                });
            }
        });

        $(document).on("click", "#edit_profile_pic", function(e) {
            e.preventDefault();
            $("#profile_pic_fld").trigger("click");
        });

        $(document).on("change", "#profile_pic_fld", function(e) {
            var outerHTMl = $("#profile_pic").html();
            $("#profile_pic").html(loader_gif);
            formData = new FormData();
            fileupload = document.getElementById("profile_pic_fld");

            formData.append("file", fileupload.files[0]);
            formData.append("_token", "{{ csrf_token() }}");
            $.ajax({
                type: "post",
                data: formData,
                enctype: 'multipart/form-data',
                contentType: false,
                processData: false,
                url: "{{ route('update.profilepic') }}",
                success: function(res) {
                    if (res.ajax_status == 'success') {
                        window.location.reload();
                    } else {
                        $("#profile_pic").html(outerHTMl);
                        $("#profile_messages").html(res.message);
                        $("#profile_messages").show().delay(3000).fadeOut();
                    }
                }
            });
        });

        $(document).on("click", "#addAddresss", function(e) {
            e.preventDefault();
            $("#adderssEditForm").hide();
            $("#adderssCreateForm").toggle();
            $('#adderssCreateForm')[0].scrollIntoView(true);
        });

        $(document).on('change', '#country', function(e) {
            var country_id = $(this).val();
            outerhtml = '';
            if (country_id != '') {
                outerhtml = $("#outer_ajaxstate").html();
                $("#outer_ajaxstate").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        id: country_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    url: "{{ route('ajax.stateLoader') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            var state_html = '';
                            state_html += '<select name="state" id="ajx_state">';
                            state_html += '<option value="">--Choose State--</option>';
                            $.each(res.states, function(index, item) {
                                state_html += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            state_html += '</select>';
                            $("#outer_ajaxstate").html(state_html);
                        } else {

                            $("#outer_ajaxstate").html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }

                });
            } else {
                var state_html = '<select name="state" id="ajx_state">';
                state_html += '<option value="">--Choose State--</option>';
                state_html += '</select>';
                $("#outer_ajaxstate").html(state_html);
            }
        });

        $(document).on('change', '#edit_country,#edit_ajax_country', function(e) {
            var country_id = $(this).val();
            outerhtml = '';
            if (country_id != '') {
                outerhtml = $("#outer_ajaxstate_edit").html();
                $("#outer_ajaxstate_edit").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        id: country_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    url: "{{ route('ajax.stateLoader') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            var state_html = '';
                            state_html += '<select name="edit_state" id="edit_ajx_state">';
                            state_html += '<option value="">--Choose State--</option>';
                            $.each(res.states, function(index, item) {
                                state_html += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            state_html += '</select>';
                            $("#outer_ajaxstate_edit").html(state_html);
                        } else {

                            $("#outer_ajaxstate_edit").html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            } else {
                var state_html = '<select name="state" id="ajx_state">';
                state_html += '<option value="">--Choose State--</option>';
                state_html += '</select>';
                $("#outer_ajaxstate_edit").html(state_html);
            }
        });

        $(document).on("click", "#address_add", function(e) {
            e.preventDefault();
            var outerhtml = $("#addressAdd_btn_outer").html();

            address_name = $('#address_name').val();
            address_phone = $('#address_phone').val();
            address_pin = $('#address_pin').val();
            address_location = $('#address_location').val();
            address_address = $('#address_address').val();
            address_town = $('#address_town').val();
            address_landmark = $('#address_landmark').val();
            country = $('#country').val();
            ajx_state = $('#ajx_state').val();
            address_type = $('input[name="address_type"]:checked').val();

            if (address_name == '') {
                $("#error_address").text('Enter your name');
                $("#error_address").show().delay(3000).fadeOut();
            } else if (address_pin == '') {
                $("#error_address").text('Enter your Pin number');
                $("#error_address").show().delay(3000).fadeOut();
            } else if (address_location == '') {
                $("#error_address").text('Enter your location');
                $("#error_address").show().delay(3000).fadeOut();
            } else {
                $("#addressAdd_btn_outer").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        address_name: address_name,
                        address_phone: address_phone,
                        address_pin: address_pin,
                        address_location: address_location,
                        address_address: address_address,
                        address_town: address_town,
                        country: country,
                        state: ajx_state,
                        address_type: address_type,
                        address_landmark: address_landmark,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('profile.add.address') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            window.location.reload();
                        } else {
                            $("#addressAdd_btn_outer").html(outerhtml);
                            $("#error_address").html(res.message);
                            $("#error_address").show().delay(3000).fadeOut();
                        }
                    }
                });
            }
        });

        $(document).on("click", ".edit-address-butn", function(e) {
            var addres_id = $(this).attr("data-item");
            $(".profile-col").show();
            $("#address_list_outer_" + addres_id).hide();
            $("#adderssCreateForm").hide();
            $("#adderssEditForm").show();
            if (addres_id) {
                $.ajax({
                    type: "post",
                    data: {
                        addres_id: addres_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('profile.getaddress') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {

                            $(".rad_address_type[value='" + res.address_data.type + "']").prop('checked', true);

                            $("#edit_address_name").val(res.address_data.name);
                            $("#edit_address_phone").val(res.address_data.phone);
                            $("#edit_address_pin").val(res.address_data.pin);
                            $("#edit_address_location").val(res.address_data.location);
                            $("#edit_address_address").val(res.address_data.address);
                            $("#edit_address_town").val(res.address_data.city);
                            $("#edit_address_landmark").val(res.address_data.landmark);
                            // $("#edit_country").val(res.address_data.country_id);
                            $("#edit_hid_address_id").val(res.address_data.id);

                            var country_html = '';
                            var state_html = '';
                            country_html += '<select name="edit_country" id="edit_ajax_country">';


                            $.each(res.countries, function(index, item) {
                                // alert(res.address_data.country_id);
                                if (item.id == res.address_data.country_id)
                                    country_html += '<option value="' + item.id + '" selected>' + item.name + '</option>';
                                else
                                    country_html += '<option value="' + item.id + '" >' + item.name + '</option>';
                            });
                            country_html += '</select>';

                            state_html += '<select name="edit_state" id="edit_ajx_state">';
                            $.each(res.state, function(index, item) {
                                if (item.id == res.address_data.state_id)
                                    state_html += '<option value="' + item.id + '" selected>' + item.name + '</option>';
                                else
                                    state_html += '<option value="' + item.id + '" >' + item.name + '</option>';
                            });
                            state_html += '</select>';
                            $("#outer_ajaxcountry_edit").html(country_html);

                            $("#outer_ajaxstate_edit").html(state_html);

                        } else {
                            $("#addressAdd_btn_outer").html(outerhtml);
                            $("#error_address").html(res.message);
                            $("#error_address").show().delay(3000).fadeOut();
                        }
                    }
                });
            }

        });

        $(document).on("click", "#address_edit_cancel", function(e) {
            $(".profile-col").show();
            $("#adderssEditForm").hide();
        });

        $(document).on("click", "#address_edit", function(e) {
            e.preventDefault();
            var outerhtml = $("#addressEdit_btn_outer").html();

            address_name = $('#edit_address_name').val();
            address_phone = $('#edit_address_phone').val();
            address_pin = $('#edit_address_pin').val();
            address_location = $('#edit_address_location').val();
            address_address = $('#edit_address_address').val();
            address_town = $('#edit_address_town').val();
            address_landmark = $('#edit_address_landmark').val();
            country = $('#edit_ajax_country').val();
            ajx_state = $('#edit_ajx_state').val();
            address_type = $('input[name="edit_address_type"]:checked').val();
            address_id = $('#edit_hid_address_id').val();
            if (address_name == '') {
                $("#error_address").text('Enter your name');
                $("#error_address").show().delay(3000).fadeOut();
            } else if (address_pin == '') {
                $("#error_address").text('Enter your Pin number');
                $("#error_address").show().delay(3000).fadeOut();
            } else if (address_location == '') {
                $("#error_address").text('Enter your location');
                $("#error_address").show().delay(3000).fadeOut();
            } else {
                $("#addressEdit_btn_outer").html(loader_gif);
                $.ajax({
                    type: "post",
                    data: {
                        address_id: address_id,
                        address_name: address_name,
                        address_phone: address_phone,
                        address_pin: address_pin,
                        address_location: address_location,
                        address_address: address_address,
                        address_town: address_town,
                        country: country,
                        state: ajx_state,
                        address_type: address_type,
                        address_landmark: address_landmark,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('profile.updateaddress') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            window.location.reload();
                        } else {
                            $("#addressEdit_btn_outer").html(outerhtml);
                            $("#error_address").html(res.message);
                            $("#error_address").show().delay(3000).fadeOut();
                        }
                    }

                });
            }
        });

        $(document).on('click', '.profile_wishlist', function(e) {
            var elm = $(this);
            var product_id = elm.attr("data_item");

            var wishlistcontainer = elm.parent();
            outerhtml = wishlistcontainer.html();
            var html = loader_gif;
            wishlistcontainer.html(html);
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
                        wishlistcontainer.closest('.products-content').remove();
                        swal({
                            title: res.mode.toUpperCase(),
                            text: res.message,
                            type: 'success',
                            timer: 1500,
                            showCancelButton: false,
                            showConfirmButton: false
                        });
                    } else {
                        wishlistcontainer.html(outerhtml);
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
        });

        //-----keep star rating for products--
        $(document).on('click', '.star_rateproduct', function() {
            var starvalue = $(this).attr('data-star');
            // alert(starvalue);

            $(this).addClass('star-yellow').prevAll().addClass('star-yellow');
            $(this).nextAll().removeClass('star-yellow');

            $(this).closest('.rating_product').find('.hid_productrate').val(starvalue);
        });

        //-----Add rating and review for products--
        $(document).on('click', '.rateproduct', function() {
            var productid = $(this).attr('data-id');
            var starvalue = $(this).closest('.rating_product').find('.hid_productrate').val();
            var productreview = $(this).closest('.rating_product').find('.product_review').val();
            // if (starvalue == 0) {
            //     swal({
            //         title: 'Failed',
            //         text: 'Please Choose Your Star Rating',
            //         type: 'error',
            //         timer: 2000,
            //         showCancelButton: false,
            //         showConfirmButton: false
            //     });
            //     exit;
            // }

            if (productid != '' && (starvalue != 0 || productreview != '')) {
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        productid: productid,
                        starvalue: starvalue,
                        productreview: productreview
                    },
                    url: "{{ route('add.product.review') }}",
                    success: function(data) {
                        if (data.ajax_status == 'success') {
                            window.location.reload(true);
                        } else {
                            alert(data.message);
                        }
                    }
                });
            } else {
                alert('Please choose star rate or write product review.');
            }
        });

        //----print-Order-Invoice--
        $('.print_invoice').on('click', function() {
            var orderID = $(this).val();

            window.open('{{ url('/order/invoice/print/') }}/' + orderID, 'name', 'width=1000,height=800');
        });

        //-----Change order status for ordered product--
        function changestatus(order_id, productid, mode) {
            if (confirm("Are you sure do you want to cancel the product?")) {
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        order_id: order_id,
                        productid: productid,
                        mode: mode
                    },
                    url: "{{ route('change.orderstatus') }}",
                    success: function(data) {
                        console.log(data);
                        if (data.ajax_status == 'success') {
                            window.location.reload(true);
                        }
                    }
                });
            }
        }
    </script>
@endsection
