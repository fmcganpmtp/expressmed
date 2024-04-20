@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls"><a href="{{ route('view.career.jobs') }}">Careers</a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls active">{{ isset($page_title) ? $page_title : 'Apply Job' }}</li>
            </ol>
        </nav>
    </div>

    <div class="width-container main-careapply-page">
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12 career-page-outer">
              <!-- <div class="row justify-content-center align-items-center"> -->
                <div class="listing-page-head d-flex justify-content-between">
                    <h2>Apply Now</h2>
                </div>

                  <div class="col-lg-12 col-md-12 col-sm-12 career-page-dtls">
                    <h4>Job Details : </h4>
                    <div class="job-desc-page">
                        <p><span>Job Title :</span> {{ $job_details->job_title }}</p>
                        <p><span>Job Description :</span> {{ $job_details->description }}</p>
                        <p><span>Skills :</span> {{ $job_details->skills }}</p>
                        <p><span>Vacancies :</span> {{ $job_details->no_of_vaccancies }}</p>
                    </div>

                    <!-- <div class="col-lg-12 col-md-12 col-sm-12"> -->
                        <div class="apply_careerjob">
                            <form method="post" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="careerjob-details">
                                    <label>Name	:</label><input type="text" name="applicant_name" class="profile-form" placeholder="Name" value="{{ old('applicant_name') }}" />
                                </div>
                                @if($errors->has('applicant_name'))
                                    <span class="text-danger">{{ $errors->first('applicant_name') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>Phone :</label><input type="text" name="phone" class="profile-form" placeholder="Phone" value="{{ old('phone') }}" />
                                </div>
                                @if($errors->has('phone'))
                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>E-mail :</label><input type="email" name="applicant_email" class="profile-form" placeholder="E-mail" value="{{ old('applicant_email') }}" />
                                </div>
                                @if($errors->has('applicant_email'))
                                    <span class="text-danger">{{ $errors->first('applicant_email') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>DOB :</label><input type="date" name="birthdate" class="profile-form"  value="{{ old('birthdate') }}"/>
                                </div>
                                @if($errors->has('birthdate'))
                                    <span class="text-danger">{{ $errors->first('birthdate') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>Address :</label><textarea name="address" class="profile-form" placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                                @if($errors->has('address'))
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>PIN :</label><input name="pin" class="profile-form" type="text" placeholder="Pin Code" value="{{ old('pin') }}"/>
                                </div>
                                @if($errors->has('pin'))
                                    <span class="text-danger">{{ $errors->first('pin') }}</span>
                                @endif
                                <div class="careerjob-details">
                                    <label>Resume :</label><input type="file" name="resume_file" class="profile-form"/>
                                </div>
                                <div class="job-pdf">(.pdf, .doc, .docx, .dot)</div>
                                @if($errors->has('resume_file'))
                                    <span class="text-danger">{{ $errors->first('resume_file') }}</span>
                                @endif
                                <div class="careerjob-details-btn" id="addressEdit_btn_outer">
                                    <button type="submit" class="btn btn-success" value="save">Apply</button>
                                    <a href="{{ route('view.career.jobs')}}" class="btn">Back</a>
                                </div>
                            </form>
                        </div>
                    <!-- </div> -->

                </div>
            <!-- </div> -->
            </div>

        </div>
    </div>

@endsection
