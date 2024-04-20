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
        <h1 class="h3 mb-2 text-gray-800">Customer Supports</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('customersupport.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tr>
                                <th>#</th>
                                <th>Customer Support</th>
                                <th>E Mail</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="action-icon">Action</th>
                            </tr>
                            @forelse ($CustomerSupports as $key => $row)
                                <tr>
                                    <td>{{ ($CustomerSupports->currentpage() - 1) * $CustomerSupports->perpage() + $key + 1 }}</td>
                                    <td class="list-table-image">
                                        @if ($row->profile_pic != '')
                                            <img src="{{ asset('/assets/uploads/customer_support/' . $row->profile_pic) }}" class="img-thumbnail" />
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" class="img-thumbnail" />
                                        @endif
                                        <div>{{ $row->name }}</div>
                                    </td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->phone }}</td>
                                    <td id="display_status_{{ $row->id }}">{{ $row->status }}</td>
                                    <td>
                                        <form action="{{ route('customersupport.destroy', $row->id) }}" method="POST">
                                            <a href="{{ route('customersupport.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>

                                            <div id="outer_status_{{ $row->id }}">
                                                @if ($row->status == 'active')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable post" title="Disable account" onclick="changeStatus({{ $row->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                @elseif($row->status=='disabled')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate post" title="Activate account" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {!! $CustomerSupports->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

<script>
    function changeStatus(cid, status) {
        if (confirm('Do you really want to '+status+' the customer support?')) {
            if (cid) {
                var outerhtml = $("#outer_status_" + cid).html();
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')

                $.ajax({
                    type: "post",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('customersupport.status') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = ' <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable post" title="Disable account" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else {
                                html = ' <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate post" title="Activate account" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a> ';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            }
                        } else {
                            $("#outer_status_" + cid).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            }
        }
    }
</script>
@endsection
