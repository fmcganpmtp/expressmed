@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Customers</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card-body">
                        <form method="get">
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
                                    <th>Profile pic</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered at</th>
                                    <th>Current Status</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @foreach ($users as $key => $row)
                                <tr>
                                    <td>{{ ($users->currentpage() - 1) * $users->perpage() + $key + 1 }}</td>
                                    <td class="list-table-image">
                                        @if ($row->profile_pic != '')
                                            <img src="{{ asset('/assets/uploads/profile/' . $row->profile_pic) }}" class="image-responsive">
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->phone }}</td>
                                    <td>{{ $row->created_at }}</td>

                                    <td id="display_status_{{ $row->id }}">{{ $row->status }}</td>
                                    <td class="action_button_outer">
                                        <div id="outer_status_{{ $row->id }}">
                                            <a href="{{ url('/admin/orders?userid=') }}{{ $row->id }}" class="btn btn-warning btn-circle btn-md" title="view Orders"><i class="fas fa-list"></i></a>
                                            <a href="{{ route('customers.view', $row->id) }}" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>
                                            @if ($row->status == 'active')
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable User" title="Disable User" onclick="changeStatus({{ $row->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus({{ $row->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @elseif($row->status=='disabled')
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate User" title="Activate User" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus({{ $row->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        function changeStatus(cid, status) {
            if (cid) {
                var outerhtml = $("#outer_status_" + cid).html();
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');

                $.ajax({
                    type: "POST",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('customers.changeStatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = '<a href="{{ url('admin/orders?userid=') }}' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Orders"><i class="fas fa-list"></i></a>';
                                html += '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable User" title="Disable User" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';

                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else if (status == 'disabled') {
                                html = '<a href="{{ url('admin/orders?userid=') }}' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Orders"><i class="fas fa-list"></i></a>';
                                html += '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate User" title="Activate User" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';

                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else {
                                html = '<a href="{{ url('admin/orders?userid=') }}' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Orders"><i class="fas fa-list"></i></a>';
                                html += '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
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
    </script>
@endsection
