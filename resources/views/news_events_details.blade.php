@extends('layouts.frontview.app')

@section('content')
    <section class="banner-container">
        <div class="inner-banner-content contact-bg">
            <img src="{{ asset('images/career.jpg') }}" class="img-fluid" alt="">
            <h3 class="">{{ $news_details->title }}</h3>
        </div>
    </section>


    <section class="news_dtils_page_out">
        <div class="width-container">
            <div class="news_dtils_page">
                <div class="row">

                    <div class="col-sm-12 col-md-6 left_inner_news_dtils">
                        {{-- <h3>{{ $news_details->title }}</h3> --}}

                        @if ($news_details->image != '')
                            <div class="news_page_img">
                                <img src="{{ asset('/assets/uploads/news/' . $news_details->image) }}" class="img-fluid " alt="" />
                            </div>
                        @else
                            <div class="news_page_img">
                                <img src="{{ asset('/assets/uploads/news/blog-default.png') }}" alt="" />
                            </div>
                        @endif

                    </div>

                    <div class="col-sm-12 col-md-6 right_inner_news_dtils">
                        <div class="news_date">{{ date('F d Y', strtotime($news_details->created_at)) }}</div>
                        <p>{!! $news_details->description !!}</p>
                    </div>
                </div>
            </div>

            <div class="sec_news_dtils_page">
                <div class="row justify-content-center">
                    <div class="inner_news_video_dtils">
                        @if (!empty($news_media))
                            @foreach ($news_media as $media_row)
                                @if ($media_row->type == 'image')
                                    <div class="col-sm-12 col-md-4 inner_news_photo_dtils">
                                        <img src="{{ asset('assets/uploads/news/gallery/') }}/{{ $media_row->url }}" alt="{{ $media_row->url }}" />
                                    </div>
                                @endif
                                @if ($media_row->type == 'youtube')
                                    <div class="col-sm-12 col-md-4 inner_news_photo_dtils">
                                        {!! $media_row->url !!}
                                        {{-- <iframe width="100%" height="500" src="https://www.youtube.com/embed/tgbNymZ7vqY"> </iframe> --}}
                                        {{-- <iframe width="420" height="315" src="https://www.youtube.com/embed/tgbNymZ7vqY" frameborder="0" allowfullscreen></iframe> --}}
                                    </div>
                                @endif
                                @if ($media_row->type == 'video')
                                    <div class="col-sm-12 col-md-4 inner_news_photo_dtils">
                                        {{-- {{ 'SAdsefreswr' }} --}}
                                        <video width="100%" controls>
                                            <source src="{{ asset('assets/uploads/news/gallery/') }}/{{ $media_row->url }}" type="video/mp4">
                                            <source src="movie.ogg" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
