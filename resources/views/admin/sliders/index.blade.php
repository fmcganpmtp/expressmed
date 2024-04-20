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
        <h1 class="h3 mb-2 text-gray-800">Sliders</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('slider.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div class="float-right">
                        <a href="{{ route('admin.sliders') }}" class="btn btn-primary">Slider</a>
                        <a href="{{ route('admin.contentpages') }}" class="btn btn-primary">Content Page</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Created at</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @php
                                $count = 0;
                            @endphp

                            @forelse ($sliders as $row)
                                @php
                                    $count++;
                                @endphp
                                <tr>
                                    <td>{{ ($sliders->currentpage() - 1) * $sliders->perpage() + $count }}</td>
                                    <td> {{ $row->name }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>
                                        <form class="form-horizontal" method="POST" action="{{ route('slider.destroy', $row->id) }}">
                                            @csrf
                                            <a href="{{ route('slider.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                            <a href="{{ route('slider.show', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-eye"></i></a>
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $sliders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
