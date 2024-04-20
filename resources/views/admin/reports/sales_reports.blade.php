@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Sales Report {!! $productname != '' ? 'of <small>' . $productname . '</small>' : '' !!}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{{ session('success') }}</li>
                        </ul>
                    </div>
                @endif
                <form action="" method="get" id="sales_filter">
                    <div class="form-group">
                        <div class="row align-items-end">
                            <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                <label for="dob" class="input-label">Date From: </label>
                                <select name="filter_status" id="filter_status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="ordered" {{ request()->get('filter_status') == 'ordered' ? 'selected' : '' }}>Active</option>
                                    <option value="cancelled" {{ request()->get('filter_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="shipped" {{ request()->get('filter_status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request()->get('filter_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </div>

                            <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">

                                <label for="dob" class="input-label">Date From: </label>
                                <input type="date" id="date-from" class="form-control" name="date_from" placeholder="" max="{{ now()->toDateString('Y-m-d') }}" value="{{ request()->get('date_from') != null ? date('Y-m-d', strtotime(request()->get('date_from'))) : '' }}">
                            </div>

                            <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                <label for="dob" class="input-label">Date To: </label>
                                <input type="date" id="date-to" class="form-control" name="date_to" placeholder="" max="{{ now()->toDateString('Y-m-d') }}" value="{{ request()->get('date_to') != null ? date('Y-m-d', strtotime(request()->get('date_to'))) : '' }}">

                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">Filter</button>
                            </div>

                            <input type="hidden" name="productid" value="{{ request()->get('productid') != '' ? request()->get('productid') : 0 }}">
                            <input type="hidden" name="userid" value="{{ request()->get('userid') != '' ? request()->get('userid') : 0 }}">
                        </div>
                    </div>
                    <div class="col-md-12 export-btn text-end">
                        <button type="submit" class="btn btn-primary" name="export" value="save">Export</button>
                    </div>
                </form>

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
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
                                    <td>{{ $key + $salesList->firstItem() }}</td>
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
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ $value->total_amount }}</td>
                                    <td>{!! $common_settings[$currency_icon]['value'] !!}{{ $value->grand_total }}</td>
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

                    {{ $salesList->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
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
    <script type="text/javascript">
        $('#date-to').on('change', function() {
            var startDate = $('#date-from').val();
            var endDate = $('#date-to').val();
            if (endDate < startDate) {
                alert('End date should be greater than Start date.');
                $('#date-to').val('');
            }
        });
        $('#date-from').on('change', function() {
            var startDate = $('#date-from').val();
            var endDate = $('#date-to').val();
            if (endDate > startDate) {
                alert('Start date should be less than End date.');
                $('#date-from').val('');
            }
        });
        $("#btnExport").click(function(e) {
            //getting values of current time for generating the file name
            var dt = new Date();
            var day = dt.getDate();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var hour = dt.getHours();
            var mins = dt.getMinutes();
            var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
            //creating a temporary HTML link element (they support setting file names)
            var a = document.createElement('a');
            //getting data from our div that contains the HTML table
            var data_type = 'data:application/vnd.ms-excel;charset=utf-8';

            var table_html = $('#dataTable')[0].outerHTML;
            //    table_html = table_html.replace(/ /g, '%20');
            table_html = table_html.replace(/<tfoot[\s\S.]*tfoot>/gmi, '');
            var css_html = '<style>td {border: 0.5pt solid #c0c0c0} .tRight { text-align:right} .tLeft { text-align:left} </style>';
            //    css_html = css_html.replace(/ /g, '%20');
            a.href = data_type + ',' + encodeURIComponent('<html><head>' + css_html + '</' + 'head><body>' + table_html + '</body></html>');
            //setting the file name
            a.download = 'exported_order_report' + postfix + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });
        $("#btnExport2").click(function(e) {
            window.open('data:application/vnd.ms-excel,' +
                '<table>' + $('#dataTable > table').html() + '</table>');
            e.preventDefault();
        });

        // $(document).on('change', '#filter_status', function() {
        //     // var orderstatus = $(this).val();
        //     document.getElementById('order_filter').submit();
        // });

        //----print-Order-Invoice--
        $('.print_invoice').on('click', function() {
            var orderID = $(this).val();
            window.open('{{ url('/admin/order/invoice_print') }}/' + orderID, 'name', 'width=1000,height=800');
        });

        $(document).on('click', '.button-change-status', function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            let status = $(this).data('status');

            $('#form-change-status').attr('action', url);
            $('#status').val(status);
            $('#modal-status').modal('toggle');
        });
    </script>
@endsection
