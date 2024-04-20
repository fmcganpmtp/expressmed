@extends('layouts.admin')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Products</h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">
                    <a href="{{ route('products.create') }}" class="btn btn-success btn-circle btn-lg" title="Add a Product"><i class="fas fa-plus"></i></a>

                    <div class="float-right">
                        <a href="{{ route('admin.taxes') }}" class="btn btn-primary">Tax</a>
                       <a href="{{ route('admin.brands') }}" class="btn btn-primary">Brands</a>
                        <a href="{{ route('admin.categories') }}" class="btn btn-primary">Categories</a>
                        <a href="{{ route('admin.producttype') }}" class="btn btn-primary">Type</a>
                        <a href="{{ route('admin.productcontent') }}" class="btn btn-primary">Contents</a>
                        <a href="{{ route('admin.supplier') }}" class="btn btn-primary">Supplier</a>
                        <a href="{{ route('admin.medicineUse') }}" class="btn btn-primary">Use</a>
                        <a href="{{ route('admin.manufacturers') }}" class="btn btn-primary">Manufacturer</a>
                        <a href="{{ route('admin.products') }}" class="btn btn-primary">Products</a>
                    </div>
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
                                        {{-- <optgroup label="Theropods">
                                            <option>Tyrannosaurus</option>
                                            <option>Velociraptor</option>
                                            <option>Deinonychus</option>
                                        </optgroup> --}}
                                        {{-- @foreach ($category as $category)
                                            <option value="{{ $category->id }}" id="category" @if (request()->get('filter_category') == $category->id) selected @endif>{{ $category->name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                {{-- <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="filter_brand" class="form-control category" value="">
                                        <option value="">Select Brand</option>
                                        @foreach ($brand as $row)
                                            <option value="{{ $row->id }}" id="category" @if (request()->get('filter_brand') == $row->id) selected @endif>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                                    <select name="price_filter" class="form-control " value="">
                                        <option value="">Select Price Range</option>
                                        <option value="0-500" @if (request()->get('price_filter') == '0-500') selected @endif>0-500</option>
                                        <option value="500-1000" @if (request()->get('price_filter') == '500-1000') selected @endif>500-1000</option>
                                        <option value="1001-50000" @if (request()->get('price_filter') == '1001-50000') selected @endif>Above 1000</option>
                                    </select>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                                    <input type="text" class="form-control" name="search_term" placeholder="Search Keyword" value="{{ request()->get('search_term') }}">
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                                    <button type="submit" class="btn btn-info">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="float-right border" style="padding: 2px">
                            <form method="POST" action="{{ route('products.import.bulk') }}" enctype="multipart/form-data">
                                <strong>Bulk products import </strong>

                                @csrf
                                <div class="input-group">
                                    <div>Download sample template <a href="{{ asset('assets/downloads/product/bulk_upload_template_example.xlsx') }}" onclick="return confirm('Do you want to download bulk upload template?')" download><i class="fa fa-download" aria-hidden="true"></i></a></div>
                                    <input type="file" class="form-control" name="bulk_products" style="border: 0">
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
                            <th class="action-icon">Action</th>
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
                                    @if($row->not_for_sale=='1')
                                    <br /><span style="color: red;">{{"Not for sale"}}</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $row->brand }}</td> --}}
                                <td>{{ $row->manufacturer }}</td>
                                <td>{{ $row->producttype }}</td>
                                <td>{{ $row->category }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td>{!! $common_settings[$currency_key]['value'] !!}{{ number_format($row->price, 2) }}</td>
                                <td>{!! $common_settings[$currency_key]['value'] !!}{{ number_format($row->offer_price, 2) }}</td>
                                <td style="width:100px"><span>{{ $row->status }}&nbsp  </span>
                                    @if($row->flag=="1")
                                    <br><span style="color: red;">{{"Sold-Out"}}</span>
                                    @endif

                                </td>
                                <td style="width:210px">
                                    <form action="{{ url('/products/delete/' . $row->id) }}" method="POST">
                                        <a href="{{ url('/admin/orders?productid=') }}{{ $row->id }}" class="btn btn-warning btn-circle btn-md" title="view Orders"><i class="fas fa-list"></i></a>
                                        @if ($row->status != 'deleted')
                                            <a href="{{ url('/products/edit/' . $row->id) }}" title="edit" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                        @endif
                                        <a href="{{ route('products.view', $row->id) }}" title="view" class="btn btn-warning btn-circle btn-md"><i class="fas fa-eye"></i></a><br>
                                        <a href="{{ route('products.duplicate', $row->id) }}" title="duplicate" class="btn btn-warning btn-circle btn-md"><i class="fas fa-clone"></i></a>

                                        @if (($row->status != 'deleted'))
                                            {{-- <a href="javascript:void(0)" class="btn btn-info btn-circle btn-md" title="Add to deals" data-productId="{{ $row->id }}" data-toggle="modal" data-target="#DealsModal"><i class="fas fa-gift"></i></a> --}}
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        @endif
                                        @if ($row->hide_from_site == '0')
                                            <a href="javascript:void(0)" title="hide" data-id="{{$row->id}}"  class="btn btn-warning btn-circle btn-md hide_option" data-item="hide"><i class="fas fa-eye-slash"></i></a>
                                        @elseif($row->hide_from_site == '1')
                                            <a href="javascript:void(0)" title="show" data-id="{{$row->id}}" class="btn btn-warning btn-circle  btn-md hide_option" data-item="show"><i class="fas fa-camera"></i></a>
                                        @endif

                                    </form>
                                    @if ($row->flag == '0')
                                            <a href="javascript:void(0)" title="sold out" data-id="{{$row->id}}"  class="btn btn-warning  btn-md sell_status" data-item="sold-out">Sold Out</a>

                                        @endif
                                        @if ($row->flag == '1')
                                            <a href="javascript:void(0)" title="sell" data-id="{{$row->id}}" class="btn btn-warning   btn-md sell_status" data-item="sell">Sell</a>

                                        @endif
                                        @if ($row->status == 'review')
                                            <a href="javascript:void(0)" title="Approve" data-id="{{$row->id}}" class="btn btn-success  btn-md approve_product">Approve</a>

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
                // if (data) {
                //     $('#subcategory').empty();
                //     $('#subcategory').focus;
                //     $('#subcategory').append('<option value=""> Select Category </option>');
                //     subcatId = $('#subcategory').attr('get_id');
                //     $.each(data, function(key, value) {
                //         selected = (value.id == subcatId ? 'selected' : '');
                //         $('select[name="filter_category"]').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                //     });
                // } else {

                //     $('#subcategory').append('<option value=""> Select Category </option>');

                // }
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

                        // if (data) {
                        //     $('#subcategory').empty();
                        //     $('#subcategory').focus;
                        //     $('#subcategory').append('<option value=""> Select Category </option>');
                        //     $.each(data, function(key, value) {

                        //         $('select[name="filter_category"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                        //     });
                        // } else {

                        //     $('#subcategory').append('<option value=""> Select Category </option>');

                        // }
                    }
                });
            } else {
                $('#subcategory').empty();
                $('#subcategory').append('<option value=""> Select Category </option>');
            }
        });

    </script>
@endsection
