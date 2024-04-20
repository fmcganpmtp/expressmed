@extends('layouts.admin')
@section('content')

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{{ session('success') }}</li>
                </ul>
            </div>
        @endif

        <h1 class="h3 mb-2 text-gray-800">Careers</h1>
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="table-responsive">
                    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.careers') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Job Title:</strong>
                            {{ $careers->job_title }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Description:</strong>
                            {{ $careers->description }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Skills:</strong>
                            {{ $careers->skills }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Vacancies:</strong>
                            {{ $careers->no_of_vaccancies }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Created at:</strong>
                            {{ $careers->created_at }}
                        </div>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $careers->status }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <h2 class="h3 mb-2 text-gray-800">Applicants List</h2>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email Id</th>
                                <th>DOB</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @forelse($Applicants as $key=>$value)
                            <tr>
                                <td>{{ $key + $Applicants->firstItem() }}</td>
                                <td>{{ $value->applicant_name }}</td>
                                <td>{{ $value->phone }}</td>
                                <td>{{ $value->applicant_email }}</td>
                                <td>{{ $value->birthdate }}</td>
                                <td>{{ $value->address }}<br>{{ ($value->pin != '') ? 'Pin : '.$value->pin : '' }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-info btn-circle" data-toggle="modal" data-target="#Message_Applicant_{{ $value->id }}"><i class="fas fa-envelope"></i></a>
                                    @if ($value->resume != '')
                                        <a href="{{ asset('assets/uploads/careerjobs_resume/') . '/' . $value->resume }}" target="_blank" class="btn btn-link"><i class="fas fa-download"> Resume</i></a>
                                    @else
                                        <span class="text-danger">No Resume</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal of message --}}
                            <div id="Message_Applicant_{{ $value->id }}" class="modal fade" role="dialog" style="display: none">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Send Message to Applicant</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post" action="{{ route('careers.send_message', $value->id) }}">
                                            @csrf
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <strong>Subject</strong> <input type="text" name="subject" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <strong>Message</strong> <textarea name="message" class="form-control"></textarea>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div id="error_msg" class="text-danger" style="display: none"></div>
                                                <input type="submit" value="Send">
                                                {{-- <button type="submit" class="btn btn-primary btn-sm" value="send"> Send</button> --}}
                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-danger text-center">No applications found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $Applicants->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
