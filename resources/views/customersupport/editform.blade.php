@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{ __('messages.Whoops') }}!</strong> {{ __('messages.There were some problems with your input') }}.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    {{ csrf_field() }}
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ $customersupport->name }}" required autofocus>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            <input type="text" placeholder="Email" class="form-control" name="cs_email" value="{{ $customersupport->email }}" required autofocus>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Phone:</strong>
            <input type="text" placeholder="Phone" class="form-control" name="phone" value="{{ $customersupport->phone }}">
        </div>
    </div>
    <div class="form-group">
        @if ($customersupport->profile_pic != '')
            <img src="{{ asset('assets/uploads/customer_support/') }}/{{ $customersupport->profile_pic }}" alt="{{ $customersupport->profile_pic }}" width="200px" />
        @endif
        <input type="File" class="course-img" name="profile_pic">
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</div>
