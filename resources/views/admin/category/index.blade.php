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
        <h1 class="h3 mb-2 text-gray-800">Product Category</h1>
        <div class="card shadow mb-4">
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
                <div class="table-responsive">
                    <a href="{{ route('categories.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div class="float-right">
                        <a href="{{ route('admin.taxes') }}" class="btn btn-primary">Tax</a>
                        {{-- <a href="{{ route('admin.brands') }}" class="btn btn-primary">Brands</a> --}}
                        <a href="{{ route('admin.categories') }}" class="btn btn-primary">Categories</a>
                        <a href="{{ route('admin.producttype') }}" class="btn btn-primary">Type</a>
                        <a href="{{ route('admin.productcontent') }}" class="btn btn-primary">Contents</a>
                        <a href="{{ route('admin.supplier') }}" class="btn btn-primary">Supplier</a>
                        <a href="{{ route('admin.medicineUse') }}" class="btn btn-primary">Use</a>
                        <a href="{{ route('admin.manufacturers') }}" class="btn btn-primary">Manufacturer</a>
                        <a href="{{ route('admin.products') }}" class="btn btn-primary">Products</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Parent Category</th>
                                <th>Image</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="action-icon">Action</th>
                            </tr>
                            @php
                                $count = 0;
                            @endphp
                            @forelse ($category as $row)
                                <tr>
                                    <td>{{ ($category->currentpage() - 1) * $category->perpage() + $count + 1 }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->parent_category }}</td>
                                    <td class="list-table-main-image">
                                        @if ($row->image != '')
                                            <img src="{{ asset('/assets/uploads/category/' . $row->image) }}" alt="{{ $row->name }}">
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    <td>{{ $row->description }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td>
                                        @if ($row->status != 'deleted')
                                            <form action="{{ route('categories.destroy', $row->id) }}" method="POST">
                                                @csrf
                                                <a href="{{ route('categories.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                                <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-circle btn-md" data-toggle="modal" data-target="#categoryoffer_{{ $row->id }}">
                                            <i class="fas fa-gift"></i>
                                        </button>
                                    </td>
                                </tr>
                                <div class="modal fade" id="categoryoffer_{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Add Category offer percentage</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <span class='text-center text-success offer_success'></span>
                                            <form id="category_offer_form">
                                                <div class="modal-body">
                                                    @csrf
                                                    <div class="col-md-12">
                                                        <label>Offer Percentage(%)</label>
                                                        <input type="text" id='offer_percentage_{{ $row->id }}' name="offer_percentage" class="form-control " value="{{ $row->offer_percentage }}">
                                                        <span class="text-danger d-none" id="offerpercent-error"></span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary save_category_offer" value="{{ $row->id }}">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $count++;
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="7" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $category->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $('.save_category_offer').on('click', function(e) {
            e.preventDefault();

            let category_id = $(this).val();
            let offer_percentage = $('#offer_percentage_' + category_id).val();

            $.ajax({
                url: "{{ route('categoriesoffer.update') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    category_id: category_id,
                    offer_percentage: offer_percentage,

                },
                success: function(response) {
                    if (response) {
                        if(response.success){
                        $('.offer_success').text(response.success).fadeIn('slow');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }else{
                        alert(response.error)
                    }

                    }
                },
                error: function(response) {
                    $("#offerpercent-error").show();
                    $.each(response.responseJSON.errors, function(key, value) {

                        $("#" + key + '_' + category_id).next().html(value[0]);
                        $("#" + key + '_' + category_id).next().removeClass('d-none');

                    });
                    setTimeout(function() {
                        $("#offerpercent-error").hide();
                    }, 2500);

                }

            });
        });
    </script>
@endsection
