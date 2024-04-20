<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Session;
use Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customersupport_chat;
use App\Models\Customersupport_chatmessages;
use Illuminate\Support\Str;

class BotManController extends Controller
{
    protected $email;

    public function handle()
    {
        $botman = app('botman');

        $botman->hears('{message}', function($botman, $message) {
            $message=Str::lower($message);
            if ($message == 'hi' || $message == 'hello') {
                $this->askName($botman);
            } else {
                // $sessionflag = Session::get('chat_flag');
                if(isset($_COOKIE['chat_flag'])){
                    $cookieflag = $_COOKIE['chat_flag'];

                    $chat_userID = Customersupport_chat::find($cookieflag);
                    if($chat_userID){ //--Insert chat message in table--
                        Customersupport_chatmessages::create([
                            'chat_id' => $chat_userID->id,
                            'type' => 'customer',
                            'text_message' => $message,
                            'time' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $botman->reply("write 'hi' to start...");
                    }
                } else {
                    $botman->reply("write 'hi' to start...");
                }
            }
        });

        $botman->listen();
    }

    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $cls = $this;
        $name = '';
        $botman->ask('Hello! What is your Name?', function(Answer $answer) use ($cls, $name, $botman){

            $name = $answer->getText();

            $this->say('Nice to meet you '.$name);

            //--Email verification--
            // $cls->askEmail($botman, $name, $cls);

            $this->ask('Please give us your mail id to continue.', function(Answer $answer) use ($name){
                $this->email = $answer->getText();

                if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                    $this->say('Thank you for reply us. Your mail id is valid.');

                    $this->say("Hi ". $name.". Our customer support excecutive will be connect in chat very soon. please hold on.");

                    $ChatID = Customersupport_chat::create([
                                'customer_name' => $name,
                                'customer_email' => $this->email,
                                'subject' => 'General',
                                'time' => date('Y-m-d H:i:s'),
                                'status' => 'active'
                            ])->id;

                    if($ChatID){
                        //--Create a session variable flag when connect to customer support--
                        // Session::put('chat_flag', $ChatID);

                        //set cookie
                        setcookie('chat_flag', $ChatID, time() + 1200, "/"); //cookie for 60 seconds validity--

                        //get cookie
                        // if(isset($_COOKIE['chat_flag'])){
                        //     $cook = $_COOKIE['chat_flag'];
                        // }
                    }
                } else {
                    $this->say("Please enter your valid email id. Say 'hi' to continue.");
                }

            });

        });
    }

    public function sendmessage(Request $request){
        if($request->chatID != 0){
            $chatdetails = Customersupport_chat::find($request->chatID);

            if($chatdetails){
                if($chatdetails->status != 'disabled'){
                    $chatmsgID = Customersupport_chatmessages::create([
                        'chat_id' => $request->chatID,
                        'type' => 'customer',
                        'text_message' => $request->message,
                        'time' => date('Y-m-d H:i:s')
                    ])->id;

                    if($chatmsgID){
                        setcookie('chat_flag', $request->chatID, time() + (60 * 2), "/"); //cookie for 2 minutes validity--

                        $returnArray['result'] = 'success';
                        $returnArray['chatmsgID'] = $chatmsgID;
                        $returnArray['last_message'] = $request->message;
                    } else {
                        $returnArray['result'] = 'failed';
                        $returnArray['message'] = 'sorry... chat send failed. Something went wrong!';
                    }
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'sorry... your chat ended.';
                }
            } else {
                $returnArray['result'] = 'failed';
                $returnArray['message'] = 'Chat details not found';
            }
        } else {
            $returnArray['result'] = 'failed';
            $returnArray['message'] = 'Chat id is not valid';
        }
        return response()->json($returnArray);
    }

    public function load_message(Request $request){
        if($request->chatID != 0){
            $customerchat = Customersupport_chat::where('id', $request->chatID)->where('status', 'attend')->first();
            if($customerchat){
                $chat_messages = Customersupport_chatmessages::join('customersupport_chats as CSC', 'CSC.id', 'customersupport_chatmessages.chat_id')
                                ->select('customersupport_chatmessages.*', 'CSC.customer_name')
                                ->where('chat_id', $request->chatID);

                                if($request->chatmsg_id != '' && $request->chatmsg_id != 0){
                                    $chat_messages->where('customersupport_chatmessages.id', '>', $request->chatmsg_id);
                                }

                $chat_messages = $chat_messages->get();

                $returnArray['result'] = 'success';
                $returnArray['chat_id'] = $request->chatID;
                $returnArray['chat_messages'] = $chat_messages;

                return response()->json($returnArray);
            }
        }
    }

    // public function askEmail($botman, $name, $cls)
    // {
    //     $botman->ask('Please give us your mail id to continue.', function(Answer $answer) use ($name){
    //         $this->email = $answer->getText();

    //         if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
    //             $this->say('Thank you for reply us. Your mail id is valid.');

    //             $this->say("Hi ". $name.". Our customer support excecutive will be connect in chat very soon. please hold on.");

    //             $ChatID = Customersupport_chat::create([
    //                         'customer_name' => $name,
    //                         'customer_email' => $this->email,
    //                         'subject' => 'General',
    //                         'time' => date('Y-m-d H:i:s'),
    //                         'status' => 'active'
    //                     ])->id;

    //             if($ChatID){
    //                 //--Create a session variable flag when connect to customer support--
    //                 Session::put('chat_flag', $ChatID);
    //                 $i = 1;
    //             }
    //         } else {
    //             $this->say("Please enter your valid email id. So that you can continue chat.");
    //         }

    //     });
    // }

}
