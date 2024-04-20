@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Completed Orders Amount Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><i class="fa fa-rupee-sign"></i>{{number_format($totalordersamount,2)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders count Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalorders}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Medicines
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{$total_medicines}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active orders card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$activeorders}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Orders Graph <small>{{ (isset($_GET['GraphType']) ? ucwords($_GET['GraphType']) : 'Daily' ) }} Wise</small></h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <form id="OrdersGraph" method="get">
                            <input type="hidden" name="GraphType" id="GraphType" value="0">
                        </form>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="daily">Daily</a>
                            <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="weekly">Weekly</a>
                            <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="monthly">Monthly</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Ordered
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Cancelled
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Completed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Content Column -->
        <div class="col-lg-6 mb-4">

            <!-- Order Details Status -->

            @if(!empty($orderDetailStatus))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ordered Product Status</h6>
                    </div>
                    <div class="card-body">
                        @php $StatusPercentage = 0; $StatusBarClass = ''; @endphp
                        @foreach($orderDetailStatus as $key=>$Order_row)
                            <h4 class="small font-weight-bold">Order ID: {{ $Order_row['order_id'] }}</h4>
                            <div class="card-body">
                                @foreach($Order_row['order_details'] as $order_details_row)
                                    @php $StatusPercentage = ($order_details_row->status == 'ordered') ? 100*1/3 : (($order_details_row->status == 'shipped') ? 100*2/3 : ($order_details_row->status == 'delivered' ? 100*3/3 : 0) ) @endphp
                                    @php $StatusBarClass = ($order_details_row->status == 'ordered') ? 'bg-primary' : (($order_details_row->status == 'shipped') ? 'bg-info' : ($order_details_row->status == 'delivered' ? 'bg-success' : '') ) @endphp

                                    <h4 class="small font-weight-bold">{{ $order_details_row->product_name }} <span class="float-right">{{ strtoupper($order_details_row->status) .' '. number_format($StatusPercentage, 2)}}%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar {{$StatusBarClass}}" role="progressbar" style="width: {{$StatusPercentage}}%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif


            <!-- Color System -->
            {{-- <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            Primary
                            <div class="text-white-50 small">#4e73df</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            Success
                            <div class="text-white-50 small">#1cc88a</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            Info
                            <div class="text-white-50 small">#36b9cc</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            Warning
                            <div class="text-white-50 small">#f6c23e</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            Danger
                            <div class="text-white-50 small">#e74a3b</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-secondary text-white shadow">
                        <div class="card-body">
                            Secondary
                            <div class="text-white-50 small">#858796</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-light text-black shadow">
                        <div class="card-body">
                            Light
                            <div class="text-black-50 small">#f8f9fc</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-dark text-white shadow">
                        <div class="card-body">
                            Dark
                            <div class="text-white-50 small">#5a5c69</div>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>

        <div class="col-lg-6 mb-4">

            <!-- Orders Notification -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Orders Notification <small>(New 10 order)</small></h6>
                </div>
                <div class="card-body">
                    <div>
                        @forelse($latest_orders as $orders_row)
                            <a href="{{ route('admin.order.details', $orders_row->id) }}">
                                <p>Order from {{ $orders_row->name }} on {{ date('Y-m-d h:i a', strtotime($orders_row->date)) }} Total Amount is <i class="fa fa-rupee-sign"></i>{{ $orders_row->grand_total }}
                                    <br>
                                    Order ID is {{ str_pad($orders_row->id, 6, 0, STR_PAD_LEFT) }}
                                </p>
                            </a>
                        @empty
                            <li><p>There are no active orders</p></li>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('footer_scripts')
    <script>
        //----Order Status pie chart--
        var activeOrders = {{$activeorders}};
        var cancelOrders = {{$cancelorders}}
        var completedOrders = {{$completedorders}}

        //----Orders line Graph--
        var graph_labels = [];
        var graph_data = [];

        @foreach($GraphDataArray as $key=>$GraphDataArray_row)
            graph_labels.push('{{ $key }}');
            graph_data.push('{{ $GraphDataArray_row }}');
        @endforeach

        $('.Selected_GraphType').on('click', function(){
            var graphType = $(this).attr('data-type');
            $('#GraphType').val(graphType);

            $('#OrdersGraph').submit();
        });
    </script>

    <!-- Page level plugins -->
    <script src="{{ asset('sb_admin/chart.js/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('sb_admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('sb_admin/js/demo/chart-pie-demo.js') }}"></script>
@endsection
