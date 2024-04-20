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

@if(session('success'))
<div class="alert alert-success">
    <ul>
    <li>{{session('success')}}</li>
    </ul>
</div>
@endif
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Testimonials</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <a href="{{ route('testimonials.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
                <div class="card-body">
                    <table  class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Profile pic</th>
                                <th>Name</th>
                                <th>Company Name</th>
                                <th>title</th>
                                <th>Comments</th>
                                <th class="action-icon">Action</th>
                            </tr>
                        </thead>
                        @php
                        $count=0;
                        @endphp
                         @forelse($testimonials as $row)
                           <tr>
                                <td>{{ ($testimonials ->currentpage()-1) * $testimonials ->perpage() + $count + 1 }}</td>
                                <td class="list-table-image">
                                    @if($row->profile_pic)
                                        <img src="{{ asset('/assets/uploads/testimonials/'.$row->profile_pic) }}" class="image-responsive">
                                    @else
                                        <img src="{{ asset('/img/no-image.jpg') }}" class="image-responsive">
                                    @endif
                                </td>
                                <td> {{$row->name}}</td>
                                <td>{{$row->company_name}}</td>
                                <td>{{$row->title}}</td>
                                <td>{{$row->comments}}</td>
                                <td>
                                    <form class="form-horizontal" method="POST" action="{{ route('testimonials.destroy',$row->id) }}">
                                        <a href="{{url('/testimonials/edit/'.$row->id)}}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                           </tr>
                           @php $count++; @endphp
                        @empty
                            <tr><td colspan="7" class="text-center text-danger">No records found</td></tr>
                        @endforelse

                    </table>
                    {{ $testimonials->links() }}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
