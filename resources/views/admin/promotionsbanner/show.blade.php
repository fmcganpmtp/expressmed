@extends('layouts.admin')
@section('content')

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{{ session('success') }}</li>
                </ul>
            </div>
        @endif

        <h1 class="h3 mb-2 text-gray-800">Promotion Banner</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.promotionbanner') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-xs-12 col-sm-12 col-md-8">
                        @if ($promotionbannerdetails->type == 'slider')
                            @if ($BannerImages->isNotEmpty())
                                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        @php $count = 0; @endphp
                                        @foreach ($BannerImages as $images)
                                            <li data-bs-target="#carouselExampleCaptions" data-slide-to="0" @if ($count == 0){{ 'class="active"' }}@endif></li>
                                            @php $count++; @endphp
                                        @endforeach
                                    </ol>
                                    <div class="carousel-inner">
                                        @php $count = 0; @endphp
                                        @foreach ($BannerImages as $images)
                                            <div class="carousel-item @if ($count == 0){{ 'active' }}@endif">
                                                @if ($images->image != '')<img src="{{ asset('/assets/uploads/promotionbanner/' . $images->image) }}" class="img-thumbnail"  />@endif
                                            </div>
                                            @php $count++; @endphp
                                        @endforeach
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden" style="color: #000000">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden" style="color: #000000">Next</span>
                                    </a>
                                </div>
                            @endif
                        @elseif ($promotionbannerdetails->type == 'plain')
                            @if ($BannerImages->isNotEmpty())
                                <img src="{{ asset('/assets/uploads/promotionbanner/' . $BannerImages[0]->image) }}" class="img-thumbnail"  />
                            @endif
                        @endif
                    </div>

                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Title:</strong>
                            {{ $promotionbannerdetails->title }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Section:</strong>
                            {{ $promotionbannerdetails->section }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Skills:</strong>
                            {{ $promotionbannerdetails->position }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Created at:</strong>
                            {{ $promotionbannerdetails->created_at }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $promotionbannerdetails->status }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
