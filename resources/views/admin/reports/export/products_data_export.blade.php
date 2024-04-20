    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered" width="100%" cellspacing="0" id="product_category">
                        <tr>
                            <th>#</th>
                            <th><b>Product</b></th>
                            <th><b>Manufactured By</b></th>
                            <th><b>Type</b></th>
                            <th><b>Category</b></th>
                            <th><b>Quantity</b></th>
                            <th><b>Price</b></th>
                            <th><b>Offer Price</b></th>
                            <th><b>Status</b></th>

                        </tr>
                        @php
                            $count = 0;
                            $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item'));
                        @endphp
                        @forelse($products as $key=>$row)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                   {{-- <td class="list-table-main-image">
                                    @if ($row->offer_price != 0)
                                        {{ number_format((($row->price - $row->offer_price) * 100) / $row->price, 0) . '% Discount' }}<br>
                                    @endif

                                    @if ($row->product_image)
                                        <img src="{{ asset('/assets/uploads/products/'.$row->product_image) }}" class="image-responsive">
                                    @else
                                        <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                    @endif
                                    <br />{{ $row->product_name }}
                                    @if ($row->not_for_sale == '1')
                                        <br /><span style="color: red;">{{ 'Not for sale' }}</span>
                                    @endif
                                </td> --}}
                                {{-- <td>{{ $row->brand }}</td> --}}
                                <td>{{ $row->product_name }}</td>

                                <td>{{ $row->manufacturer }}</td>
                                <td>{{ $row->producttype }}</td>
                                <td>{{ $row->category }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td>Rs {{ number_format($row->price, 2) }}</td>
                                <td>Rs {{ number_format($row->offer_price, 2) }}</td>
                                <td style="width:100px"><span>{{ $row->status }} </span>
                                    @if ($row->flag == '1')
                                        <br><span style="color: red;">{{ 'Sold-Out' }}</span>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-danger">No records found</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>

