<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            <input type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : (!empty($admin) ? $admin->name : '') }}">
            @if ($errors->has('name'))<span class="text-danger">{{ $errors->first('name') }}</span>@endif
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            <input type="text" placeholder="Email" class="form-control" name="admin_email" value="{{ old('admin_email') != '' ? old('admin_email') : (!empty($admin) ? $admin->email : '') }}">
            @if ($errors->has('admin_email'))<span class="text-danger">{{ $errors->first('admin_email') }}</span>@endif
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Phone:</strong>
            <input id="phone" type="text" placeholder="Phone" class="form-control" name="phone" value="{{ old('phone') != '' ? old('phone') : (!empty($admin) ? $admin->phone : '') }}">
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Jobtitle:</strong>
            <input type="text" placeholder="Jobtitle" class="form-control" name="job_title" value="{{ old('job_title') != '' ? old('job_title') : (!empty($admin) ? $admin->job_title : '') }}">
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Bio:</strong>
            <textarea placeholder="Bio" class="form-control" name="bio">{{ old('bio') != '' ? old('bio') : (!empty($admin) ? $admin->bio : '') }}</textarea>
        </div>
    </div>

    <div class="form-group">
        @if ($admin->profile_pic != '')
            <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic }}" alt="{{ $admin->profile_pic }}" width="200px" />
        @endif
        <input type="File" class="course-img" name="profile_pic">
        @if ($errors->has('profile_pic'))<span class="text-danger">{{ $errors->first('profile_pic') }}</span>@endif
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>

</div>
