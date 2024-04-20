@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Products Report</h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">
                    <a href="{{ route('products.create') }}" class="btn btn-success btn-circle btn-lg" title="Add a Product"><i class="fas fa-plus"></i></a>

                    <div class="card-body">
                        <form action="" method="get">
                            <div class="form-group row">
                                <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="filter_type" class="form-control category" id='type_get' value="">
                                        <option value="">Select Type</option>
                                        @foreach ($Producttypes as $row)
                                            <option value="{{ $row->id }}" @if (request()->get('filter_type') == $row->id) selected @endif>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="filter_category" class="form-control category" id="subcategory" @if (isset($_GET['filter_category'])) get_id="{{ $_GET['filter_category'] }}" @endif value="">
                                        <option value="">Select Category</option>

                                    </select>
                                </div>


                                <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="price_filter" class="form-control " value="">
                                        <option value="">Select Price Range</option>
                                        <option value="0-500" @if (request()->get('price_filter') == '0-500') selected @endif>0-500</option>
                                        <option value="500-1000" @if (request()->get('price_filter') == '500-1000') selected @endif>500-1000</option>
                                        <option value="1001-50000" @if (request()->get('price_filter') == '1001-50000') selected @endif>Above 1000</option>
                                    </select>
                                </div>
                                {{request()->get('status')}}
                                <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="filter_status" class="form-control " value="">
                                        <option value="">Select Status</option>
                                        <option value="active" @if (request()->get('filter_status') == 'active') selected @endif>Active</option>
                                        <option value="review" @if (request()->get('filter_status') == 'review') selected @endif>Review</option>
                                        <option value="hidden" @if (request()->get('filter_status') == 'hidden') selected @endif>Hidden</option>
                                        <option value="sold-out" @if (request()->get('filter_status') == 'sold-out') selected @endif>Sold-Out</option>
                                    </select>
                                </div>

                                <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                                    <button type="submit" class="btn btn-info">Filter</button>
                                </div>
                            </div>
                            <div  class="col-md-12 export-btn text-end">
                                <button type="submit" class="btn btn-primary" name="export" value ="save">Export</button>
                            </div>
                        </form>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary" id="btnExport">Export</button> --}}
                    <table class="table table-bordered" width="100%" cellspacing="0" id="product_category">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Manufactured By</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Offer Price</th>
                            <th>Status</th>

                        </tr>
                        @php
                            $count = 0;
                            $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item'));
                        @endphp
                        @forelse($products as $row)
                            <tr>
                                <td>{{ $count++ + $products->firstItem() }}</td>
                                <td class="list-table-main-image">
                                    @if ($row->offer_price != 0)
                                        {{ number_format((($row->price - $row->offer_price) * 100) / $row->price, 0) . '% Discount' }}<br>
                                    @endif

                                    @if ($row->product_image)
                                        <img src="{{ asset('/assets/uploads/products/' . $row->product_image) }}" class="image-responsive">
                                    @else
                                        <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                    @endif
                                    <br />{{ $row->product_name }}
                                    @if ($row->not_for_sale == '1')
                                        <br /><span style="color: red;">{{ 'Not for sale' }}</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $row->brand }}</td> --}}
                                <td>{{ $row->manufacturer }}</td>
                                <td>{{ $row->producttype }}</td>
                                <td>{{ $row->category }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td>{!! $common_settings[$currency_key]['value'] !!}{{ number_format($row->price, 2) }}</td>
                                <td>{!! $common_settings[$currency_key]['value'] !!}{{ number_format($row->offer_price, 2) }}</td>
                                <td style="width:100px"><span>{{ $row->status }}&nbsp </span>
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
                    {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">

var typeId = $("#type_get").val();
        subcatId = $('#subcategory').val();
        // alert($('#subcategory').attr('get_id'));
        sel_id=$('#subcategory').attr('get_id');
        selcatId = (($('#subcategory').attr('get_id')=="")||($('#subcategory').attr('get_id')=="undefined"))?'0':sel_id

        $.ajax({
            url: "{{ route('find.subcategories') }}",
            type: "POST",
            data: {
                typeId: typeId,
                selcatId:selcatId,
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(data) {
                // alert(data.html);
                if (data) {

                    $('#subcategory').html(data.html);
                } else {
                    $('#subcategory').empty();
                }

            }
        });
        $(document).ready(function() {
            var CountryId = $("#country_get").val();
            $.ajax({
                url: "{{ route('ajax.stateLoader') }}",
                type: "POST",
                data: {
                    CountryId: CountryId,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(data) {

                    if (data) {
                        $('#states').empty();
                        $('#states').focus;
                        $('#states').append('<option value=""> Select State </option>');

                        Stateid = $('#states').attr('get_id');

                        $.each(data, function(key, value) {

                            selected = (value.id == Stateid ? 'selected' : '');
                            $('select[name="states"]').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                        });
                    } else {

                        $('#states').append('<option value=""> Select State </option>');

                    }
                }
            });
            $('.alert-success').fadeIn().delay(3000).fadeOut();
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

        $('#type_get').on('change', function() {
            var typeId = $(this).val();
            if (typeId) {
                $.ajax({
                    url: "{{ route('find.subcategories') }}",
                    type: "POST",
                    data: {
                        typeId: typeId,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {

                            $('#subcategory').html(data.html);
                        } else {
                            $('#subcategory').empty();
                        }


                    }
                });
            } else {
                $('#subcategory').empty();
                $('#subcategory').append('<option value=""> Select Category </option>');
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

            var table_html = $('#product_category')[0].outerHTML;
            //    table_html = table_html.replace(/ /g, '%20');
            table_html = table_html.replace(/<tfoot[\s\S.]*tfoot>/gmi, '');
            var css_html = '<style>td {border: 0.5pt solid #c0c0c0} .tRight { text-align:right} .tLeft { text-align:left} </style>';
            //    css_html = css_html.replace(/ /g, '%20');
            a.href = data_type + ',' + encodeURIComponent('<html><head>' + css_html + '</' + 'head><body>' + table_html + '</body></html>');
            //setting the file name
            a.download = 'exported_products_report' + postfix + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });
        $("#btnExport2").click(function(e) {
            window.open('data:application/vnd.ms-excel,' +
                '<table>' + $('#product_category > table').html() + '</table>');
            e.preventDefault();
        });
    </script>
@endsection
