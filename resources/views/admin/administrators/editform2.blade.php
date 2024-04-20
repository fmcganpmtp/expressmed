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
<div class="modal" id="view_licence"role="dialog">
    <div class="modal-dialog">

    <div class="modal-content"style="width: 800px;;height: 750px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

      <!-- Modal Header -->
      <div class="modal-body">
        <embed src='{{ asset('assets/uploads/admin_licence/') }}/{{ $admin->licence}}'#toolbar=0 width="100%"height="550px">

      </div>

        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>


    </div>
  </div>

<div class="row">
    {{ csrf_field() }}
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Name:</strong>
            <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : $admin->name }}" autofocus>
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Email:</strong>
            <input id="admin_email" type="text" placeholder="Email" class="form-control" name="admin_email" value="{{ old('admin_email') != '' ? old('admin_email') : $admin->email }}">
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Password:</strong>
            <input id="password" type="password" placeholder="Password" class="form-control" name="password">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Phone:</strong>
            <input id="phone" type="text" placeholder="Phone" class="form-control" name="phone" value="{{ old('phone') != '' ? old('phone') : $admin->phone }}">
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Jobtitle:</strong>
            <input id="job_title" type="text" placeholder="Jobtitle" class="form-control" name="job_title" value="{{ old('job_title') != '' ? old('job_title') : $admin->job_title }}">
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Bio:</strong>
            <textarea id="bio" placeholder="Bio" class="form-control" name="bio">{{ old('bio') != '' ? old('bio') : $admin->bio }}</textarea>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Role:</strong>
            <select class="form-control" name="role">
                <option value="">--Select Role--</option>
                @foreach ($roles as $items)
                    <option value="{{ $items->id }}" {{ !empty(old('role')) && old('role') == $items->id ? 'selected' : ($admin->role_id == $items->id ? 'Selected' : '') }}>{{ $items->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">

            <strong>Licence:</strong><br>
            <input id="licence" type="File" class="course-img" name="licence">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Profile Picture:</strong><br>
            @if ($admin->profile_pic != '')

                <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic }}" alt="{{ $admin->profile_pic }}" width="200px" />
            @endif
            <input id="profile_pic" type="File" class="course-img" name="profile_pic">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <button type="submit" class="btn btn-primary">Update</button>

        @if ($admin->licence != '')
            <a type="button" class='btn btn-success' data-toggle="modal" data-target="#view_licence"><i class='fa fa-eye'></i> View Licence</a>
        @endif
    </div>

</div>
