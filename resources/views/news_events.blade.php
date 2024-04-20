@extends('layouts.frontview.app')

@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <section class="banner-container">
        <div class="inner-banner-content contact-bg">

            <nav aria-label="breadcrumb" class="inner-breadcrumb">
                <ol class="breadcrumb ">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">News & Events</li>
                </ol>
            </nav>

        </div>
    </section>
    <div class="news-full">
        <div class="listing-page-head d-flex justify-content-between">
            <h2>NEWS & EVENTS</h2>
        </div>
        <div class="width-container row justify-content-center">

            @foreach ($news as $row)
                <div class="col-12 col-sm-6 col-md-4 news-single">
                    <div class="inner-news-single">
                        <h6>{{ date('F d Y', strtotime($row->created_at)) }}</h6>
                        <a href="{{ url('news-evets-details/' . $row->title) }}">
                            @if ($row->image != '')
                                <div class="news-img-outer">
                                    <img src="{{ asset('/assets/uploads/news/' . $row->image) }}" class="img-fluid " alt="" />
                                </div>
                            @else
                                <div class="news-img-outer">
                                    <img src="{{ asset('/assets/uploads/news/blog-default.png') }}" alt="" />
                                </div>
                            @endif
                        </a>
                        <div class="inner-news-cont">
                            <a href="{{ url('news-evets-details/' . $row->title) }}">
                                <h5>{{ $row->title }}</h5>
                            </a>

                            <p>{!! Str::limit($row->description, 250) !!}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
