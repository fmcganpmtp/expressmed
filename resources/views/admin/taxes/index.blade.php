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
    <div class="alert alert-success" id="myElem" style="display: none"></div>
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Taxes</h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">
                    <a href="" class="btn btn-success btn-circle btn-lg" data-toggle="modal" data-target="#TaxescreateModal"><i class="fas fa-plus"></i></a>

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
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl.No</th>
                                <th>Tax Name</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th class="action-icon">Action</th>
                            </tr>
                            @forelse ($taxes as $key => $tax)
                                <tr>
                                    <td>{{ $key + $taxes->firstItem() }}</td>
                                    <td>{{ $tax->tax_name }}</td>
                                    <td>{{ $tax->percentage }}</td>
                                    <td id="display_status_{{ $tax->id }}">{{ $tax->status }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle btn-md" data-toggle="modal" data-target="#exampleModal{{ $tax->id }}"><i class="fas fa-edit"></i></button>
                                        <div id="outer_status_{{ $tax->id }}" class="float-left">
                                            @if ($tax->status == 'active')
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Disable post" title="Disable account" onclick="changeStatus({{ $tax->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                            @elseif($tax->status=='disabled')
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate post" title="Activate account" onclick="changeStatus({{ $tax->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Tax edit modal start --}}
                                    <div class="modal fade" id="exampleModal{{ $tax->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Update Tax</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('taxes.update') }}">
                                                        @csrf
                                                        <input type="hidden" name="tax_id" value="{{ $tax->id }}">
                                                        <div class="col-md-12">
                                                            <label>Name</label>
                                                            <input type="text" name="tax_nameUpdate" value="{{ $tax->tax_name }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label>Percentage</label>
                                                            <input type="text" name="percentageUpdate" value="{{ $tax->percentage }}" class="form-control">
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Tax edit modal end --}}

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-danger">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $taxes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add new taxes start --}}
    <div class="modal fade" id="TaxescreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Tax</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('taxes.create') }}">
                        @csrf
                        <div class="col-md-12">
                            <label>Name</label>
                            <input type="text" name="tax_name" value="{{ old('tax_name') }}" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label>Percentage(%)</label>
                            <input type="text" name="percentage" value="{{ old('percentage') }}" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add new taxes end --}}

@endsection

@section('footer_scripts')

<script>
    function changeStatus(tax_id, status) {
        if (tax_id) {
            var outerhtml = $("#outer_status_" + tax_id).html();
            $("#outer_status_" + tax_id).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
            $.ajax({
                type: "post",
                data: {
                    id: tax_id,
                    status: status,
                    "_token": "{{ csrf_token() }}"
                },
                url: "{{ route('taxes.changestatus') }}",
                success: function(res) {
                    if (res.ajax_status == 'success') {
                        if (status == 'active') {
                            html = '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Disable post" title="Disable" onclick="changeStatus(' + tax_id + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';

                            $("#outer_status_" + tax_id).html(html);
                            $("#display_status_" + tax_id).html(status);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        } else {
                            html = '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate post" title="Activate" onclick="changeStatus(' + tax_id + ',\'active\')"><i class="fas fa-check-circle"></i></a>';

                            $("#outer_status_" + tax_id).html(html);
                            $("#display_status_" + tax_id).html(status);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    } else {
                        $("#outer_status_" + tax_id).html(outerhtml);
                        $("#myElem").html(res.message);
                        $("#myElem").show().delay(3000).fadeOut();
                    }
                }
            });
        }
    }
</script>
@endsection
