<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use Request;
use Mail;

class PublicController extends Controller
{


    /**
        Send email message     *
     * @return \Illuminate\Http\Response
     */
    public function contact(Request $request)
    {
        $data = [
           'email' => Request::input('con_email'),
           'name' => Request::input('con_name'),
           'text' => Request::input('con_message'),
           'sendto' => 'mattjglover@hotmail.com'
        ];

        // check recaptcha status
        if(Request::has('g-recaptcha-response')){
            
            $captcha = Request::input('g-recaptcha-response');
            $rspn = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcSoJ8UAAAAAEDdFUBBK4LMnWSWykPr_hNKyP0F&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $g_response = json_decode($rspn);

            if(!$g_response->success===true){; 
                return response()->json(['success' => false, 'message' => "Captcha not verified"]);
            }
        }else{
            return response()->json(['success' => false, 'message' => "No Captcha found"]);
        }


        Mail::send('emails.contact', $data, function($message) use ($data) {
            $message->to($data['sendto']);
            $message->subject('New message from MattGlover.ca');
        });

        return response()->json(['success' => true, 'message' => "Email Sent!"]);

    }

}
