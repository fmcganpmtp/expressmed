<!-- My order history view -->

<div class="pb-90" id="nav-changepassword" role="tabpanel" aria-labelledby="nav-orderhistory-tab">

    <div class="row">
        <p class="text-uppercase font-weight-bold cyan mb-3">Change Password</p>
        <div class="col-md-12">

         <form method="POST" action="{{route('myaccount.updatepassword')}}">
            @csrf
            <div class="form-group row">
            <label>Current Password</label>
            <input type="password" name='current_password' class="form-control" id="current_password" value="{{old('current_password')}}" placeholder="Current password">
            <i onclick="changecurrentpasswordShow()" class="fa fa-eye" id="change_current_pass"></i>

            @if($errors->has('current_password'))<span class="text-danger">{{ $errors->first('current_password') }}</span>@endif

            </div>
            <div class="form-group row">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" id="change_password" value="{{old('new_password')}}" placeholder="New  password">
            <i onclick="changepasswordShow()" class="fa fa-eye" id="change_pass"></i>

            @if($errors->has('new_password'))<span class="text-danger">{{ $errors->first('new_password') }}</span>@endif

            </div>
            <div class="form-group row">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_new_password" class="form-control" id="change_confirmpassword" value="{{old('confirm_new_password')}}" placeholder="Confirm password">
            <i onclick="changecpasswordShow()" class="fa fa-eye" id="change_cpass"></i>

            @if($errors->has('confirm_new_password'))<span class="text-danger">{{ $errors->first('confirm_new_password') }}</span>@endif

            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>

         </form>
        </div>
        <div class="col-md-12">
            <div id="accordionExample">
                @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                @php
                    $orderstatus = '';
                    $buttonname = '';
                @endphp


            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $(document).on('click', '.review_delete', function() {
            if (confirm('Are you sure do you want to delete this product review?')) {
                var product_id = $(this).closest('button').attr('data-id');
                $.ajax({
                    type: "post",
                    data: {
                        product_id: product_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    url: "{{ route('customer.review.delete') }}",
                    success: function(response) {
                        if (response.ajax_status == "success") {
                            $('#alert-success').html('Review And Rating Deleted Successfully.').show();
                            setTimeout(location.reload.bind(location), 1500);

                        }
                    }

                })
            }

        });

    });
    function changecurrentpasswordShow() {
            var x = document.getElementById("current_password");
            document.getElementById('change_current_pass').classList.toggle('fa-eye-slash');
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    function changepasswordShow() {
            var x = document.getElementById("change_password");
            document.getElementById('change_pass').classList.toggle('fa-eye-slash');
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function changecpasswordShow() {
            var x = document.getElementById("change_confirmpassword");
            document.getElementById('change_cpass').classList.toggle('fa-eye-slash');
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
</script>
