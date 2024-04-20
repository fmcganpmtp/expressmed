<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\Newsletter;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {

        $newsletters = Newsletter::select('newsletters.*');

        if ($request->has('search_keyword') && $request->search_keyword != '') {
            $newsletters->where('products.email', 'LIKE', '%' . $request->search_keyword . '%');
        }

        $newsletters = $newsletters->latest()->paginate(30);

        return view('admin.newsletter.index', compact('newsletters'))->with('i', (request()->input('page', 1) - 1) * 30);
    }
    public function sentNewsletterMail(Request $request)
    {
        $email = $request->email;
        $subject = $request->subject;
        $message = $request->message;
        $fileName = '';

        if ($request->file('file')) {
            $fileName = time() . '.' . $request->file->extension();

            $request->file->move(public_path('/assets/uploads/news_letter/'), $fileName);
        }

        if ($email == '') {
            $ajax_status = 'error';
            $message = 'Enter Your Email Address';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ajax_status = 'error';
            $message = 'Enter A Valid Email Address';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        } else {
            $settings = Generalsetting::where('item', 'notification_email')->get()->first();
            if ($settings && $settings->value != '') {
                Mail::send('email.newsletterMail',
                    array(
                        'name' => $request->fullname,
                        'email' => $request->email,
                        'subject' => $request->subject,
                        'comment' => $request->message,
                    ), function ($message) use ($request, $settings, $fileName) {
                        $message->from($settings->value);
                        $message->to($request->email);
                        $message->subject($request->subject);
                        if ($request->file('file')) {
                            $message->attach(public_path('/assets/uploads/news_letter/' . $fileName));
                        }
                    });
            }
            $message = "Mail sent successfully";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
        return response()->json($return_array);
    }
    public function sentNewsletterMailtoAll(Request $request)
    {

        $subject = $request->subject;
        $message = $request->message;
        $fileName = '';

        if ($request->file('file')) {
            $fileName = time() . '.' . $request->file->extension();

            $request->file->move(public_path('/assets/uploads/news_letter/'), $fileName);
        }
        $subscriptionsmails = Newsletter::where('status', 'subscribed')->get();
        if (count($subscriptionsmails) < 0) {
            $ajax_status = 'error';
            $message = 'No subscribed emails available';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);

        } else {
            $settings = Generalsetting::where('item', 'notification_email')->get()->first();
            $subscriptionsmails = Newsletter::where('status', 'subscribed')->get();
            if ($settings && $settings->value != '') {
                foreach ($subscriptionsmails as $subscriptions) {
                    Mail::send('email.newsletterMail',
                        array(
                            'name' => $request->fullname,
                            'email' => $subscriptions->email_id,
                            'subject' => $request->subject,
                            'comment' => $request->message,
                        ), function ($message) use ($request, $settings, $subscriptions, $fileName) {
                            $message->from($settings->value);
                            $message->to($subscriptions->email_id);
                            $message->subject($request->subject);
                            if ($request->file('file')) {
                                $message->attach(public_path('/assets/uploads/news_letter/' . $fileName));
                            }});
                }
                $message = "Mail successfully send to All Subscribed Users ";
                $ajax_status = 'success';
                $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                return response()->json($return_array);

            }
            $message = "From Mail Not Set Yet";
            $ajax_status = 'error';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);

        }
        return response()->json($return_array);
    }

}
