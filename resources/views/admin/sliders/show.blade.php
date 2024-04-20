@extends('layouts.admin')

@section('content')

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Sliders</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.sliders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @php $count = 0; @endphp
                                @foreach ($slider_images as $images)
                                    <li data-bs-target="#carouselExampleCaptions" data-slide-to="0" @if ($count == 0) {{ 'class="active"' }} @endif></li>
                                    @php $count++; @endphp
                                @endforeach
                            </ol>
                            <div class="carousel-inner">
                                @php $count = 0; @endphp
                                @foreach ($slider_images as $images)
                                    <div class="carousel-item @if ($count==0) {{ 'active' }} @endif">
                                        @if ($images->image != '')<img src="{{ asset('/assets/uploads/sliders/' . $images->image) }}" class="img-thumbnail" width="98%" height="100px" />@endif
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>{{ $images->title }}</h5>
                                            <p>{{ $images->description }}</p>
                                            @if ($images->target)
                                                <a href="{{ $images->target }}">Readmore</a>
                                            @endif
                                        </div>
                                    </div>
                                    @php $count++; @endphp
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
