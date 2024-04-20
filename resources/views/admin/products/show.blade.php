@extends('layouts.admin')

@section('content')

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Products</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg p-3" href="{{ route('admin.products') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <div class="float-right">
                    <form action="{{ url('/products/delete/' . $product->id) }}" method="POST">
                    <a href="{{ url('/admin/orders?productid=') }}{{ $product->id }}" class="btn btn-primary"><i class="fas fa-list"></i> View order</a>

                    @if ($product->status != 'deleted')
                        <a href="{{ url('/products/edit/' . $product->id) }}" title="edit" class="btn btn-primary"><i class="fas fa-pen"></i> Edit</a>
                    @endif
                    @if ($product->hide_from_site == '0')
                        <a href="javascript:void(0)" title="hide" data-id="{{ $product->id }}" class=" btn-md hide_option btn btn-primary" data-item="hide"><i class="fas fa-eye-slash"></i> Hide</a>
                    @elseif($product->hide_from_site == '1')
                        <a href="javascript:void(0)" title="show" data-id="{{ $product->id }}" class="  btn-md hide_option btn btn-primary" data-item="show"><i class="fas fa-camera"></i> Show</a>
                    @endif
                    @if ($product->flag == '0')
                        <a href="javascript:void(0)" title="sold out" data-id="{{ $product->id }}" class="btn btn-md sell_status btn-primary" data-item="sold-out">Sold Out</a>
                    @elseif ($product->flag == '1')
                        <a href="javascript:void(0)" title="sell" data-id="{{ $product->id }}" class="btn btn-md sell_status btn-primary" data-item="sell">Sell</a>
                    @endif
                    @if ($product->status == 'review')
                        <a href="javascript:void(0)" title="Approve" data-id="{{ $product->id }}" class="btn btn-success  btn-md approve_product"><i class="fas fa-check"></i>Approve</a>
                    @endif

                    @csrf
                    <button type="submit" class="btn btn-danger btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                    <br>
                </div>

                <div class="row">

                    <div class="container">

                        <div class="wrapper row">

                            <div class="preview col-md-6">
                                <div class="preview-pic tab-content">
                                    @php $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item')); @endphp
                                    @if ($product->product_image != '')
                                        <img src="{{ asset('/assets/uploads/products/') }}/{{ $product->product_image }}" alt="{{ $product->product_image }}" width="80px" height="500px" class="tab-pane active image_display" id="product_pic" />
                                    @endif

                                </div>
                                <ul class="preview-thumbnail nav nav-tabs thumbnail">
                                    @foreach ($product_images as $all_product_images)
                                        <li class="active"><a data-target="#pic-1" data-toggle="tab" class="thumbnail">
                                                <img src="{{ asset('/assets/uploads/products/') }}/{{ $all_product_images->product_image }}" alt="{{ $all_product_images->product_image }}" width="50px" class="tab-pane thumbnail  image_display" id="product_pic" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="details col-md-6">


                                <h3 class="product-title">{{ $product->product_name }}</h3>

                                <div class="rating">
                                    <div class="stars"> </div>
                                    <p><span>{{ $product->brand_name }}</span></p>
                                </div>

                                <div class="rating">
                                    <div class="stars"> </div>
                                    <p><span>Medicine Use:</span> {{ $product->medicine_use_name != '' ? $product->medicine_use_name : 'N/a' }}</p>
                                </div>

                                @if ($productsuppliers)
                                    <p><span>Supplier:</span>
                                        @foreach ($productsuppliers as $key => $productsuppliers_Row)
                                            {{ $productsuppliers_Row->name . (!$loop->last ? ',' : '') }}
                                        @endforeach
                                    </p>
                                @endif

                                @if ($product->manufacturer != '')
                                    <p><span>Manufactured by:</span> {{ $product->manufacturer }}</p>
                                @endif

                                <p><span>Quantity:</span> {{ $product->quantity }}</p>

                                <p><span>Product Contents:</span>
                                    @if ($Productcontents)
                                        @foreach ($Productcontents as $key => $Productcontents_Row)
                                            {{ $Productcontents_Row->name . (!$loop->last ? ',' : '') }}
                                        @endforeach
                                    @else
                                        Nill
                                    @endif
                                </p>

                                @if ($product->offer_price != 0)
                                    <p><span>Discounts:</span> {{ number_format((($product->price - $product->offer_price) * 100) / $product->price, 0) }}%</p>
                                @endif
                                <h6 class="price"><span>Price:</span> {!! $common_settings[$currency_key]['value'] . $product->price !!}</h6>

                                <h6 class="price"><span>Offer price:</span> {!! $common_settings[$currency_key]['value'] . $product->offer_price !!}</h6>

                                @if ($product->product_pack != '')
                                    <p><span>Pack:</span>{{ $product->product_pack }}</p>
                                @endif

                                <p><span>Category:</span> {{ $product->category }}</p>

                                @if ($Taxes->isNotEmpty())
                                    <p class="price"><span>Tax:</span>
                                        @foreach ($Taxes as $key => $Taxes_Row)
                                            <span>{{ $Taxes_Row->tax_name }} ({{ $Taxes_Row->percentage }}%){{ $key != count($Taxes) - 1 ? ',' : '' }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                @if ($product->storage != '')
                                    <p><span>Storage :</span> {{ $product->storage }}&#8451;</p>
                                @endif

                                <p><span>Doctor's Prescription:</span> {{ $product->prescription == 1 ? 'Yes' : 'No' }}</p>

                                @if ($product->variant_products != '')
                                    <p><span>Product Variants:</span></p>
                                    @foreach ($variant_products as $variant)
                                        <p>{{ $variant->product_name }}</p>
                                    @endforeach
                                @endif

                                <p class="product-description"><span>Product Description:</span> {!! $product->description !!}</p>

                                @if ($product->how_to_use != '')
                                    <p><span>How to use:</span> {!! $product->how_to_use !!}</p>
                                @endif

                                @if ($product->benefits != '')
                                    <p><span>Benefits:</span> {!! $product->benefits !!}</p>
                                @endif

                                @if ($product->side_effects != '')
                                    <p><span>Side Effects:</span> {!! $product->side_effects !!}</p>
                                @endif

                                @if ($product->tagline != '')
                                    <p><span>Tag Line:</span> {{ $product->tagline }}</p>
                                @endif

                                @if ($product->features != '')
                                    <p><span>Features:</span> {{ $product->features }}</p>
                                @endif
                                <p><span>Current Status:</span> {{ $product->status }}</p>
                                @if ($product->status == 'active' && $product->approved_by != '')
                                    <p><span>Approved by:</span> {{ $product->approved_by }}</p>
                                @endif

                            </div>
                        </div>

                        <div class="wrapper row">
                            <div class="col-md-12" id="rating_product" style="display: none">
                                <div class="rating" id="div_ratingstar">
                                    <span class="glyphicon glyphicon-star star_rateproduct" data-star="1"><i class="fas fa-star"></i></span>
                                    <span class="glyphicon glyphicon-star star_rateproduct" data-star="2"><i class="fas fa-star"></i></span>
                                    <span class="glyphicon glyphicon-star star_rateproduct" data-star="3"><i class="fas fa-star"></i></span>
                                    <span class="glyphicon glyphicon-star star_rateproduct" data-star="4"><i class="fas fa-star"></i></span>
                                    <span class="glyphicon glyphicon-star star_rateproduct" data-star="5"><i class="fas fa-star"></i></span>
                                </div>
                                <input type="hidden" id="hid_userid" value="0">
                                <input type="hidden" id="hid_productreviews_id" value="0">
                                <input type="hidden" id="hid_productrating" value="0">
                                <textarea class="form-control shadow-sm" rows="3" id="product_review"></textarea>
                                <button type="button" class="btn btn-success btn-sm" id="update_review" value="0">Update Review</button>
                                <button type="button" class="btn btn-info btn-sm" id="cancel_review">Cancel</button>
                            </div>

                            <!--review list section-->
                            @if (!$reviews->isEmpty())
                                @foreach ($reviews as $review_content)
                                    @php
                                        $btn_class = 'btn-default';
                                        if ($review_content->rating == 5) {
                                            $btn_class = 'btn-success';
                                        } elseif ($review_content->rating == 4) {
                                            $btn_class = 'btn-primary';
                                        } elseif ($review_content->rating == 3) {
                                            $btn_class = 'btn-info';
                                        } elseif ($review_content->rating == 2) {
                                            $btn_class = 'btn-warning';
                                        } elseif ($review_content->rating == 1) {
                                            $btn_class = 'btn-danger';
                                        }
                                    @endphp

                                    <div class="col-md-12 review_content">
                                        <div class="comment-head" style="background-color: #ddd">
                                            <span class="btn {{ $btn_class }}">
                                                {{ $review_content->rating }}
                                                <small class="f-9"><i class="fas fa-star"></i></small>
                                            </span>
                                            <span class="font-weight-bold">
                                                @if ($review_content->rating == '5')
                                                    Highly recommended
                                                @elseif($review_content->rating == '4')
                                                    Recommended
                                                @elseif($review_content->rating == '3')
                                                    Average
                                                @elseif($review_content->rating == '2')
                                                    Not Bad
                                                @else
                                                    Poor
                                                @endif
                                            </span>

                                            <button type="button" class="btn btn-md float-right edit_productreview" userid="{{ $review_content->user_id }}" rating="{{ $review_content->rating }}" value="{{ $review_content->id }}"><i class="fas fa-pen"></i></button>
                                            <button type="button" class="btn btn-md float-right delete_productreview" value="{{ $review_content->id }}" userid="{{ $review_content->user_id }}"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <div class="comment-text px-4 py-2 review_content">{{ $review_content->reviews }}</div>
                                        <div class="comment-footer px-4 pb-3">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <span class="comment-name">{{ $review_content->user->name }}</span>
                                                    <span class="verified-user"><i class="fas fa-check-circle"></i> Verified Customer</span>
                                                    <span class="comment-time">
                                                        Published on: {{ date('Y-m-d', strtotime($review_content->created_at)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{ $reviews->links() }}
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(function() {
            $('.thumbnail').click(function() {
                $("#product_pic").attr('src', this.src);
            });
        });

        //-----star rating for products--
        $(document).on('click', '.star_rateproduct', function() {
            var starvalue = $(this).attr('data-star');

            $(this).addClass('star-yellow').prevAll().addClass('star-yellow');
            $(this).nextAll().removeClass('star-yellow');

            $('#hid_productrating').val(starvalue);
        });

        //-----Load product review--
        $(document).on('click', '.edit_productreview', function() {
            $('#rating_product').show(500);

            var product_reviews_id = $(this).val();
            var userid = $(this).attr('userid');
            var star_rating = $(this).attr('rating');
            var review_content = $(this).closest('.review_content').find('.review_content').text();

            $('#hid_userid').val(userid);
            $('#hid_productreviews_id').val(product_reviews_id);
            $('#hid_productrating').val(star_rating);
            $('#product_review').text(review_content);

            var html = yellowstar5 = yellowstar4 = yellowstar3 = yellowstar2 = yellowstar1 = '';

            if (star_rating == 5) {
                yellowstar5 = 'star-yellow';
                yellowstar4 = 'star-yellow';
                yellowstar3 = 'star-yellow';
                yellowstar2 = 'star-yellow';
                yellowstar1 = 'star-yellow';
            } else if (star_rating == 4) {
                yellowstar4 = 'star-yellow';
                yellowstar3 = 'star-yellow';
                yellowstar2 = 'star-yellow';
                yellowstar1 = 'star-yellow';
            } else if (star_rating == 3) {
                yellowstar3 = 'star-yellow';
                yellowstar2 = 'star-yellow';
                yellowstar1 = 'star-yellow';
            } else if (star_rating == 2) {
                yellowstar2 = 'star-yellow';
                yellowstar1 = 'star-yellow';
            } else if (star_rating == 1) {
                yellowstar1 = 'star-yellow';
            }

            html += '<span class="glyphicon glyphicon-star star_rateproduct ' + yellowstar1 + '" data-star="1"><i class="fas fa-star"></i></span>';
            html += '<span class="glyphicon glyphicon-star star_rateproduct ' + yellowstar2 + '" data-star="2"><i class="fas fa-star"></i></span>';
            html += '<span class="glyphicon glyphicon-star star_rateproduct ' + yellowstar3 + '" data-star="3"><i class="fas fa-star"></i></span>';
            html += '<span class="glyphicon glyphicon-star star_rateproduct ' + yellowstar4 + '" data-star="4"><i class="fas fa-star"></i></span>';
            html += '<span class="glyphicon glyphicon-star star_rateproduct ' + yellowstar5 + '" data-star="5"><i class="fas fa-star"></i></span>';

            $('#div_ratingstar').html(html);
        });

        //-----Update product review--
        $(document).on('click', '#update_review', function() {
            var userid = $('#hid_userid').val();
            var productid = '{{ $product->id }}';
            var product_reviews_id = $('#hid_productreviews_id').val();
            var starvalue = $('#hid_productrating').val();
            var productreview = $('#product_review').val();

            if (productid != '' && product_reviews_id != '') {
                if (starvalue != 0) {
                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            userid: userid,
                            productid: productid,
                            product_reviews_id: product_reviews_id,
                            starvalue: starvalue,
                            productreview: productreview
                        },
                        url: "{{ route('product.review.update') }}",
                        success: function(data) {
                            if (data.ajax_status == 'success') {
                                window.location.reload(true);
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                } else {
                    alert('Please rate star value for product.');
                }

            } else {
                alert('Invalid product or review choose.');
            }
        });

        //-----Delete product review--
        $(document).on('click', '.delete_productreview', function() {
            if (confirm('Do you really want delete this review?')) {
                var elm = $(this);
                var product_reviews_id = $(this).val();
                var userid = $(this).attr('userid');
                var productid = '{{ $product->id }}';

                if (productid != '' && product_reviews_id != '') {
                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            product_reviews_id: product_reviews_id,
                            userid: userid,
                            productid: productid,
                        },
                        url: "{{ route('product.review.delete') }}",
                        success: function(data) {
                            if (data.ajax_status == 'success') {
                                elm.closest('.review_content').remove();
                            } else {
                                alert(data.message);
                            }
                        }
                    });

                } else {
                    alert('Invalid product or review choose.');
                }
            }

        });

        //-----cancel product review--
        $('#cancel_review').on('click', function() {
            $('#rating_product').hide(500);
            $('#hid_userid').val(0);
            $('#hid_productreviews_id').val(0);
            $('#hid_productrating').val(0);
            $('#product_review').text('');
        });
        $('.sell_status').on('click',function(){
            if(confirm('Are you sure ?')){
            var status=$(this).attr('data-item');
            var product_id=$(this).attr('data-id');
            if(status){
                $.ajax({
                    url:"{{route('product.update.sellstatus')}}",
                    type:"POST",
                    data:{
                        status: status,
                        product_id:product_id,
                        "_token":"{{csrf_token()}}"
                    },
                    dataType:"json",
                   success:function(data){
                       if(data.ajax_status=="success"){
                        alert(data.message)
                       location.reload();
                       }

                   }
                })
            }
            }
        });

        $('.approve_product').on('click',function(){
            if(confirm('Are you sure ?')){
            var product_id=$(this).attr('data-id');
                $.ajax({
                    url:"{{route('approve.product')}}",
                    type:"POST",
                    data:{
                        product_id:product_id,
                        "_token":"{{csrf_token()}}"
                    },
                    dataType:"json",
                   success:function(data){
                       if(data.ajax_status=="success"){
                        alert(data.message)

                       location.reload();
                       }

                   }
                })
            }
        });

        $('.hide_option').on('click',function(){
            if(confirm('Are you sure ?')){
            var status=$(this).attr('data-item');
            var product_id=$(this).attr('data-id');
            if(status){
                $.ajax({
                    url:"{{route('product.update.hideoption')}}",
                    type:"POST",
                    data:{
                        status: status,
                        product_id:product_id,
                        "_token":"{{csrf_token()}}"
                    },
                    dataType:"json",
                   success:function(data){
                       if(data.ajax_status=="success"){
                       location.reload();
                       }

                   }
                })
            }
            }
        });
    </script>
@endsection
