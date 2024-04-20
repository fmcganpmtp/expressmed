@extends('layouts.admin')

@section('content')

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">News</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.news') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Title:</strong>
                            {{ $news->title }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Description:</strong>
                            {!! $news->description !!}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Created at:</strong>
                            {{ $news->created_at }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            @if ($news->image != '')
                                <img src="{{ asset('assets/uploads/news/') }}/{{ $news->image }}" alt="{{ $news->image }}" width="200px" />
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <h2>Medias</h2>
                        </div>

                        <div class="row">
                            @foreach ($media as $items)
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="news-desc-img">
                                        @if ($items->type == 'image')
                                            <img src="{{ asset('assets/uploads/news/gallery/') }}/{{ $items->url }}" alt="{{ $items->url }}" />
                                        @endif
                                        @if ($items->type == 'youtube')
                                            <iframe width="100%" height="100%" src="{{ 'https://www.youtube.com/embed/'.$items->url }}">
                                            </iframe>
                                        @endif
                                        @if ($items->type == 'video')
                                            <video width="250" controls>
                                                <source src="{{ asset('assets/uploads/news/gallery/') }}/{{ $items->url }}" type="video/mp4">
                                                <source src="movie.ogg" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
