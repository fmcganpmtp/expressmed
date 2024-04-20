@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->

    <div class="width-container">
        <nav aria-label="breadcrumb cart-page-outer">
            <ol class="breadcrumb inner-breadcrumb">
                <li class="breadcrumb-item inner-breadcrumb-item"><a href="{{ route('home') }}"><img src="{{ asset('front_view/images/house.png') }}" alt=""></a></li>
                <li class="breadcrumb-item inner-breadcrumb-dtls active">Careers</li>
            </ol>
        </nav>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif

    <div class="width-container main-product-detail-page">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 product-detail-page-outer">
                <div class="listing-page-head d-flex justify-content-between">
                    <h2>We’re Hiring!</h2>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 job-career-outer">
                    <div class="row">
                        @foreach ($careers as $careers_row)

                                <div class="col-lg-3 col-md-3 col-sm-12 job-outer">
                                     <div class="job-inn d-flex flex-column">
                                        <div class="job-title"><a href="{{ route('apply.career.jobs', $careers_row->id) }}"><h6>{{ $careers_row->job_title }}</h6></a></div>
                                        <div class="job-desc">{{Str::limit(ucfirst($careers_row->description), 200, $end = '..')}}</div>
                                        <div class="job-skill"><span>Skills:</span>{{Str::limit(ucfirst($careers_row->skills), 200, $end = '..')}}</div>
                                        <div class="job-vacan"><span>Job Vacancies:</span>{{ $careers_row->no_of_vaccancies }}</div>
                                      <div class="job-apply d-flex mt-auto"> <a href="{{ route('apply.career.jobs', $careers_row->id) }}" class="btn btn-primary text-center apply-career">Apply</a></div>
                                    </div>
                                </div>

                        @endforeach
                    </div>
                </div>
                {{ $careers->links() }}
            </div>
        </div>
    </div>

@endsection
