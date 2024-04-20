@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls active">Our Team</li>
            </ol>
        </nav>
    </div>

    <div class="width-container main-product-detail-page content-page-outer">
         
            @if ($doctors->isNotEmpty())
                <section class="why-choose">
                    <div class="why-choose-head">
                        <h2>Our Doctors</h2>
                    </div>
                    <div class="width-container justify-content-center row mt-5">
                        @foreach ($doctors as $row)
                            <div class="col-md-3 doctor-profile-outer">
                                <div class="why-choose-dtl"><div class="doc-img-outer"><img class="w-100" src="{{ asset('/assets/uploads/doctors/' . $row->image) }}" /></div>
                                    <h6><b>{{ $row->name }}</b></h6>
                                    <p>{{ $row->department }}</p>
                                    <p>{{ $row->qualification }}</p>

                                    <p>{{ $row->description }}</p>

                                    <div class="social-media">
                                        @if ($row->instagram != '')
                                            <a href="{{ $row->instagram }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="instagram" width="30"> --}}
                                                <i class="fa fa-instagram" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($row->facebook != '')
                                            <a href="{{ $row->instagram }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="facebook" width="30"> --}}
                                                <i class="fa fa-facebook" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($row->twitter != '')
                                            <a href="{{ $row->twitter }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="twitter" width="30"> --}}
                                                <i class="fa fa-twitter" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($row->linkedin != '')
                                            <a href="{{ $row->linkedin }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="linkedin" width="30"> --}}
                                                <i class="fa fa-linkedin-square" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach


                    </div>
                </section>
            @endif
            @if ($teams->isNotEmpty())
                <section class="why-choose">
                    <div class="why-choose-head">
                        <h2>Our Team</h2>
                    </div>
                    <div class="width-container justify-content-center row mt-5">
                        @foreach ($teams as $team)
                            <div class="col-md-3 doctor-profile-outer">
                                <div class="why-choose-dtl"><div class="doc-img-outer"><img class="w-100" src="{{ asset('/assets/uploads/teams/' . $team->image) }}" /></div>
                                    <h6><b>{{ $team->name }}</b></h6>
                                    <p>{{ $team->position }}</p>
                                    <p>{{ $team->description }}</p>

                                    <div class="social-media">
                                        @if ($team->instagram != '')
                                            <a href="{{ $team->instagram }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="instagram" width="30"> --}}
                                                <i class="fa fa-instagram" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($team->facebook != '')
                                            <a href="{{ $team->instagram }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="facebook" width="30"> --}}
                                                <i class="fa fa-facebook" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($team->twitter != '')
                                            <a href="{{ $team->twitter }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="twitter" width="30"> --}}
                                                <i class="fa fa-twitter" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                        @if ($team->linkedin != '')
                                            <a href="{{ $team->linkedin }}" target="_blank">
                                                {{-- <img src="{{ asset('/assets/uploads/socialmedia/' . $icons->icon) }}" class="img-fluid" alt="linkedin" width="30"> --}}
                                                <i class="fa fa-linkedin-square" aria-hidden="true"></i>

                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

    </div>

@endsection
