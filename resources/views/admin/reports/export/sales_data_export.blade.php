
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800"></h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <button type="submit" class="btn btn-primary" id="btnExport">Export</button>
                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product Name</th>
                                <th>Total Amount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                            @forelse($salesList as $key=>$value)
                                <tr>
                                    {{-- <td>{{ $key + $ordersList->firstItem() }}</td> --}}
                                    <td>{{ $value->id }}</td>
                                    <td>
                                        <ul>
                                        @if(count($value->get_paroductsname($value->id))>0)
                                        @foreach($value->get_paroductsname($value->id) as $productname_row)
                                        <li> {{$productname_row->product_name }}</li>
                                        @endforeach
                                        @endif
                                        </ul>
                                    </td>
                                    <td>Rs {{ $value->total_amount }}</td>
                                    <td>Rs {{ $value->grand_total }}</td>
                                    <td>{{ $value->status == 'ordered' ? 'Active' : ucwords($value->status) }}</td>
                                    <td>{{ date('d-m-Y h:i a', strtotime($value->date)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-danger">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>





