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
@if(session('success'))
<div class="alert alert-success">
    <ul>
        <li>{{session('success')}}</li>
    </ul>
</div>
@endif

<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4">
        <div class="form-group">
            <strong>Name:</strong>
            <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ (old('name') != '' ? old('name') : (!empty($permission) ? $permission->name : '') ) }}" autofocus autocomplete="off">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4">
        <div class="form-group">
            <strong>Slug:</strong>
            <input id="slug" type="text" placeholder="Slug" class="form-control" name="slug" value="{{ (old('slug') != '' ? old('slug') : (!empty($permission) ? $permission->slug : '') ) }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4">
        <button type="submit" class="btn btn-primary">{{ (!empty($permission) ? 'Update' : 'Submit') }}</button>
    </div>
</div>
