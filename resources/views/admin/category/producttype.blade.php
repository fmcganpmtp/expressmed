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
        <h1 class="h3 mb-2 text-gray-800">Product Types</h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">
                    <a href="" class="btn btn-success btn-circle btn-lg" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i></a>

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
                                <th>Product Type</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th class="action-icon">Action</th>
                            </tr>
                            @forelse ($ProductTypes as $key => $value)
                                <tr>
                                    <td>{{ $key + $ProductTypes->firstItem() }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->description }}</td>
                                    <td class="list-table-main-image">
                                        @if ($value->image != '')
                                            <img src="{{ asset('/assets/uploads/category/' . $value->image) }}" alt="{{ $value->name }}">
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    {{-- <td class="{{ $value->type=='sys' ? 'text-danger' : 'text-success' }}">{{ $value->type }}</td> --}}
                                    <td>
                                        @if ($value->type != 'sys')
                                            <form class="form-horizontal" method="POST" action="{{ route('producttype.destroy', $value->id) }}">
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" data-toggle="modal" data-target="#exampleModal{{ $value->id }}"><i class="fas fa-edit"></i></a>

                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Are you sure do you want to delete this product type?')" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>

                                    {{-- Producttype edit modal start --}}
                                    <div class="modal fade" id="exampleModal{{ $value->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Update Product Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('producttype.update') }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="producttype_id" value="{{ $value->id }}">
                                                        <div class="col-md-12">
                                                            <label for="producttype_Update">Product Type</label>
                                                            <input type="text" name="producttype_update" value="{{ $value->name }}" class="form-control producttype_Update" autofocus>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="producttype">Description</label>
                                                            <textarea type="text" id="productdescription_update" name="productdescription_update" class="form-control">{{ $value->description }}</textarea>
                                                        </div>

                                                        <div class="col-md-12">

                                                            <strong> Image </strong>
                                                            @if ($value->image != '')
                                                                <br><img src="{{ asset('assets/uploads/category/') }}/{{ $value->image }}" alt="{{ $value->image }}" width="200px" height="150px"><br>

                                                            @endif

                                                            <input type="file" name="productimage_update" value="{{ old('image') }}" class="form-control">
                                                            <span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>

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
                                    {{-- Producttype edit modal end --}}

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-danger">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $ProductTypes->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add new producttype start --}}
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Product Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('producttype.create') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12">
                            <label for="producttype">Product Type</label>
                            <input type="text" id="producttype" name="producttype" value="{{ old('producttype') }}" class="form-control" autofocus>
                        </div>
                        <div class="col-md-12">
                            <label for="producttype">Description</label>
                            <textarea type="text" id="productdescription" name="productdescription" class="form-control">{{ old('productdescription') }}</textarea>
                        </div>

                        <div class="col-md-12">

                            <strong> Image </strong>
                            <input type="file" name="image" value="{{ old('image') }}" class="form-control">
                            <span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>

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
    {{-- Add new producttype end --}}

@endsection

@section('footer_scripts')
    <script type="text/javascript">
        $('.modal').on('show.bs.modal', function() {
            setTimeout(function() {
                $('#producttype').focus();
                $('.producttype_Update').focus().select();
            }, 500);
        });
    </script>
@endsection
