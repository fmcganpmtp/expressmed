@extends('layouts.admin')

@section('content')

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>{{ _('messages.Whoops') }}!</strong>
    {{ _('messages.There were some problems with your input') }}.<br><br>
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

<div id="myAlert" class="alert" style="display: none"></div>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Uploaded General Prescriptions</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <div id="myElem" class="alert alert-success float-right" style="display:none"></div>
                <div class="float-right">
                    <a href="{{ url('admin/general-prescription?status=active') }}" class="btn {{ ((empty(request()->all()) || request()->status == 'active' || (request()->status != 'approved' && request()->status != 'rejected' && request()->status != 'completed')) ? 'btn-success' : 'btn-primary') }}">Active</a>
                    <a href="{{ url('admin/general-prescription?status=approved') }}" class="btn {{ ((request()->status == 'approved') ? 'btn-success' : 'btn-primary') }}">Approved</a>
                    <a href="{{ url('admin/general-prescription?status=rejected') }}" class="btn {{ ((request()->status == 'rejected') ? 'btn-success' : 'btn-primary') }}">Rejected</a>
                    <a href="{{ url('admin/general-prescription?status=completed') }}" class="btn {{ ((request()->status == 'completed') ? 'btn-success' : 'btn-primary') }}">Completed</a>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current()}}" method="get">
                        <!-- <div class="row"> -->
                        <!-- <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <strong>Customer</strong>
                                        <select name="filter_customer" class="form-control">
                                            <option value="">--Select Filter--</option>
                                            @foreach($customerdropdown as $customer_row)
                                                <option value="{{ $customer_row->id }}" {{ (request()->filter_customer == $customer_row->id ? 'selected' : '') }}>{{ $customer_row->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <strong>Product</strong>
                                        <select name="filter_product" class="form-control">
                                            <option value="">--Select Product--</option>
                                            {{-- @foreach($productsdropdown as $product_row) --}}
                                                {{-- <option value="{{ $product_row->id }}" {{ (request()->filter_product == $product_row->id ? 'selected' : '') }}>{{ $product_row->product_name }}</option> --}}
                                            {{-- @endforeach --}}
                                        </select>
                                    </div>
                                </div> -->

                        <!-- @if(request()->has('status'))
                                    <input type="hidden" name="status" value="{{ request()->status }}">
                                @endif
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <br>
                                        <button type="submit" class="btn btn-info">Filter</button>
                                    </div>
                                </div> -->

                        <!-- </div> -->
                        <div class="form-group row">
                            <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                                <input type='text' class="form-control" placeholder="Search Keyword" name="search_keyword" value="{{ request()->get('search_keyword') }}">
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                                <button type='submit' class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Customer</th>
                                <th>Order ID</th>
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
                        @forelse ($prescription as $key=>$row)
                        <tr>
                            <td>{{ $prescription->firstItem() + ($key) }}</td>
                            <td>{{ ucwords($row->customername) }}</td>
                            <td><a href="{{route('admin.order.details',$row->order_id)}}" target="_blank">{{$row->order_id}}</a></td>
                            <td><a href="{{ asset('assets/uploads/prescription').'/'.$row->file }}" target="_blank">Open Prescription file</a></td>
                            <td>{{ ($row->status == 1 ? 'Active' : ($row->status == 2 ? 'Approved' : ($row->status == 3 ? 'Completed' : 'Rejected') )) }}</td>
                            @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                            <td>{{ $row->allowed_qty }}</td>
                            @endif
                            @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                            <td>{{$row->approved_by}}</td>
                            @endif
                            <td>{{ $row->created_at }}</td>
                            <td>
                                <button type="button" data-id="{{ $row->id }}" class="btn btn-success btn-sm" {{ ($row->status == 1 ? '' : ($row->status == 0 ? '' : 'disabled')) }} data-toggle="modal" data-target="#ApproveModal">Approve</button>
                                <button type="button" data-id="{{ $row->id }}" class="btn btn-danger btn-sm reject_purchase" {{ ($row->status == 1 ? '' : ($row->status == 2 ? '' : 'disabled')) }}>Reject</button>
                                @if($row->status == 0)
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
                    </table>
                    {{ $prescription->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

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

@endsection
@section('footer_scripts')
<script>

    var loader_gif = '<img src="{{ asset('img/ajax-loader.gif') }}">';
    // $(document).ready(function(){

    // })
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
                url: '{{ route("manage.generalprescription") }}',
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
