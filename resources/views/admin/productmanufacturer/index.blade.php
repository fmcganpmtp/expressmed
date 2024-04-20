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
        <h1 class="h3 mb-2 text-gray-800">Product Manufacturers</h1>
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
                    <a href="{{ route('manufacturers.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
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
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th class="action-icon">Action</th>
                            </tr>
                            @forelse ($productmanufacturers as $key=> $row)
                                <tr>
                                    <td>{{ ($productmanufacturers->currentpage() - 1) * $productmanufacturers->perpage() + $key + 1 }}</td>
                                    <td class="list-table-main-image">
                                        @if ($row->image != '')
                                            <img src="{{ asset('/assets/uploads/manufacturers/' . $row->image) }}" class="img-thumbnail" />
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    <td>{{ $row->name }}</td>
                                    <td>
                                        <form action="{{ route('manufacturers.destroy', $row->id) }}" method="POST">
                                            <a class="btn btn-warning btn-circle btn-md" href="{{ route('manufacturers.edit', $row->id) }}"><i class="fas fa-pen"></i></a>
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {!! $productmanufacturers->links('pagination::bootstrap-4')  !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
