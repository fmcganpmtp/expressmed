@extends('layouts.admin')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong>
            {{ _('messages.There were some problems with your input') }}.<br><br>
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
        <h1 class="h3 mb-2 text-gray-800">Careers</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.careers') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Job Title:</strong>
                                <input id="job_title" type="text" placeholder="Job Title" class="form-control" name="job_title" value="{{ old('job_title', $careers->job_title) }}" autofocus>
                            </div>

                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Description:</strong>
                                <textarea name="description" class="form-control" placeholder="Description">{{ old('description', $careers->description) }}</textarea>

                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Skills:</strong>
                                <textarea name="skills" class="form-control" placeholder="Skills">{{ old('skills', $careers->skills) }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Vacancies:</strong>
                                <input id="vaccancies" type="number" placeholder="Vacancies" class="form-control" name="vacancies" value="{{ old('vacancies', $careers->no_of_vaccancies) }}" required>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option value="active" @if (old('status', $careers->status) == 'active') {{ 'selected' }} @endif>Active</option>
                                    <option value="disabled" @if (old('status', $careers->status) == 'disabled') {{ 'selected' }} @endif>Disabled</option>
                                    <option value="closed" @if (old('status', $careers->status) == 'closed') {{ 'selected' }} @endif>Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
