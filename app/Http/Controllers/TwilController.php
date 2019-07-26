<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Twilio;

use App\User;
use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;


class TwilController extends Controller
{		

	public function twilTest(Request $request)
	{
		Twilio::message('4169851997', "Wow it really worked!");
		return "Text message sent";
	}
    
    // inbound call from +1 647 930 6035
    public function voiceInbound(Request $request)
    {
		// Start our TwiML response
		// $response = new TwiML;
		// Read a message aloud to the caller
		// $response->say(
		//     "Thank you for calling! Have a great day.", 
		//     array("voice" => "alice")
		// );


		$response = new VoiceResponse();
		$response->say('Welcome');
		$response->play('', ['digits' => 'w9ww9ww9ww9ww9ww9ww9ww9ww9ww9']);

		return response($response)->header('Content-Type', 'application/xml');

 	}
 	

 	// inbound text from +1 647 930 6035
    public function messageInbound(Request $request)
    {

 	}

}
