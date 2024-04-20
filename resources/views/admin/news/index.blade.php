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
        <h1 class="h3 mb-2 text-gray-800">News</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('news.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl no</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Created At</th>
                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @php $count = 0; @endphp
                            @forelse ($news as $row)
                                <tr>
                                    <td>{{ ($news->currentpage() - 1) * $news->perpage() + $count + 1 }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td class="list-table-main-image">
                                        @if ($row->image != '')
                                            <img src="{{ asset('/assets/uploads/news/' . $row->image) }}" class="image-responsive">
                                        @else
                                        <img src="{{ asset('/img/no-image.jpg') }}" class="image-responsive">
                                        @endif
                                    </td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>
                                        <form class="form-horizontal" method="POST" action="{{ route('news.destroy',$row->id) }}">
                                            @csrf
                                            <a href="{{ route('news.show', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('news.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                            <button type="submit" onclick="return confirm('Do you really want to delete?')" class="btn btn-danger btn-circle btn-md"><i class="fas fa-trash"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                @php
                                    $count++;
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="6" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $news->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
