<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Session;
use Auth;
// use Cookie;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Customersupport_chat;
use App\Models\Customersupport_chatmessages;

class AdminBotManController extends Controller
{
    public function handle(Request $request)
    {
        //--Save admin chat --
        Customersupport_chatmessages::create([
            'chat_id' => $request->chatID,
            'type' => 'executive',
            'text_message' => $request->message
        ]);

        $adminbotman = app('botman');

        $adminbotman->hears('{message}', function($adminbotman, $message) {
            // dd($message);
            // Customersupport_chatmessages::create([
            //     'chat_id' => $sessionflag,
            //     'type' => 'customer',
            //     'text_message' => $message
            // ]);
            // $adminbotman->reply("admin botman reply.");
        });

        $adminbotman->listen();
    }

    public function sendmessage(Request $request){
        if($request->chatID != 0){
            $chatdetails = Customersupport_chat::find($request->chatID);
            if($chatdetails){
                $chatmsgID = Customersupport_chatmessages::create([
                                'chat_id' => $request->chatID,
                                'type' => 'executive',
                                'text_message' => $request->message,
                                'time' => date('Y-m-d H:i:s')
                            ])->id;

                if($chatmsgID){
                    $returnArray['result'] = 'success';
                    $returnArray['chatmsgID'] = $chatmsgID;
                    $returnArray['last_message'] = $request->message;
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'sorry... chat send failed. Something went wrong!';
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
}
