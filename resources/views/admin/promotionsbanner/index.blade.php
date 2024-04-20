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
        <h1 class="h3 mb-2 text-gray-800">Promotion Banner</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('promotionbanner.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div id="myElem" class="alert alert-success float-right" style="display:none"></div>
                    <div class="card-body">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <strong>Banner Section</strong>
                                        <select name="filter_section" id="bannersection" class="form-control">
                                            <option value="">All Banner Section</option>
                                            <option value="mainbody" {{ isset($_GET['filter_section']) && $_GET['filter_section'] == 'mainbody' ? 'selected' : '' }}>Main Body</option>
                                            <option value="sidebar" {{ isset($_GET['filter_section']) && $_GET['filter_section'] == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                            <option value="mobile" {{ isset($_GET['filter_section']) && $_GET['filter_section'] == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <strong>Banner Position</strong>
                                        <select name="filter_position" id="bannerposition" class="form-control">
                                            @if (isset($_GET['filter_section']) && $_GET['filter_section'] == 'mainbody')
                                                <option value="">--Choose Position--</option>
                                                <option value="maintop" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                                <option value="middle" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'middle' ? 'selected' : '' }}>Middle</option>
                                                <option value="footer" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'footer' ? 'selected' : '' }}>Bottom1</option>
                                                <option value="footer2" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'footer2' ? 'selected' : '' }}>Bottom2</option>
                                                <option value="footer3" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'footer3' ? 'selected' : '' }}>Bottom3</option>
                                                <option value="footer4" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'footer4' ? 'selected' : '' }}>Bottom4</option>

                                            @elseif (isset($_GET['filter_section']) && $_GET['filter_section'] == 'sidebar')
                                                <option value="">--Choose Position1--</option>
                                                <option value="top" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'top' ? 'selected' : '' }}>Sidebar Top</option>
                                                <option value="top2" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'top2' ? 'selected' : '' }}>Sidebar Top 2</option>
                                                <option value="top3" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'top3' ? 'selected' : '' }}>Sidebar Top 3</option>
                                                <option value="top4" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'top4' ? 'selected' : '' }}>Sidebar Top 4</option>

                                                <option value="bottom" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'bottom' ? 'selected' : '' }}>Product Details-Sidebar 1</option>
                                                <option value="bottom2" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'bottom2' ? 'selected' : '' }}>Product Details-Sidebar 2</option>
                                            @elseif (isset($_GET['filter_section']) && $_GET['filter_section'] == 'mobile')
                                                <option value="">--Choose Position--</option>
                                                <option value="maintop" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'maintop' ? 'selected' : '' }}>Top Main Banner</option>
                                                <option value="middle" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'middle' ? 'selected' : '' }}>Middle</option>
                                                <option value="footer" {{ isset($_GET['filter_position']) && $_GET['filter_position'] == 'footer' ? 'selected' : '' }}>Bottom</option>
                                            @else
                                                <option value="">Choose Any Banner Section</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <strong>Status</strong>
                                        <select name="filter_status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="active" {{ isset($_GET['filter_status']) && $_GET['filter_status'] == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="disabled" {{ isset($_GET['filter_status']) && $_GET['filter_status'] == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <div class="form-group">
                                        <br>
                                        <button type="submit" class="btn btn-info">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl no</th>
                                    <th>Banner Title</th>
                                    <th>Section</th>
                                    <th>Position</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @php $count=0; @endphp
                            {{-- {{dd($promotionbanners)}} --}}
                            @forelse ($promotionbanners as $row)
                                @php
                                    switch ($row->position) {
                                        case 'maintop':
                                            $position = 'Top Main Banner';
                                            break;
                                        case 'middle':
                                            $position = 'Middle';
                                            break;
                                        case 'footer':
                                            $position = 'Bottom';
                                            break;
                                        case 'top':
                                            $position = 'Sidebar Top';
                                            break;
                                        case 'top2':
                                            $position = 'Sidebar Top 2';
                                            break;
                                        case 'top3':
                                            $position = 'Sidebar Top 3';
                                            break;
                                        case 'top4':
                                            $position = 'Sidebar Top 4';
                                            break;
                                        case 'bottom':
                                            $position = 'Product Details-Sidebar 1';
                                            break;
                                        case 'bottom2':
                                            $position = 'Product Details-Sidebar 2';
                                            break;
                                        default:
                                            $position = 'Banner Position';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ ($promotionbanners->currentpage() - 1) * $promotionbanners->perpage() + $count + 1 }}</td>
                                    <td>{{ ucwords($row->title) }}</td>
                                    <td>{{ ucwords($row->section) }}</td>
                                    <td>{{ ucwords($position) }}</td>
                                    <td>{{ ucwords($row->type) }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td id="display_status_{{ $row->id }}">{{ strtoupper($row->status) }}</td>
                                    <td>
                                        <form class="form-horizontal" method="POST" action="{{ route('promotionbanner.destroy', $row->id) }}">
                                            @csrf
                                            <a href="{{ route('promotionbanner.show', $row->id) }}" class="btn btn-warning btn-circle btn-md" title="View Promotion Banner"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('promotionbanner.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md" title="Edit Promotion Banner"><i class="fas fa-pen"></i></a>

                                            <button type="submit" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            <div id="outer_status_{{ $row->id }}">
                                                @if ($row->status == 'active')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Disable Banner" onclick="changeStatus({{ $row->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                @elseif($row->status == 'disabled')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Activate Banner" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                            @empty
                                <tr>
                                    <td colspan="9" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse

                        </table>
                        {{ $promotionbanners->appends(request()->except('page'))->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(document).on('change', '#bannersection', function() {
            var section = $(this).val();
            $('#bannertype').val('Choose any position');
            $('#div_bannerimage').html('');
            $('#imageinfo').text('');
            $('#addmoreimage').hide();

            var ban_position_option = '';
            if (section == 'mainbody') {
                ban_position_option = '<option value="">--Choose Position--</option>';
                ban_position_option += '<option value="maintop">Top Main Banner</option>';
                ban_position_option += '<option value="middle">Middle</option>';
                ban_position_option += '<option value="footer">Bottom1</option>';
                ban_position_option += '<option value="footer2">Bottom2</option>';
                ban_position_option += '<option value="footer3">Bottom3</option>';
                ban_position_option += '<option value="footer4">Bottom4</option>';

                $('#bannerposition').focus();
            } else if (section == 'sidebar') {
                ban_position_option = '<option value="">--Choose Position--</option>';
                ban_position_option += '<option value="top">Sidebar Top</option>';
                ban_position_option += '<option value="top2">Sidebar Top 2</option>';
                ban_position_option += '<option value="top3">Sidebar Top 3</option>';
                ban_position_option += '<option value="top4">Sidebar Top 4</option>';
                ban_position_option += '<option value="bottom">Product Details-Sidebar 1</option>';
                ban_position_option += '<option value="bottom2">Product Details-Sidebar 2</option>';
                $('#bannerposition').focus();
            } else if (section == 'mobile') {
                ban_position_option = '<option value="">--Choose Position--</option>';
                ban_position_option += '<option value="maintop">Top Main Banner</option>';
                ban_position_option += '<option value="middle">Middle</option>';
                ban_position_option += '<option value="footer">Bottom</option>';
                $('#bannerposition').focus();
            } else {
                ban_position_option = '<option value="">--Choose Banner section--</option>';
            }
            $('#bannerposition').html(ban_position_option);
        });

        function changeStatus(id, status) {
            if (id) {
                var outerhtml = $("#outer_status_" + id).html();
                $("#outer_status_" + id).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
                $.ajax({
                    type: "post",
                    data: {
                        id: id,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('promotionbanner.changestatus') }}",
                    success: function(res) {
                        if (res.result == 'success') {
                            if (status == 'active') {
                                html =
                                    '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Disable Banner" onclick="changeStatus(' + id +
                                    ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                $("#outer_status_" + id).html(html);
                                $("#display_status_" + id).text(status.toUpperCase());
                                $("#myAlert").text(res.message);
                                $("#myAlert").show().delay(2000).fadeOut();
                                $("#myAlert").addClass('alert-success').removeClass('alert-danger');
                            } else {
                                html =
                                    '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" title="Activate Banner" onclick="changeStatus(' +
                                    id + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                $("#outer_status_" + id).html(html);
                                $("#display_status_" + id).text(status.toUpperCase());
                                $("#myAlert").text(res.message);
                                $("#myAlert").show().delay(2000).fadeOut();
                                $("#myAlert").addClass('alert-success').removeClass('alert-danger');
                            }
                        } else {
                            $("#outer_status_" + id).html(outerhtml);
                            $("#myAlert").text(res.message);
                            $("#myAlert").show().delay(2000).fadeOut();
                            $("#myAlert").addClass('alert-danger').removeClass('alert-success');
                        }
                    }
                });
            }
        }
    </script>
@endsection
