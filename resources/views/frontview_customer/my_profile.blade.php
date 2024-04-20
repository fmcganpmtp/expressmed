<!-- My profile view -->
<div class="tab-pane fade show active pb-90" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

    <div class="addAdress_outer"> <button type="button" class="btn btn-primary" id="addAddresss">Add Address</button></div>
    <div class="col-md-12 tab-sgle-dtls">
        <div class="row">

            @if ($user_address)
                @foreach ($user_address as $address)
                    <div class="col-md-4 prof-cont">
                        <div class="tab-details">
                            <p class="f-18 text-uppercase font-weight-bold cyan pb-10 pt-50 mb-0">{{ $address->type }} Address</p>
                            <p class="login-text text-dark mb-0 f-15 pb-25">
                                {{ ucfirst($address->name) }}<br />
                                {{ $address->phone }}<br />
                                {{ ucfirst($address->location) }}<br />
                                {{ ucfirst($address->address) }}
                                {{ ucfirst($address->city) }}<br />
                                {{ $address->pin }}<br />
                                {{ $address->countryname }}<br />
                                {{ $address->state }}<br />
                                {{ ucfirst($address->landmark) }}<br />
                            </p>
                            <a href="javascript:void(0)" class="btn btn-primary bg-cyan border-0 f-14 border-radius-19 px-3 edit-address-butn" data-item="{{ $address->id }}">Edit</a>
                        </div>
                    </div>
                @endforeach
            @else
                <h4>Please add any address.</h4>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="alert alert-danger" id="error_address" style="display:none"></div>
        {{-- Address Edit form --}}
        <div class="col-md-6" id="adderssEditForm" style="display:none">
            <div class="adderssEditForm">
                <div id="profile_information">
                    <div class="address-type">
                        <label>Type :</label>
                        <input type="radio" value="primary" id="edit_address_type1" class="rad_address_type" name="edit_address_type" checked />Primary
                        <input type="radio" value="home" id="edit_address_type2" class="rad_address_type" name="edit_address_type" />Home
                        <input type="radio" value="work" id="edit_address_type3" class="rad_address_type" name="edit_address_type" />Work
                    </div>
                    <div class="profile-details">
                        <label>Name :</label><input name="edit_address_name" id="edit_address_name" class="profile-form" type="text" placeholder="Name" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Phone :</label><input name="edit_address_phone" id="edit_address_phone" class="profile-form" type="text" placeholder="Phone" value="" />
                    </div>
                    <div class="profile-details">
                        <label>PIN :</label><input name="edit_address_pin" id="edit_address_pin" class="profile-form" type="text" placeholder="Pin" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Location :</label><input name="edit_address_location" id="edit_address_location" class="profile-form" type="text" placeholder="Location" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Address :</label>
                        <textarea name="edit_address_address" id="edit_address_address" class="profile-form" placeholder="Address"></textarea>
                    </div>
                    <div class="profile-details">
                        <label>Town :</label><input name="edit_address_town" id="edit_address_town" class="profile-form" type="text" placeholder="Town" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Country :</label>
                        <div id="outer_ajaxcountry_edit">
                            <select name="edit_country" id="edit_ajax_country">
                                <option value="">--Choose Country--</option>

                                @foreach ($countries as $row)
                                    @if ($row->name == 'India')
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="profile-details">
                        <label>State :</label>
                        <div id="outer_ajaxstate_edit">
                            <select name="edit_state" id="edit_ajx_state">
                                <option value="">--Choose State--</option>
                            </select>
                        </div>
                    </div>
                    <div class="profile-details">
                        <label>Landmark :</label><input name="edit_address_landmark" id="edit_address_landmark" class="profile-form" type="text" placeholder="Landmark" value="" />
                        <input type="hidden" id="edit_hid_address_id" name="edit_hid_address_id" value="" />
                    </div>
                    <div class="profile-details" id="addressEdit_btn_outer">
                        <button type="button" class="btn btn-success" id="address_edit">Save</button>
                        <button type="button" class="btn btn-light" id="address_edit_cancel">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Address form --}}
        <div class="col-md-6 prof-sec-cont" style="display: none" id="adderssCreateForm">

            <div class="adderssCreateForm">
                <div id="profile_information">
                    <div class="address-type">
                        <label>Type :</label>
                        <input type="radio" value="primary" id="address_type1" name="address_type" checked />Primary
                        <input type="radio" value="home" id="address_type2" name="address_type" />Home
                        <input type="radio" value="work" id="address_type3" name="address_type" />Work
                    </div>
                    <div class="profile-details">
                        <label>Name :</label><input name="address_name" id="address_name" class="profile-form" type="text" placeholder="Name" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Phone :</label><input name="address_phone" id="address_phone" class="profile-form" type="text" placeholder="Phone" value="" />
                    </div>
                    <div class="profile-details">
                        <label>PIN :</label><input name="address_pin" id="address_pin" class="profile-form" type="text" placeholder="Pin" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Location :</label><input name="address_location" id="address_location" class="profile-form" type="text" placeholder="Location" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Address :</label>
                        <textarea name="address_address" id="address_address" class="profile-form" placeholder="Address"></textarea>
                    </div>
                    <div class="profile-details">
                        <label>Town :</label><input name="address_town" id="address_town" class="profile-form" type="text" placeholder="Town" value="" />
                    </div>
                    <div class="profile-details">
                        <label>Country :</label>
                        <select name="country" id="country">
                            <option value="">--Choose Country--</option>
                            @foreach ($countries as $row)
                                @if ($row->name == 'India')
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="profile-details">
                        <label>State :</label>
                        <div id="outer_ajaxstate">
                            <select name="state" id="ajx_state">
                                <option value="">--Choose State--</option>
                            </select>
                        </div>
                    </div>
                    <div class="profile-details">
                        <label>Landmark :</label><input name="address_landmark" id="address_landmark" class="profile-form" type="text" placeholder="Landmark" value="" />
                    </div>
                    <div class="profile-details" id="addressAdd_btn_outer"><button type="button" class="btn btn-success" id="address_add">Add</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
