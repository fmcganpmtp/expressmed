<div class="main-nav-outer" id="main-nav-outer">
    <nav class="navbar navbar-light navbar-expand-lg main-nav" id="myNavbar">

        <!-- next is your mobile burger menu toggle -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            {{-- <span class="navbar-toggler-icon"></span> --}}
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </button>
        <div class="drop-cart-menu">
            <div class="inner-drop-cart-menu">
                <li class="logi-user_cart"><a href="{{route('product.cart')}}"  role="button" aria-expanded="false" aria-controls="collapsecart"><span class="cart-count">{{ count($carts) }}</span><i class="fa fa-shopping-cart text-white" aria-hidden="true"></i></a></li>
                <li class="logi-user_nav"><a data-toggle="collapse" class="drop login-user-drop" id="log-btn-id" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin" id="btn_login"> <i class="fa fa-user" aria-hidden="true"></i> </a></li>

                <div class="header-account-menu-a">
                    <div class="collapse login-dropdown" id="collapseLogin">
                        <div class="card card-body">
                            {{-- <div class="social-login">
                                Login with : <a href="#"><i class="fab fa-facebook fb-login"></i></a><a href="#"><i class="fab fa-google google-login"></i></a>
                            </div>
                            <div class="seprate-login">
                                <span>or</span>
                            </div> --}}


                            {{-- <div class="buttons">
                                <a href="#" onclick="toggleVisibility('Menu1');">Menu1</a>
                                <a href="#" onclick="toggleVisibility('Menu2');">Menu2</a>
                                <a href="#" onclick="toggleVisibility('Menu3');">Menu3</a>
                                <a href="#" onclick="toggleVisibility('Menu4');">Menu4</a>
                              </div> --}}
                            {{-- <div id="Menu1">I'm container one</div>
                                            <div id="Menu2" style="display: none;">I'm container two</div>
                                            <div id="Menu3" style="display: none;">I'm container three</div>
                                            <div id="Menu4" style="display: none;">I'm container four</div> --}}

                            <div class="login-one" id="Menu1">
                                <div class="login-fields">
                                    <input name="username" id="username" type="text" placeholder="Email address" />
                                </div>
                                <div class="login-fields login-eye-icon">
                                    <input name="password" id="password" type="password" placeholder="Password" />
                                    <i onclick="loginpasswordShow()" class="fa fa-eye" id="login-pass"></i>
                                </div>
                                <div class="login-alerts text-danger"><small id="login_alerts"></small></div>
                                <div class="login-fields" id="login_button_outer">
                                    <button name="login" class="login-button" id="login_button">Login</button>
                                </div>
                                <div class="create-account text-left">
                                    <a href="#" onclick="toggleVisibility('Menu2');" class="javascript:void(0)">Login With OTP<br></a>
                                </div>
                            </div>

                            <div class="login-two" id="Menu2" style="display: none;">
                                <div class="input-group register-form login-contrys">
                                    <select class="form-control selectpicker" id="country_top" name="country" data-live-search="true" required>
                                        <option value="">Choose Your Country</option>
                                        @foreach ($countries as $row)
											@if( $row->name == 'India')
                                            <option value="{{ $row->id }}" data-code="{{ $row->phonecode }}" data-tokens="{{ $row->name }}" data-content="<div class='flag-outer'><img src='{{ asset('assets/uploads/countries_flag') . '/' . $row->flag_icon }}'></div><span class='text-dark'>{{ $row->name }}</span>">{{ $row->name }}
                                            </option>
											@endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="top-contry-log">
                                    <div class="login-fields flag-select">
                                        <input type="text" id="country_code_top" class="form-control" autocomplete="off" placeholder="Code" disabled />
                                    </div>
                                    <div class="login-fields login-field-no">
                                        <input name="mobile" id="mobile" type="text" placeholder="Enter the mobile number" />
                                    </div>
                                </div>
                                <div class="login-alerts text-danger"><small id="otp_login_alerts"></small></div>

                                <div class="login-fields" id="login_button_outer">
                                    <button name="login" class="login-button otp-request-button" id="otp_request_button">Send OTP</button>
                                </div>
                                <div class="create-account text-left">
                                    <a href="#" onclick="toggleVisibility('Menu1');">Login With Username & Password </a>
                                </div>
                            </div>


                            <div class="forgot-password">
                                <a href="{{ route('user.reset.password') }}">Forgot Your password?</a>
                            </div>
                            <div class="create-account">
                                {{-- <a href="#" onclick="toggleVisibility('Menu1');">Login With OTP </a><br>
                                <a href="#" onclick="toggleVisibility('Menu2');">Login <br></a> --}}
                                <a href="{{ route('register.view') }}">Create Account</a>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- next is where your logo goes -->

        <!-- now we go to the main bit of the nav -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav nav-fill">
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="{{ route('list.all-medicines') }}"  id="Dropdown"  aria-haspopup="true" aria-expanded="false">All Medicines <span class="sr-only">(current)</span></a>

                    <div class="dropdown-menu dropdown-menu-right" style='right:auto;' aria-labelledby="Dropdown">
                    <div class="d-md-flex align-items-start justify-content-start">
                    <div class="dropdown-header">
                        <a href="{{ route('list.medicine.categories') }}">All medicines by categories</a>
                    </div></div>

                    </div>
                </li>

                @foreach ($limitCategories as $limitCategories_Row)
                    @if (count($limitCategories_Row->subcategory) > 0)

                        <li class="nav-item px-4">
                            @if (trim($limitCategories_Row->name) != 'All Medicines')
                                <a class="nav-link dropdown-toggle" href="#" id="{{ $limitCategories_Row->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $limitCategories_Row->name }}</a>

                                <!-- your mega menu starts here! -->
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="{{ $limitCategories_Row->id }}Dropdown">

                                    <!-- the standard dropdown items -->

                                    <!-- next, a divider to tidy things up -->
                                    <div class="dropdown-divider"></div>

                                    <!-- finally, using flex to create your layout -->
                                    <div class="d-md-flex align-items-start justify-content-start">
                                        @if (trim($limitCategories_Row->name) == 'All Medicines')

                                        @else
                                            <div class="med_link_cont">
                                                <div class="med_link_dtls">
                                                    @if (trim($limitCategories_Row->name) == 'Medical Devices & Surgical')
                                                    @php $counteri=1; @endphp
                                                    @else
                                                     @php $counteri=0; @endphp
                                                     @endif
                                            @foreach ($limitCategories_Row->subcategory as $subCategories_Row)
                                                @if($counteri%2==0 && count($subCategories_Row->subcategory)>0)
                                                 </div><div class="med_link_dtls">
                                                @endif
                                                @if(count($subCategories_Row->subcategory)>0)
                                                <div class="dropdown-header">
                                                    <h6><a href="{{ url('productlisting/category/' . $subCategories_Row->name) }}"> {{ $subCategories_Row->name }}</a></h6>
                                                </div>
                                                @else
                                                <div class="dropdown-header">
                                                     <a href="{{ url('productlisting/category/' . $subCategories_Row->name) }}"> {{ $subCategories_Row->name }}</a>
                                                </div>
                                                @endif
                                                @foreach ($subCategories_Row->subcategory as $subCategories_Row2)
                                                    <div class="dropdown-header">
                                                        <a class="dropdown-item" href="{{ url('productlisting/category/' . $subCategories_Row2->name) }}"> {{ $subCategories_Row2->name }}</a>
                                                    </div>
                                                    @foreach ($subCategories_Row2->subcategory as $subCategories_Row3)
                                                        <div class="dropdown-header"><a class="dropdown-item" href="{{ url('productlisting/category/' . $subCategories_Row3->name) }}">{{ $subCategories_Row3->name }}</a></div>
                                                    @endforeach
                                                @endforeach
                                                @php $counteri++; @endphp
                                            @endforeach
                                            </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endif
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('shopping.productlisting', $limitCategories_Row->name) }}"> {{ $limitCategories_Row->name }}  </a>
                        </li>
                    @endif
                @endforeach

            </ul>
        </div>
    </nav>
</div>
