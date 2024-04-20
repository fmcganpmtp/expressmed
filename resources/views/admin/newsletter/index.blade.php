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

    <div id="myAlert" class="alert" style="display: none"></div>
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">News Letter </h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="myElem" class="alert alert-success float-right" style="display:none"></div>

                    <div class="card-body">
                        @if (!empty($newsletters))

                    <button type="button"  class="btn btn-primary btn-sm sendtoallmail_modal"data-toggle="modal" data-target="#AllMailModal">Send Mail</button>

                    @endif


                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl no</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    @if (request()->filled('status') && (request()->status == 'approved' || request()->status == 'completed'))
                                    @endif

                                    <th class="action-icon">Action</th>
                                </tr>
                            </thead>
                            @forelse ($newsletters as $key=>$row)
                                <tr>
                                    <td>{{ $newsletters->firstItem() + $key }}</td>
                                    <td>{{ $row->email_id }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td>
                                        <button type="button" data-id="{{ $row->id }}" data-email="{{ $row->email_id }}" class="btn btn-primary btn-sm sendmail_modal"  data-toggle="modal" data-target="#singlemailModal">Send Mail</button>
                                        {{-- {{ $row->status == 1 ? '' : ($row->status == 0 ? '' : 'disabled') }} --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                        </table>
                        {{ $newsletters->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="singlemailModal" tabindex="-1" role="dialog" aria-labelledby="ApproveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ApproveModalLabel">News Letter Mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="mail_alerts" class="alert alert-danger" style="display:none;"></div>
                <div id="mail_success" class="alert alert-success" style="display:none;"></div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <label>To:</label>
                        <input type="text" name="recipient" id="recipient_email"disabled class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label>Subject</label>
                        <input type="text" name="subject" id="mail_subject" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label>Message</label>
                        <textarea name="message" id="mail_content"class="form-control"></textarea>

                    </div>
                    <div class="col-md-12">
                        <label>File</label>
                        <input type="file" name="mail_file" id="mail_file_id" class="form-control">

                    </div>

                </div>
                <div class="modal-footer">
                    <div id="ajax_loader" style="display:none;"><img src="{{ asset('img/ajax-loader.gif') }}"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div id="send_outer">
                    <button type="submit" class="btn btn-primary" id="send-email">Send</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="AllMailModal" tabindex="-1" role="dialog" aria-labelledby="ApproveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ApproveModalLabel">News Letter Mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="all_mail_alerts" class="alert alert-danger" style="display:none;"></div>
                <div id="all_mail_success" class="alert alert-success" style="display:none;"></div>
                <div class="modal-body">

                    <div class="col-md-12">
                        <label>Subject</label>
                        <input type="text" name="subject" id="all_mail_subject" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label>Message</label>
                        <textarea name="message" id="all_mail_content"class="form-control"></textarea>

                    </div>
                    <div class="col-md-12">
                        <label>File</label>
                        <input type="file" name="mail_file" id="all_mail_file_id" class="form-control">

                    </div>

                </div>
                <div class="modal-footer">
                    <div id="ajax_loader" style="display:none;"><img src="{{ asset('img/ajax-loader.gif') }}"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div id="all_send_outer">
                    <button type="submit" class="btn btn-primary" id="all-send-email">Send</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(document).on('click', '.sendmail_modal', function(e) {
            var email = $(this).attr('data-email');
            $('#recipient_email').val(email);

        });
        $(document).on('click', '#send-email',function(e) {
            var email = $('#recipient_email').val();
            var mail_subject = $('#mail_subject').val();
            var mail_content = $('#mail_content').val()
            var file = document.getElementById('mail_file_id');

            formData = new FormData();
            formData.append('email', email);
            formData.append('subject', mail_subject);
            formData.append('message', mail_content);
            formData.append('file', file.files[0]);
            formData.append("_token", "{{ csrf_token() }}");

            if (mail_subject == '') {
                $("#mail_alerts").html('Enter Your Mail Subject');
                $("#mail_alerts").show().delay(3000).fadeOut();
            } else if (mail_content == '') {
                $("#mail_alerts").html('Enter Your Email Message');
                $("#mail_alerts").show().delay(3000).fadeOut();
            } else {
                outerhtml = $("#send_outer").html();
                $("#send_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');

                $.ajax({
                    url: '{{ route('sent.newsletter') }}',
                    method: "POST",
                    data: formData,
                    enctype: 'multipart/form-data',
                    contentType: false,
                    processData: false,
                    // data: {
                    //     formData,
                    // email: email,
                    // subject: mail_subject,
                    // message: mail_content,
                    // _token: "{{ csrf_token() }}"
                    // },
                    dataType: "json",
                    success: function(data) {
                        if (data.ajax_status == 'success') {
                            $("#mail_subject").val('');
                            $("#mail_content").val('');
                            $("#mail_file_id").val('');

                            $("#mail_success").html(data.message);
                            $("#mail_success").show().delay(3000).fadeOut();
                        } else {
                            $("#mail_alerts").html(data.message);
                            $("#mail_alerts").show().delay(3000).fadeOut();
                        }
                        $("#send_outer").html(outerhtml);
                    }
                });
            }




        });
        $(document).on('click', '#all-send-email',function(e) {
            var mail_subject = $('#all_mail_subject').val();
            var mail_content = $('#all_mail_content').val()
            var file = document.getElementById('all_mail_file_id');

            formData = new FormData();
            formData.append('subject', mail_subject);
            formData.append('message', mail_content);
            formData.append('file', file.files[0]);
            formData.append("_token", "{{ csrf_token() }}");

            if (mail_subject == '') {
                $("#all_mail_alerts").html('Enter Your Mail Subject');
                $("#all_mail_alerts").show().delay(3000).fadeOut();
            } else if (mail_content == '') {
                $("#all_mail_alerts").html('Enter Your Email Message');
                $("#all_mail_alerts").show().delay(3000).fadeOut();
            } else {
                outerhtml = $("#all_send_outer").html();
                $("#all_send_outer").html('<img src="{{ asset('img/ajax-loader.gif') }}" >');

                $.ajax({
                    url: '{{ route('sent.newslettertoAll') }}',
                    method: "POST",
                    data: formData,
                    enctype: 'multipart/form-data',
                    contentType: false,
                    processData: false,
                    // data: {
                    //     formData,
                    // email: email,
                    // subject: mail_subject,
                    // message: mail_content,
                    // _token: "{{ csrf_token() }}"
                    // },
                    dataType: "json",
                    success: function(data) {
                        if (data.ajax_status == 'success') {
                            $("#all_mail_subject").val('');
                            $("#all_mail_content").val('');

                            $("#all_mail_success").html(data.message);
                            $("#all_mail_success").show().delay(3000).fadeOut();
                        } else {
                            $("#all_mail_alerts").html(data.message);
                            $("#all_mail_alerts").show().delay(3000).fadeOut();
                        }
                        $("#all_send_outer").html(outerhtml);
                    }
                });
            }




        });

    </script>
@endsection
