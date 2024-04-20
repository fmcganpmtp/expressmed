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
        <h1 class="h3 mb-2 text-gray-800">Content Pages</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('contentpages.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div class="float-right">
                        <a href="{{ route('admin.sliders') }}" class="btn btn-primary">sliders</a>
                        <a href="{{ route('admin.contentpages') }}" class="btn btn-primary">Content Pages</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Page</th>
                                    <th>Title</th>
                                    <th>Position</th>
                                    <th>Created at</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @php
                                $count = 0;
                            @endphp

                            @forelse ($contents as $row)
                                @php
                                    $count++;
                                @endphp
                                <tr>
                                    <td>{{ ($contents->currentpage() - 1) * $contents->perpage() + $count }}</td>
                                    <td>{{ $row->page }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->page_position }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>
                                        <form action="{{ route('contentpages.destroy', $row->id) }}" method="POST">
                                            <a href="{{ route('contentpages.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                            <a href="{{ route('contentpages.show', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-eye"></i></a>
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $contents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
