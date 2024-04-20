<!-- My order history view -->
<div class="pb-90" id="nav-orderhistory" role="tabpanel" aria-labelledby="nav-orderhistory-tab">
    <div class="row">
        <div class="col-md-12">
            <p class="text-uppercase font-weight-bold cyan mb-0">Order History</p>
        </div>
        {{-- @if (session('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{{ session('success') }}</li>
                </ul>
            </div>
        @endif
        <div class="col-md-12 alert alert-success" id="error_address" style="display:none"></div> --}}

        <div class="col-md-12">
            <div id="accordionExample">
                @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                @php
                    $orderstatus = '';
                    $buttonname = '';
                @endphp
                @if (!empty($orders) && $order_details->isNotEmpty())
                    @foreach ($orders as $key => $value)
                        <div class="card">
                            <div class="card-header" id="heading{{ $key }}">
                                <div data-toggle="collapse" data-target="#collapse{{ $key }}" aria-expanded="true" aria-controls="collapse{{ $key }}">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span class="ordered-value">Order ID: {{ $value['order_id'] }}</span>
                                    <span class="ordered-date"> | Order date: {{ date('d-m-Y h:ia', strtotime($value['order_date'])) }}</span>
                                    {{-- <span class="ordered-amount"> | Total Amount: {!! $common_settings[$currency_icon]['value'] !!} {{ $value['total_amount'] }}</span> --}}
                                    <span class="ordered-grand-tot"> | Grand Total: {!! $common_settings[$currency_icon]['value'] !!}{{ $value['grand_total'] }}</span>
                                    <span class="ordered-grand-tot sts"> | Status: {{ $value['status'] }}</span>
                                </div>
                            </div>

                            <div id="collapse{{ $key }}" class="collapse" aria-labelledby="heading{{ $key }}" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="pull-right-invoice"><button type="button" value="{{ $value['order_id'] }}" class="btn btn-sm print_invoice" title="Print Invoice"><i class="fa fa-print"></i> Print Invoice</button></div>

                                    <div class="inv-outer">
                                        <div class="left-inv-outer">
                                            <div class="row">
                                                <div class="col-md-4 col-4 inv-item"><strong>Item</strong></div>
                                                <div class="col-md-4 col-4 inv-qty"><strong>(Qty x Item Price) + tax</strong></div>
                                                <div class="col-md-4 col-4 inv-total"><strong>Total Amount</strong></div>
                                                {{-- <div class="col-md-4 col-4">
                                            <strong>Tools</strong>
                                        </div> --}}

                                            </div>
                                        </div>
                                        @foreach ($value['order_details'] as $products)
                                            @php
                                                switch ($products->productstatus) {
                                                    case 'ordered':
                                                        $orderstatus = 'cancelproduct';
                                                        $buttonname = 'Cancel Order';
                                                        break;
                                                    case 'cancelled':
                                                        $orderstatus = 'Cancelled';
                                                        $buttonname = 'Cancelled';
                                                        break;
                                                    case 'delivered':
                                                        $orderstatus = 'returnproduct';
                                                        $buttonname = 'Return Order';
                                                        break;
                                                }
                                            @endphp

                                            <div class="right-inv-outer">
                                                <div class="row">
                                                    <div class="col-md-4 col-4 inv-sub-cont">
                                                        <div class="inv-product">
                                                            @if ($products->product_image)
                                                                <a href="{{ route('shopping.productdetail', $products->product_url) }}" target="_blank"><img src="{{ asset('assets/uploads/products/' . $products->product_image) }}" width="50px"></a>
                                                            @else
                                                                <img src="{{ asset('img/no-image.jpg') }}" width="50px">
                                                            @endif
                                                            <div class="invoice-item-head">
                                                                <a href="{{ route('shopping.productdetail', $products->product_url) }}" target="_blank">{{ $products->product_name }}</a>
                                                            </div>
                                                        </div>

                                                        @if (!empty($products->variant_type))
                                                            <p class="mb-1"> <strong>Styles:</strong> {{ $products->variant_type }}</p>
                                                        @endif

                                                        @if (!empty($products->variant_unit))
                                                            <p> <strong>Size:</strong> {{ $products->variant_unit }}</p>
                                                        @endif
                                                        @if($products->productstatus=="cancelled")
                                                        <div class="inv-status">Status: {{ $products->productstatus }}</div>
                                                        <div class="inv-date">on {{ date('d-M-Y', strtotime($products->status_on)) }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 col-4 inv-sub-dtls">({!! $products->quantity . ' x ' . $common_settings[$currency_icon]['value'] . number_format($products->price, 2) !!}) + {!! $common_settings[$currency_icon]['value'] . number_format($products->total_tax, 2) !!}</div>
                                                    <div class="col-md-4 col-4 inv-sub-comm"><span>Total: </span> {!! $common_settings[$currency_icon]['value'] . number_format($products->amount, 2) !!}</div>
                                                    <div class="col-md-12 rating_product">
                                                        @if ($products->productstatus == 'delivered' && !in_array($products->product_id, $reviewproducts) && $products->product_typename != 'All Medicines')
                                                            <div class="rating">
                                                                {{-- <span class="glyphicon glyphicon-star star_rateproduct" data-star="1"><i class="fas fa-star star-yellow"></i></span> --}}
                                                                <span class="glyphicon glyphicon-star star_rateproduct" data-star="1"><i class="fas fa-star"></i></span>
                                                                <span class="glyphicon glyphicon-star star_rateproduct" data-star="2"><i class="fas fa-star"></i></span>
                                                                <span class="glyphicon glyphicon-star star_rateproduct" data-star="3"><i class="fas fa-star"></i></span>
                                                                <span class="glyphicon glyphicon-star star_rateproduct" data-star="4"><i class="fas fa-star"></i></span>
                                                                <span class="glyphicon glyphicon-star star_rateproduct" data-star="5"><i class="fas fa-star"></i></span>
                                                            </div>
                                                            <input type="hidden" class="hid_productrate" value="0">
                                                            <textarea class="form-control shadow-sm product_review" rows="2" style="resize: none;"></textarea>

                                                            <button type="button" class="btn btn-info btn-sm rateproduct" data-id="{{ $products->product_id }}">Add Review</button>
                                                        @elseif($products->productstatus == 'delivered' && in_array($products->product_id, $reviewproducts) && $products->product_typename != 'All Medicines')
                                                            @if ($products->rating != '' || $products->reviews != '')
                                                                @php
                                                                    $rating = $products->rating;
                                                                    $width = 20;
                                                                    for ($i = 1; $i < $rating; $i++) {
                                                                        $width = $width + 20;
                                                                    }
                                                                @endphp
                                                                <div class="item-rating">
                                                                    <div class="star-ratings-sprite">
                                                                        <span style="width:{{ $width }}%" class="star-ratings-sprite-rating"></span>

                                                                    </div>
                                                                    <p>{{ $products->reviews }}</p>
                                                                </div>

                                                                <button type="button" class="btn btn-info btn-sm review_delete"  data-id="{{ $products->product_id }}"><i class="fa fa-trash"></i></button>
                                                            @endif
                                                        @endif

                                                        @if ($products->productstatus == 'ordered')
                                                            <button type="button" class="btn btn-info btn-sm" onclick="changestatus({{ $value['order_id'] . ',' . $products->product_id }},'{{ $orderstatus }}')" {{ $orderstatus == 'Cancelled' ? 'disabled' : '' }}>{{ $buttonname }}</button>
                                                        @endif

                                                        @if ($products->productstatus == 'delivered' && strtotime($products->status_on) > strtotime('-7 day'))
                                                            {{-- <button type="button" class="btn btn-danger btn-sm" onclick="changestatus({{ $value['order_id'].','.$products->product_id }},'{{$orderstatus}}')">{{ $buttonname }}</button>
                                                    <div><small>*Return within 7 days from delivered date.</small></div> --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                    {{ $order_details->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                @else
                    <h4>No Orders found in your account.</h4>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $(document).on('click', '.review_delete', function() {
            if (confirm('Are you sure do you want to delete this product review?')) {
                var product_id = $(this).closest('button').attr('data-id');
                $.ajax({
                    type: "post",
                    data: {
                        product_id: product_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    url: "{{ route('customer.review.delete') }}",
                    success: function(response) {
                        if (response.ajax_status == "success") {
                            $('#alert-success').html('Review And Rating Deleted Successfully.').show();
                            setTimeout(location.reload.bind(location), 1500);

                        }
                    }

                })
            }

        });

    });
</script>
