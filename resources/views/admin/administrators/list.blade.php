@extends('layouts.admin')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
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
        <h1 class="h3 mb-2 text-gray-800">Administrators</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a href="{{ route('admin.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                <div class="float-right">
                    <a href="{{ route('admin.list') }}" class="btn btn-primary">Accounts</a>
                    <a href="{{ route('admin.roles') }}" class="btn btn-primary">Roles</a>
                    <a href="{{ route('admin.permissions') }}" class="btn btn-primary">Permissions</a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Job title</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th class="action-icon">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $count = 0;
                                $skipped = $admin->currentPage() * $admin->perPage();
                            @endphp
                            @forelse($admin as $rows)
                                <tr>
                                    <td>{{ ($admin->currentpage() - 1) * $admin->perpage() + $count + 1 }}</td>
                                    <td class="list-table-image">
                                        @if ($rows->profile_pic != '')
                                            <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $rows->profile_pic }}" alt="{{ $rows->profile_pic }}" />
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    <td>{{ $rows->name }}</td>
                                    <td>{{ $rows->email }}</td>
                                    <td>{{ $rows->phone }}</td>
                                    <td>{{ $rows->job_title }}</td>
                                    <td>{{ $rows->role }}</td>
                                    <td>{{ $rows->created_at }}</td>
                                    <td>
                                        <form class="form-horizontal" method="POST" action="{{ route('admin.delete', $rows->id) }}">
                                            <a href="{{ route('admin.edit', ['id' => $rows->id]) }}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                            <a href="{{ route('admin.show', ['id' => $rows->id]) }}" class="btn btn-info btn-circle btn-md"><i class="fas fa-eye"></i></i></a>
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                            @empty
                                <tr>
                                    <td colspan="9" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                            {{ $admin->links('pagination::bootstrap-4') }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
