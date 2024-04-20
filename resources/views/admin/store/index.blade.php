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
        <h1 class="h3 mb-2 text-gray-800">Stores</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('store.create') }}" class="btn btn-success btn-circle btn-lg" style="background-color: #36cc81"><i class="fas fa-plus"></i></a>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tr>
                                <th>Sl no</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th width="200px">Action</th>
                            </tr>
                               @php $count=0; @endphp
                            @foreach ($data as $row)
                                @php $count++; @endphp
                                <tr>
                                    <td>{{ ($data->currentpage() - 1) * $data->perpage() + $count }}</td>
                            {{-- @php
                                $count = 0;
                            @endphp
                            @foreach ($data as $row)
                                @php
                                    $count++;
                                @endphp
                                <tr>
                                    <td>{{ $count }}</td> --}}
                                    <td>{{ $row->name }}</td>
                                    <td>{!! \Illuminate\Support\Str::limit(strip_tags($row->address), 250, '...') !!}</td>
                                    <td>{{ $row->location }}</td>

                                    <td class="action_button_outer">
                                        <a href="{{ route('store.show', $row->id) }}" class="btn btn-info btn-circle btn-md"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('store.edit', $row->id) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                        @if ($row->store_id==$row->id)
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('This store working.So cannot delete it')"><i class="fas fa-trash"></i></button>
                                        @else
                                            <form action="{{ route('store.destroy', $row->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-circle btn-md" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        {{ $data->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
