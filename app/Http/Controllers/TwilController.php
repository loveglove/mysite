<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Twilio;
use Input;

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

    	$autoOpen = true;
    	$passMode = false;

		$numberA = '4169851997';
		$numberB = '7057968449';

    	if($autoOpen){

			Twilio::message($numberA, "Someone is buzzing in");
			Twilio::message($numberB, "Someone is buzzing in");
			$response = new VoiceResponse();
			$response->say('Welcome, come on in!');
			$response->play('', ['digits' => '9w9w9w9w9w9w9w99w99']);
			return response($response)->header('Content-Type', 'application/xml');

		}else if($passMode){

			// pass word mode
			$response = new VoiceResponse();
			$gather = $response->gather(['action' => '/apps/operator/password/process',
			    'method' => 'GET']);
			$gather->say('Please enter the passcode, followed by the pound sign');
			$response->say('We didn\'t receive any input. Goodbye!');
			return response($response)->header('Content-Type', 'application/xml');

		}else{
			//default
			return redirect('http://twimlets.com/simulring?PhoneNumbers%5B0%5D='.$numberA.'&PhoneNumbers%5B1%5D='.$numberB.'&Message=%22&');
			// eventually handle this errror and do a direct dial to me as absolute fall back
		}

 	}
 	
 	// process the return function from gathering pass word input from keypad
 	public function processPassword(Request $request)
 	{
		$response = new VoiceResponse();
		$passcode = 555;
		$digits = $request->get('Digits');

		if($passcode == $digits){
			Twilio::message($numberA, "Someone is buzzing in");
			Twilio::message($numberB, "Someone is buzzing in");
			$response = new VoiceResponse();
			$response->say('Correct, come on in!');
			$response->play('', ['digits' => '9w9w9w9w9w9w9w99w99']);

		}else{
			$response->say('Sorry, that is incorrect, please try again.');
		    $response->redirect('/apps/operator/voice/inbound', ['method' => 'GET']);
		}

		return response($response)->header('Content-Type', 'application/xml');

 	}


 	// inbound text from +1 647 930 6035
    public function messageInbound(Request $request)
    {
    	// respond to a text
 	}

}
