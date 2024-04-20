@if (count($offerlinksection) > 0)
    <section class="top-flate">
        @foreach ($offerlinksection as $row)
            <div class="width-container row justify-content-center">
                <div class="helvetica-animate-wrapper col-md-6">
                    <h1>{!! $row->offer_content !!}
                        <div class="helvetica-animate-words helvetica-words helvetica-words-2">
                            <span><a href="{!! $row->offer_link !!}">Explore</a></span>
                            <span><a href="{!! $row->offer_link !!}">Click</a></span>
                        </div>
                    </h1>
                </div>
            </div>
        @endforeach

    </section>
@endif

<section class="main-top">
    <div class="width-container container">
        <div class="row justify-content-center align-items-center">
            <div class="col-sm-3 col-lg-2 main-logo">
                @php $key = array_search('company_logo', array_column($common_settings, 'item')) @endphp
                @if ($common_settings[$key]['value'])
                    <a href="{{ route('home') }}"><img src="{{ asset('/assets/uploads/logo/' . $common_settings[$key]['value']) }}" class="img-fluid" alt="" title="company logo" /></a>
                @else
                    <a href="{{ route('home') }}"><img src="{{ asset('front_view/images/logo.png') }}" class="img-fluid"></a>
                @endif
            </div>
            <div class="col-lg-5 col-sm-5 main-search-outer">

                <form action="{{ route('list.allproductlisting') }}" method="GET">
                    <div class="header-search">
                        <input type="text" class="header-search-a flex-grow-1" id="search_keyword" name="search_keyword" value="{{ isset($_GET['search_keyword']) && $_GET['search_keyword'] != '' ? $_GET['search_keyword'] : '' }}" placeholder="Search Products" autocomplete="off" />
                        <input type="hidden" id="userID" name="userID" value="" />
                        <div class="dropdown">
                            <button class="dropdown-toggle country-serach" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ isset($_GET['hid_searchCategoryname']) && $_GET['hid_searchCategoryname'] != ''? Str::limit($_GET['hid_searchCategoryname'], 14, $end = '..'): 'All Categories' }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height: 350px; overflow-y: scroll;">
                                <a class="dropdown-item search_category" href="javascript:void(0)" data_id="0">All Categories</a>
                                @foreach ($AllCategories as $AllCategories_Row)
                                    <a class="dropdown-item search_category" href="javascript:void(0)" data_id="{{ $AllCategories_Row->id }}">{{ $AllCategories_Row->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="hid_searchCategory" id="hid_searchCategory" value="{{ isset($_GET['hid_searchCategory']) && $_GET['hid_searchCategory'] != 0 ? $_GET['hid_searchCategory'] : 0 }}">
                        <input type="hidden" name="hid_searchCategoryname" id="hid_searchCategoryname" value="{{ isset($_GET['hid_searchCategoryname']) && $_GET['hid_searchCategoryname'] != ''? $_GET['hid_searchCategoryname']: '' }}">

                        @if (!empty($_GET['productbrand']))
                            @foreach ($_GET['productbrand'] as $brandId)
                                <input type="hidden" name="productbrand[]" value="{{ $brandId }}">
                            @endforeach
                        @endif

                        @if (!empty($_GET['producttype']))
                            @foreach ($_GET['producttype'] as $producttype)
                                <input type="hidden" name="producttype[]" value="{{ $producttype }}">
                            @endforeach
                        @endif

                        @if (!empty($_GET['medicineuse']))
                            @foreach ($_GET['medicineuse'] as $medicineuse)
                                <input type="hidden" name="medicineuse[]" value="{{ $medicineuse }}">
                            @endforeach
                        @endif

                        <button type="submit" class="btn search-button" name="search_submit" value="search"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-sm-4 col-lg-5 header-account-menu-outer">
                <!-- <div class="top-head-content-links">
               <a class="" href="#">About Us</a>
               <a class="" href="#">Blog</a>
               <a class="" href="#">Help</a>
               <a class="" href="#">Careers</a>
             </div> -->
                @guest('user')
                    <div class="header-account-menu">
                        <ul>
                            {{-- <li><a href="#" class="User_login_drop"><img src="{{ asset('front_view/images/wishlist.png') }}" class="pr-2"> Wishlist</a></li> --}}
                            <li class="log_menu_cart"><a data-toggle="collapse" class="drop" id="cart-btn-id" href="#collapsecart" role="button" aria-expanded="false" aria-controls="collapsecart"><span class="cart-count">{{ count($carts) }}</span><img src="{{ asset('front_view/images/cart.png') }}" class="pr-2">Cart </a></li>
                            <li class="log_menu_log"><a data-toggle="collapse" class="drop" id="log-btn-id" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin" id="btn_login"> Login/Register</a></li>
                            {{-- <li><a class="" href="#">About Us</a></li>
                            <li><a class="" href="#">Blog</a></li>
                            <li><a class="" href="#">Help</a></li> --}}
                            @if (!empty($ContentPages))
                                @foreach ($ContentPages as $ContentPages_Row)
                                    @php $PagePosition = explode(',', $ContentPages_Row->page_position); @endphp
                                    @if (in_array('Top', $PagePosition))
                                        <li><a href="{{ route('view.contentpage', $ContentPages_Row->seo_url) }}">{{ $ContentPages_Row->page }}</a></li>
                                    @endif
                                @endforeach
                            @endif

                            {{-- <li> <a href="{{ route('view.career.jobs') }}">Careers</a></li> --}}
                            <li class="home-deals"> <a href="{{ route('list.offerproducts') }}">Deals</a></li>
                            <li>

                            <li> <a href="{{ route('generalprescription.create') }}"><button class="btn add-cart">Upload Prescription</button></a></li>

                            </li>
                        </ul>

                    </div>
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
                                                <option value="{{ $row->id }}" data-code="{{ $row->phonecode }}" data-tokens="{{ $row->name }}" data-content="<div class='flag-outer'><img src='{{ asset('assets/uploads/countries_flag') . '/' . $row->flag_icon }}'></div><span class='text-dark'>{{ $row->name }}</span>">{{ $row->name }}
                                                </option>
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
                @endguest

                @auth('user')
                    <div class="header-account-menu">
                        <ul>
                            {{-- <li><a href="{{ route('myaccount.wishlist') }}"><img src="{{ asset('front_view/images/wishlist.png') }}" class="pr-2"> Wishlist</a></li> --}}
                            <li><a data-toggle="collapse" href="#collapsecart" role="button" aria-expanded="false" aria-controls="collapsecart"><span class="cart-count">{{ count($carts) }}</span><img src="{{ asset('front_view/images/cart.png') }}" class="pr-2">Cart </a></li>
                            <li class='hi-logout'><a href="{{ route('user.myaccount') }}" role="button">Hi {{ Auth::guard('user')->user()->name }}</a> <span class="seperator-line">|</span> <a href="{{ route('user.logout') }}" role="button">Logout</a></li>
                            @if (!empty($ContentPages))
                                @foreach ($ContentPages as $ContentPages_Row)
                                    @php $PagePosition = explode(',', $ContentPages_Row->page_position); @endphp
                                    @if (in_array('Top', $PagePosition))
                                        <li><a href="{{ route('view.contentpage', $ContentPages_Row->seo_url) }}">{{ $ContentPages_Row->page }}</a></li>
                                    @endif
                                @endforeach
                            @endif
                            <li> <a href="{{ route('track.order') }}">Track Order</a></li>

                            <li class="home-deals"> <a href="{{ route('list.offerproducts') }}">Deals</a></li>
                            <li>

                                <a href="{{ route('generalprescription.create') }}"><button class="btn add-cart">Upload Prescription</button></a>

                            </li>
                        </ul>
                    </div>
                @endauth
                {{-- show cart products --}}
                <div class="header-account-menu-a">
                    <div class="collapse cart_outer_container" id="collapsecart">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!--END-nsv-top-bar-->
