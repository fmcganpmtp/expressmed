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
    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Name</strong><span class="text-danger">*</span>
            <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') }}" autofocus>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Email</strong><span class="text-danger">*</span>
            <input id="admin_email" type="text" placeholder="Email" class="form-control" name="admin_email" value="{{ old('admin_email') }}">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Password</strong><span class="text-danger">*</span>
            <input id="password" type="password" placeholder="Password" class="form-control" name="password" value="{{ old('password') }}">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Phone:</strong>
            <input id="phone" type="text" placeholder="Phone" class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Jobtitle:</strong>
            <input id="job_title" type="text" placeholder="Jobtitle" class="form-control" name="job_title" value="{{ old('job_title') }}">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Bio:</strong>
            <textarea id="bio" placeholder="Bio" class="form-control" name="bio">{{ old('bio') }}</textarea>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8">
        <div class="form-group">
            <strong>Role</strong><span class="text-danger">*</span>
            <select class="form-control" name="role">
                <option value="">--Select Role--</option>
                @foreach ($roles as $items)
                    <option value="{{ $items->id }}" @if (old('role') == $items->id){{ 'Selected' }} @endif>{{ $items->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8 ">

        <div class="form-group">
            <strong>Licence:</strong>

           <input id="licence" type="File" class="course-img" name="licence">
        </div>
    </div>

    <div class="col-xs-8 col-sm-8 col-md-8 ">

        <div class="form-group">
            <strong>Profile Picture:</strong>

           <input id="profile_pic" type="File" class="course-img" name="profile_pic">
        </div>
    </div>


    <div class="col-xs-8 col-sm-8 col-md-8">
        <button type="submit" class="btn btn-primary">Create</button>
    </div>

</div>
