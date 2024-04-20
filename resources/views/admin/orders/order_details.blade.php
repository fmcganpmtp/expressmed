@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Order Details</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                @if (session('errors'))
                    <div class="alert alert-danger">
                        <ul>
                            <li>{{ session('errors') }}</li>
                        </ul>
                    </div>
                @endif
                <!-- Modal -->
                <div class="modal fade" id="ApproveModal" tabindex="-1" role="dialog" aria-labelledby="ApproveModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ApproveModalLabel">Approve Prescription</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>The customer can buy only the allowed quantity.</p>
                                <input type="hidden" name="prescription_id" value="0">
                                <div class="col-md-12">
                                    <label>Allowed Quantity</label>
                                    <input type="number" id="allowed_qty" value="1" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div id="ajax_loader" style="display:none;"><img src="{{ asset('img/ajax-loader.gif') }}"></div>
                                <button type="submit" class="btn btn-primary" id="approve_purchase">Approve</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <a class="btn btn-primary btn-circle btn-lg" href="{{ url()->previous() == url()->current() ? route('admin.orders') : url()->previous() }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a> --}}
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.orders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

                @if (!empty($orderArray))
                    @if ($orderArray['order'])
                        <div class="col-sm-12">
                            <button type="button" id="print_invoice" value="{{ $orderArray['order']->id }}" class="btn btn-sm" title="Print Invoice"><i class="fa fa-print"></i> Invoice</button>
                            {{-- <address>
                                <strong style="float:right">Order ID :{{ $orderArray['order']->id }}</strong><br>
                                <strong style="float:right">Date : {{ date('d-m-Y h:ia', strtotime($orderArray['order']->date)) }}</strong><br>
                                <strong>{{ $orderArray['order']->name }}</strong><br>
                                Phone: {{ $orderArray['order']->phone }}<br>
                                Email: {{ $orderArray['order']->email }}<br>
                                Address: {{ $orderArray['order']->address }}<br>
                                Pin: {{ $orderArray['order']->pin }}<br>
                                Location: {{ $orderArray['order']->location }}<br>
                                Landmark: {{ $orderArray['order']->landmark }}<br>
                                City: {{ $orderArray['order']->city }}<br>
                                State: {{ $orderArray['order']->state_name }}<br>
                                Country: {{ $orderArray['order']->country_name }}<br>
                            </address> --}}

                            <address>
                                <strong style="float:right">Order ID :{{ $orderArray['order']->id }}</strong><br>
                                <strong style="float:right">Date : {{ date('d-m-Y h:ia', strtotime($orderArray['order']->date)) }}</strong><br>
                                <strong>{{ $orderArray['order']->name }}</strong><br>
                                <strong>{{ $orderArray['order']->email }}</strong><br>
                                <strong>{{ $orderArray['order']->phone }}</strong><br>


                                @if ($orderArray['order']->delivery_type == 'direct')
                                    <p><strong>Delivery Address</strong></p>

                                    Address: {{ $orderArray['order']->address }}<br>
                                    {{-- Phone: {{ $orderArray['order']->phone }}<br>
                                    Email: {{ $orderArray['order']->email }}<br> --}}
                                    Pin: {{ $orderArray['order']->pin }}<br>
                                    Location: {{ $orderArray['order']->location }}<br>
                                    Landmark: {{ $orderArray['order']->landmark }}<br>
                                    City: {{ $orderArray['order']->city }}<br>
                                    State: {{ $orderArray['order']->state_name }}<br>
                                    Country: {{ $orderArray['order']->country_name }}<br>
                                @elseif($orderArray['order']->delivery_type == 'pickup')
                                    <p><strong>Pickup Store Address</strong></p>
                                    Name: {{ $orderArray['order']->store_name }}<br>
                                    Address: {{ $orderArray['order']->store_address }}<br>
                                    Location: {{ $orderArray['order']->store_location }}<br>
                                    Contact Number: {{ $orderArray['order']->store_contact_number }}<br>
                                @endif
                            </address>
                        </div>
                    @endif



                    <div class="col-sm-12">
                        Payment Method: {{ ucfirst($orderArray['order']->payment_method) }}<br>

                        @if ($orderArray['order']->payment_gateway != '')
                            Payment Gateway: {{ ucfirst($orderArray['order']->payment_gateway) }}</td><br>
                            Transaction Status: {{ ucfirst($orderArray['order']->transaction_status) }}<br>

                        @endif
                    </div>


                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price Per Item</th>
                                    <th>Tax Per Item</th>
                                    <th>Total Amount(Including Tax)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            @if (count($orderArray['details']) > 0)
                                <tbody>
                                    @php
                                        $sub_total = 0;
                                        $grand_total = 0;
                                        $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item'));
                                    @endphp

                                    @foreach ($orderArray['details'] as $key => $order)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if ($order->product_image)
                                                    <img src="{{ asset('assets/uploads/products/' . $order->product_image) }}" width="50px">
                                                @else
                                                    <img src="{{ asset('img/no-image.jpg') }}" width="50px">
                                                @endif
                                                {{ $order->product_name }}

                                                <p class="mb-1"> Current Status : {{ $order->status == 'ordered' ? 'Active' : ucwords($order->status) }}</p>
                                            </td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->price, 2) }}</td>
                                            <td>{!! $common_settings[$currency_icon]['value'] !!} {{ number_format($order->total_tax / $order->quantity, 2) }}</td>
                                            <td>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($order->amount, 2) }}</td>
                                            <td><button type="button" class="btn btn-sm btn-warning cancel-product" data-id="{{ $order->id }}" data-orderid="{{ $orderArray['order']->id }}" {{ $order->status == 'ordered' ? '' : 'disabled' }}>CANCEL</button></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                                <tr>
                                    <td colspan="5"></td>
                                    <td>Sub Total(Excluding Tax)</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orderArray['order']->total_amount,2) }}</td>
                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                    <td>
                                        <td>Total Tax</td>
                                    </td>
                                    <td>
                                        {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orderArray['order']->total_tax_amount, 2) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                    <td>
                                        <td>Shipping Fee</td>
                                    </td>
                                    <td>
                                        @if ($orderArray['order']->shipping_charge > 0)
                                            {!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orderArray['order']->shipping_charge, 2) }}
                                        @else
                                            Free
                                        @endif
                                </tr>
                                {{-- <tr>
                                    <td colspan="5"></td>
                                    <td>Shipping</td>
                                    <td>
                                        <h6>Free</h6>
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-success">
                                        <h4>Grand Total</h4>
                                    </td>
                                    <td class="text-success">
                                        <h4>{!! $common_settings[$currency_icon]['value'] !!}{{ number_format($orderArray['order']->grand_total,2) }}<h4>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-danger text-center" colspan="6">Items not found in this order.</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="float-right">

                        <a href="{{ url('admin/ordersdetails') . '/' . $orderArray['order']->id . '?status=active' }}" class="btn {{ empty(request()->all()) || request()->status == 'active' || (request()->status != 'approved' && request()->status != 'rejected' && request()->status != 'completed') ? 'btn-success' : 'btn-primary' }}">Active</a>
                        <a href="{{ url('admin/ordersdetails') . '/' . $orderArray['order']->id . '?status=approved' }}" class="btn {{ request()->status == 'approved' ? 'btn-success' : 'btn-primary' }}">Approved</a>
                        <a href="{{ url('admin/ordersdetails') . '/' . $orderArray['order']->id . '?status=rejected' }}" class="btn {{ request()->status == 'rejected' ? 'btn-success' : 'btn-primary' }}">Rejected</a>
                        <a href="{{ url('admin/ordersdetails') . '/' . $orderArray['order']->id . '?status=completed' }}" class="btn {{ request()->status == 'completed' ? 'btn-success' : 'btn-primary' }}">Completed</a>
                    </div>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                {{-- <th>Customer</th> --}}
                                {{-- <th>Product</th> --}}
                                <th>Prescription file</th>
                                <th>Status</th>
                                @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                                    <th>Allowed Quantity</th>
                                    <th>Approved by</th>
                                @endif
                                <th>Created At</th>
                                <th class="action-icon">Action</th>
                            </tr>
                        </thead>
                        @forelse ($prescription_data as $key=>$row)
                            <tr>
                                <td>{{ $prescription_data->firstItem() + $key }}</td>
                                {{-- <td>{{ ucwords($row->customername) }}</td> --}}
                                {{-- <td>
                                    @if ($row->type == 'normal') --}}
                                {{-- <a href="{{ route('shopping.productdetail', $row->product_url) }}" target="_blank">{{ ucwords($row->product_name) }}</a> --}}
                                {{-- @else
                                        -
                                    @endif
                                </td> --}}
                                <td><a href="{{ asset('assets/uploads/prescription') . '/' . $row->file }}" target="_blank">Open Prescription file</a></td>
                                <td>{{ $row->status == 1 ? 'Active' : ($row->status == 2 ? 'Approved' : ($row->status == 3 ? 'Completed' : 'Rejected')) }}</td>
                                @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                                    <td>{{ $row->allowed_qty }}</td>
                                @endif
                                @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                                    <td>{{ $row->approved_by }}</td>
                                @endif
                                <td>{{ $row->created_at }}</td>
                                <td>
                                    <button type="button" data-id="{{ $row->id }}" class="btn btn-success btn-sm" {{ $row->status == 1 ? '' : ($row->status == 0 ? '' : 'disabled') }} data-toggle="modal" data-target="#ApproveModal">Approve</button>
                                    <button type="button" data-id="{{ $row->id }}" class="btn btn-danger btn-sm reject_purchase" {{ $row->status == 1 ? '' : ($row->status == 2 ? '' : 'disabled') }}>Reject</button>
                                    @if ($row->status == 0)
                                        <form method="GET" action="{{ route('prescription.destroy') }}">
                                            <button type="submit" name="delete[{{ $row->id }}]" class="btn btn-danger btn-sm" title="Delete Prescription" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No records found</td>
                            </tr>
                        @endforelse
                        {{ $prescription_data->appends(request()->except('page'))->links('pagination::bootstrap-4') }}


                    </table>
                @endif

            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        var loader_gif = '<img src="{{ asset('img/ajax-loader.gif') }}">';

        //----print-Order-Invoice--
        $('#print_invoice').on('click', function() {
            var orderID = $(this).val();
            window.open('{{ url('/admin/order/invoice_print') }}/' + orderID, 'name', 'width=1000,height=800');
        });

        $('.cancel-product').on('click', function() {
            if (confirm('Are you sure do you want to cancel the product?')) {
                var orderDetailsId = $(this).data('id');
                var orderId = $(this).data('orderid');

                $.ajax({
                    type: "POST",
                    data: {
                        orderDetailsId: orderDetailsId,
                        orderId: orderId,
                        '_token': '{{ csrf_token() }}'
                    },
                    url: '{{ route('admin.orderdetails.changestatus') }}',
                    success: function(data) {
                        if (data.result) {
                            alert(data.message);
                            document.location.reload(true);
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }
        });





        $('#ApproveModal').on('show.bs.modal', function(event) {
            var btn = event.relatedTarget;
            var prescrptionId = $(btn).attr('data-id');
            $('input[name="prescription_id"]').val(prescrptionId);
        });

        $('#approve_purchase').on('click', function() {
            if (confirm('Are you sure do you want to approve?')) {
                var prescriptionId = $('input[name="prescription_id"]').val();

                var qty = $('#allowed_qty').val();
                $("#ajax_loader").hide();
                if (qty > 0) {
                    $("#ajax_loader").show();
                    manage_prescription(prescriptionId, qty, 'approve');
                } else {
                    alert('Allowed Quantity must be greater than zero.');
                }
            }
        });

        $('.reject_purchase').on('click', function() {
            if (confirm('Are you sure do you want to reject?')) {
                $(this).closest('td').prepend(loader_gif);
                var prescriptionId = $(this).attr('data-id');
                manage_prescription(prescriptionId, 0, 'reject');
            }
        });

        function manage_prescription(prescriptionId = null, quantity = 0, mode = '') {
            if (prescriptionId != null && mode != '') {
                $.ajax({
                    type: "POST",
                    data: {
                        prescriptionId: prescriptionId,
                        quantity: quantity,
                        mode: mode,
                        '_token': '{{ csrf_token() }}'
                    },
                    url: '{{ route('manage.generalprescription') }}',
                    success: function(response) {

                        alert(response.message);
                        $("#myElem").html(response.message).show().delay(3000).fadeOut();

                        location.reload(true);
                        //  show().delay(3000).fadeOut();
                    }
                });

            } else {
                alert('Invalid request');
            }
            return true;
        }
    </script>
@endsection
