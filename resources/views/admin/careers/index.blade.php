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

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Careers</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('careers.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div id="myElem" class="alert alert-success float-right" style="display:none"></div>
                    <div class="card-body">

                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl no</th>
                                    <th>Job Title</th>
                                    <th>Description</th>
                                    <th>Skills</th>
                                    <th>Vacancies</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @php $count=0; @endphp
                            @forelse ($careers as $row)
                                <tr>
                                    <td>{{ ($careers->currentpage() - 1) * $careers->perpage() + $count + 1 }}</td>
                                    <td>{{ $row->job_title }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($row->description, 150, '...') }}</td>
                                    <td>{{ $row->skills }}</td>
                                    <td>{{ $row->no_of_vaccancies }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td id="display_status_{{ $row->id }}">{{ $row->status }}</td>
                                    <td>
                                        <form class="form-horizontal" method="POST" action="{{ route('careers.destroy', $row->id) }}">
                                            <a href="{{ route('careers.show', $row->id) }}" class="btn btn-warning btn-circle btn-md" title="View Career"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('careers.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md" title="Edit Career"><i class="fas fa-pen"></i></a>

                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>

                                            <div id="outer_status_{{ $row->id }}">
                                                @if ($row->status == 'active')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Disable post" onclick="changeStatus({{ $row->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Close post" onclick="changeStatus({{ $row->id }},'closed')"><i class="fas fa-times-circle"></i></a>
                                                @elseif($row->status=='closed' || $row->status=='disabled')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Activate post" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                            @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse

                        </table>
                        {{ $careers->links() }}

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
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
                $.ajax({
                    type: "post",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('careers.status') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html =
                                    ' <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable post" title="Disable post" onclick="changeStatus(' + cid +
                                    ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>   <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Close post" title="Close post" onclick="changeStatus(' +
                                    cid + ',\'closed\')"><i class="fas fa-times-circle"></i></a> ';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);

                                $("#myElem").text(res.message);
                                $("#myElem").show().delay(2000).fadeOut();
                            } else {
                                html =
                                    ' <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate post" title="Activate post" onclick="changeStatus(' +
                                    cid + ',\'active\')"><i class="fas fa-check-circle"></i></a> ';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(2000).fadeOut();
                            }
                        } else {
                            $("#outer_status_" + cid).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(2000).fadeOut();
                        }
                    }

                });
            }
        }
    </script>
@endsection
