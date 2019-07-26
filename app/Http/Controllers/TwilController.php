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

    	$autoOpen = false;

		$numberA = '4169851997';
		$numberB = '7057968449';

    	if($autoOpen){

			Twilio::message($numberA, "Someone is buzzing in");
			Twilio::message($numberB, "Someone is buzzing in");
			$response = new VoiceResponse();
			$response->say('Welcome, come on in', ['voice' => 'alice', 'language' => 'en-gb']);
			$response->play('', ['digits' => 'w9w9w9w9w9w9w9w9w9ww9ww9ww9']);
			return response($response)->header('Content-Type', 'application/xml');

		}else{

			// $response = new VoiceResponse();
			// $response->dial('4169851997');
			// return response($response)->header('Content-Type', 'application/xml');
			return redirect('http://twimlets.com/simulring?PhoneNumbers%5B0%5D='.$numberA.'&PhoneNumbers%5B1%5D='.$numberB.'&Message=%22&');

		}

 	}
 	

 	// inbound text from +1 647 930 6035
    public function messageInbound(Request $request)
    {
    	// respond to a text
 	}

}
