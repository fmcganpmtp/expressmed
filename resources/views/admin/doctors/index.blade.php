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
@if(session('error'))
<div class="alert alert-danger">
    <ul>
    <li>{{session('error')}}</li>
    </ul>
</div>
@endif
<div class="container-fluid">
<h1 class="h3 mb-2 text-gray-800">Doctors</h1>
{{-- <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam nec viverra nunc. Vivamus non orci tempus est interdum elementum id feugiat enim.</p> --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <a href="{{ route('doctor.create') }}" class="btn btn-success btn-circle btn-lg"><i class="fas fa-plus"></i></a>
            <div class="card-body">
                    <table  class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Department</th>
                                <th>Qualificaion</th>
                                <th>About</th>
								<th>Display Order</th>
                                <th>Instagram</th>
                                <th>Facebook</th>
                                <th>Twitter</th>
                                <th>Linkedin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @php $count=0; @endphp
                         @foreach($doctors as $row)
                           <tr>
                            <td>{{ ($doctors->currentpage() - 1) * $doctors->perpage() + $count + 1 }}</td>
                                <td> {{$row->name}}</td>
                                <td><img src="{{ asset('/assets/uploads/doctors/'.$row->image) }}" class="image-responsive" width="90" height="90"></td>
                                <td>{{$row->department}}</td>
                                <td>{{$row->qualification}}</td>
                                <td>{{$row->description}}</td>
								<td><input type="number" id="display_order" class="display_order" value="{{$row->displayorder}}" data-item="{{$row->id}}" /> </td>
                                <td>{{$row->instagram}}</td>
                                <td>{{$row->facebook}}</td>
                                <td>{{$row->twitter}}</td>
                                <td>{{$row->linkedin}}</td>

                                <td  class="action_button_outer">
                                    <a href="{{url('/doctor/edit/'.$row->id)}}" class="btn btn-warning btn-circle btn-md"><i class="fas fa-pen"></i></a>

                                    <form class="form-horizontal" method="POST" action="{{ route('doctor.delete',$row->id) }}">
                                    {{ csrf_field() }}
                                    <button type="submit" style="display: inline;" class="btn btn-danger btn-circle btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                           </tr>
                           @php $count++; @endphp
                        @endforeach
                    </table>
                    {{-- {{ $teams->links() }} --}}
                    {{ $doctors->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer_scripts')
<script>
    $(document).ready(function(){
        $(".alert-danger,.alert-success").delay(3000).fadeOut();

    })
	$(document).on('change', '.display_order', function(e) {
       var order_value = $(this).val();
	   var team_id = $(this).attr("data-item");
		$.ajax({
			dataType: 'json',
			type: 'POST',
			data: {
				"_token": "{{ csrf_token() }}",
				order_value: order_value, 
				team_id:team_id
			},
			url: "{{ route('doctor.orderchange') }}",
			success: function(data) { console.log(data);
				if (data.result == true) {
					alert("Successfully updated order");
				} else {
					alert(data.errorMsg);
				}
			}
		});
	});
</script>
@endsection
