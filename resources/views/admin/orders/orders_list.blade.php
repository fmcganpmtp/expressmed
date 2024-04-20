@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Orders List {!! $productname != '' ? 'of <small>' . $productname . '</small>' : '' !!}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{{ session('success') }}</li>
                        </ul>
                    </div>
                @endif
                <form action="" method="get" id="order_filter">
                    <div class="form-group row">
                        <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                            <select name="filter_status" id="filter_status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="ordered" {{ request()->get('filter_status') == 'ordered' ? 'selected' : '' }}>Active</option>
                                <option value="cancelled" {{ request()->get('filter_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="shipped" {{ request()->get('filter_status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ request()->get('filter_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="productid" value="{{ request()->get('productid') != '' ? request()->get('productid') : 0 }}">
                    <input type="hidden" name="userid" value="{{ request()->get('userid') != '' ? request()->get('userid') : 0 }}">
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th class="action-icon"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currency_icon = array_search('site_currency_icon', array_column($common_settings, 'item')) @endphp
                            @forelse($ordersList as $key=>$value)
                                <tr>
                                    <td>{{ $key + $ordersList->firstItem() }}</td>
                                    <td><a href="{{ route('admin.order.details', $value->id) }}">{{ $value->id }}</a></td>
                                    <td>{{ $value->name }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ $value->total_amount }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ $value->grand_total }}</td>
                                    <td>{{ $value->status == 'ordered' ? 'Active' : ucwords($value->status) }}</td>
                                    <td>{{ date('d-m-Y h:i a', strtotime($value->date)) }}</td>
                                    <td>{{ucfirst($value->payment_method)}}</td>
                                    <td>
                                        <a href="{{ route('admin.order.details', $value->id) }}" class="btn btn-sm btn-primary btn-circle" title="Show Details"><i class="fas fa-eye"></i></a>
                                        <button type="button" value="{{ $value->id }}" class="btn btn-sm btn-primary btn-circle print_invoice" title="Print Invoice"><i class="fa fa-print"></i></button>
                                        <button class="btn btn-sm btn-primary btn-circle button-change-status" data-url="{{ route('admin.order.changestatus', $value->id) }}" data-status="{{ $value->status }}" title="Change Status"><i class="fas fa-exchange-alt"></i></button>
                                        <a href="{{ route('admin.order.details', $value->id) }}" class="btn btn-sm btn-primary btn-circle" title="Track Details"><i class="fa fa-truck" aria-hidden="true"></i></a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-danger">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $ordersList->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <div id="modal-status" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="form-change-status" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">STATUS</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option disabled>Status</option>
                                        <option value="{{ \App\Models\Order::STATUS_ORDERED }}">ORDERED</option>
                                        <option value="{{ \App\Models\Order::STATUS_CANCELLED }}">CANCELLED</option>
                                        <option value="{{ \App\Models\Order::STATUS_SHIPPED }}">SHIPPED</option>
                                        <option value="{{ \App\Models\Order::STATUS_DELIVERED }}">DELIVERED</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(document).on('change', '#filter_status', function() {
            // var orderstatus = $(this).val();
            document.getElementById('order_filter').submit();
        });

        //----print-Order-Invoice--
        $('.print_invoice').on('click', function() {
            var orderID = $(this).val();
            window.open('{{ url('/admin/order/invoice_print') }}/' + orderID, 'name', 'width=1000,height=800');
        });

        $(document).on('click', '.button-change-status', function (e) {
            e.preventDefault();
            let url = $(this).data('url');
            let status = $(this).data('status');

            $('#form-change-status').attr('action', url);
            $('#status').val(status);
            $('#modal-status').modal('toggle');
        });
    </script>
@endsection
