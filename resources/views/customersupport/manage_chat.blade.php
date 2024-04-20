@extends('layouts.customersupport.customersupport')
@section('content')
    <div id="alert_message" class="alert alert-danger" style="display: none">
        <p></p>
    </div>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Customer Support</h1>

        </div>
        <div class="row">
            <div class="col-md-8">
                <!-- Default Card Example -->
                <div class="card mb-4">
                    <div class="card-header" id="conversation_title">Conversation {{ !empty($attended_chat) ? 'with ' . $attended_chat->customer_name : '' }}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12" style="overflow-y: scroll; height:300px">
                                <div class="form-group" id="chat_block">
                                    @if (!empty($chat_messages))
                                        @foreach ($chat_messages as $chat_messagesRow)
                                            @if ($chat_messagesRow->type == 'customer')
                                                <span class="cust_msg" data-chatid="{{ $chat_messagesRow->chat_id }}" data-id="{{ $chat_messagesRow->id }}">{{ $chat_messagesRow->customer_name . ' : ' . $chat_messagesRow->text_message }}</span><br>
                                            @elseif($chat_messagesRow->type == 'executive')
                                                <span class="float-right admin_msg" data-chatid="{{ $chat_messagesRow->chat_id }}" data-id="{{ $chat_messagesRow->id }}">{{ 'me : ' . $chat_messagesRow->text_message }}</span><br>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="textmessage"><strong>Messsage:</strong></label>
                                    <textarea id="textmessage" placeholder="Type message here..." class="form-control" rows="4" autofocus></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <button type="button" id="send_msg" class="btn btn-primary" value="{{ !empty($attended_chat) ? $attended_chat->id : 0 }}" {{ !empty($attended_chat) ? '' : 'disabled' }}><i class="fa fa-paper-plane"></i> Send</button>
                                <button type="button" id="chat_disconnect" class="btn btn-danger" value="{{ !empty($attended_chat) ? $attended_chat->id : 0 }}" {{ !empty($attended_chat) ? '' : 'disabled' }}>Disconnect</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body" style="overflow-y: scroll; height:600px">
                        @if (!empty($attended_chat))
                            <label><strong>Attended Chat</strong></label>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>User</th>
                                        <th>Subject</th>
                                        <th>Time</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $attended_chat->customer_name }}</td>
                                        <td>{{ $attended_chat->subject }}</td>
                                        <td>{{ $attended_chat->time }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif

                        <label><strong>Available Chats</strong></label>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Subject</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                                @if ($available_Chats->isNotEmpty())
                                    @foreach ($available_Chats as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->customer_name }}</td>
                                            <td>{{ $value->subject }}</td>
                                            <td>{{ $value->time }}</td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm attend_chat" value="{{ $value->id }}">Attend</button>
                                            </td>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center"><strong>No active chats found</strong></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footer_scripts')
    {{-- <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script> --}}

    <script>
        setInterval(load_message, 8000); //--call ajax function every 5 seconds--

        // var botmanWidget = {
        //     chatServer: "/adminbotman",
        //     introMessage: '✋ Hi! Welcome to ShopeOn customer service. How may i help you? say hi',
        //     title: 'ShopeOn Customer Support',
        //     mainColor: '#00bbb5',
        //     bubbleBackground: '#038681',
        //     aboutText: '',
        //     bubbleAvatarUrl: '',
        // };

        $(document).on('click', '#send_msg', function() {
            var chatID = $(this).val();
            var message = $.trim($('#textmessage').val());
            var html = '';
            if (message != '') {
                if (chatID != 0) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '{{ route('admin.chat.send') }}',
                        data: {
                            chatID: chatID,
                            message: message,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.result == 'success') {
                                $('#textmessage').val('');
                                html = '<span class="float-right admin_msg" data-chatid="' + chatID + '" data-id="' + response.chatmsgID + '"> me : ' + response.last_message + '</span><br>';
                                $('#chat_block').append(html);
                            } else {
                                $('#alert_message').show().find('p').text(response.message);
                                $('#alert_message').delay(1000).fadeOut();
                            }
                        }
                    });
                } else {
                    $('#alert_message').show().find('p').text('Chat id not found.');
                    $('#alert_message').delay(1000).fadeOut();
                }
            }
        });

        $(document).on('click', '#chat_disconnect', function(e){
            var chatID = e.target.value;
            if(chatID != 0){
                $.ajax({
                    type: 'POST',
                    // dataType: 'json',
                    url: '{{ route('admin.chat.disconnect') }}',
                    data: {
                        chatID: chatID,
                        '_token': '{{ csrf_token() }}'
                    },
                    success:function(response){
                        if(response.result == 'success'){
                            alert(response.message);

                            $('#conversation_title').text('Conversation');
                            $('#chat_block').html('');

                            $('#send_msg').val(0);
                            $('#send_msg').attr('disabled', 'true');
                            $('#chat_disconnect').val(0);
                            $('#chat_disconnect').attr('disabled', 'true');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.attend_chat', function() {
            var btn = $(this);
            var chatID = btn.val();
            var html = '';

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ route('customersupport.chat.attend') }}',
                data: {
                    chatID: chatID,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.result == 'success') {
                        $('#send_msg').val(chatID);
                        $('#send_msg').removeAttr('disabled', '');

                        $('#chat_disconnect').val(chatID);
                        $('#chat_disconnect').removeAttr('disabled', '');

                        if (response.chat_messages != '') {
                            jQuery.each(response.chat_messages, function(i, val) {
                                if (val.type == 'customer') {
                                    html += '<span class="cust_msg" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">' + val.customer_name + ' : ' + val.text_message + '</span><br>';
                                }
                                if (val.type == 'executive') {
                                    html += '<span class="float-right admin_msg" data-chatid="' + val.chat_id + '" data-id="' + val.id + '"> me : ' + val.text_message + '</span><br>';
                                }
                            });
                            $('#chat_block').html(html);
                        }
                        $('#conversation_title').text('Conversation with ' + response.customername);
                        btn.attr('disabled', 'true');
                    } else {
                        $('#alert_message').show().find('p').text(response.message);
                        $('#alert_message').delay(1000).fadeOut();
                    }
                }
            });
        });

        function load_message() {
            var chatID = $('#send_msg').val();
            var html = '';
            var chatmsg_custid = (typeof($('.cust_msg').last().attr('data-id')) != "undefined" && $('.cust_msg').last().attr('data-id') != null) ? $('.cust_msg').last().attr('data-id') : 0;
            var chatmsg_adminid = (typeof($('.admin_msg').last().attr('data-id')) != "undefined" && $('.admin_msg').last().attr('data-id') != null)  ? $('.admin_msg').last().attr('data-id') : 0;

            var chatmsg_id = (parseInt(chatmsg_custid) > parseInt(chatmsg_adminid) ? parseInt(chatmsg_custid) : parseInt(chatmsg_adminid));
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ route('admin.chat.load') }}',
                data: {
                    chatID: chatID,
                    chatmsg_id: chatmsg_id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.result == 'success') {
                        if (response.chat_messages != '') {
                            jQuery.each(response.chat_messages, function(i, val) {
                                if (val.type == 'customer') {
                                    html += '<span class="cust_msg" data-chatid="' + val.chat_id + '" data-id="' + val.id + '">' + val.customer_name + ' : ' + val.text_message + '</span><br>';
                                }
                                if (val.type == 'executive') {
                                    html += '<span class="float-right admin_msg" data-chatid="' + val.chat_id + '" data-id="' + val.id + '"> me : ' + val.text_message + '</span><br>';
                                }
                            });
                            $('#chat_block').append(html);
                        }
                    } else {
                        // $('#alert_message').show().find('p').text(response.message);
                        // $('#alert_message').delay(1000).fadeOut();
                    }
                }
            });
        }
    </script>
@endsection
