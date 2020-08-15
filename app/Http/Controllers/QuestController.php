<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Twilio;
use Input;
use Auth;
use Carbon\Carbon;

use App\User;
use App\Log;
use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;

class QuestController extends Controller
{


    // show the admin page
    public function admin()
    {
        return view('quest.admin');
    }

    // show the compass page
    public function compass()
    {
        return view('quest.compass');
    }


    //send an SMS message 
    public function sendSMS(Request $request)
    {   
        $phone = "7057968449";
        $data = $request->all();
		Twilio::message("6475566309",$phone, $data["text"]); // from, to, message

		// Log::create([
		// 	'building_id' => 1,
        //     'type' => 'Door Entry',
        //     'data' => 2,
		// ]);
		
		return response()->noContent();
    }
    


}
