<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Twilio;
use Input;
use Auth;

use App\User;
use App\Building;
use App\Contact;
use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;


class TwilController extends Controller
{		

	public function twilTest(Request $request)
	{

		$entry_digit = Auth::user()->building->entry_digit;
		Twilio::message('4169851997', "Wow it really worked!");
		return $entry_digit;
	}
    
    // inbound call from +1 647 930 6035
    public function voiceInbound(Request $request)
    {

		$loftbot_number = substr($request->get('To'), 2);
		$building = Building::where('loftbot_number', $loftbot_number)->first();
		$contacts = $building->contacts;

		switch($building->mode)
		{
			// auto open
			case 2:
				foreach($contacts as $contact){
					if($contact->notify_entry){
						Twilio::message($contact->phone, "Someone is buzzing in");
					}
				}
				$response = new VoiceResponse();
				$response->say($building->entry_message);
				$response->play('', ['digits' => '9w9w9w9w9w9w9w99w99']);
				return response($response)->header('Content-Type', 'application/xml');
			break;

			// pass code
			case 3:
				$response = new VoiceResponse();
				$gather = $response->gather(['action' => '/apps/operator/password/process',
			    'method' => 'GET']);
				$gather->say('Please enter the passcode, followed by the pound sign');
				$response->say('We didn\'t receive any input. Goodbye!');
				return response($response)->header('Content-Type', 'application/xml');
			break;

			// default mode - multi ring
			case 1:
			default:
				$queryString = '';
				$index = 0;
				foreach($contacts as $contact){
					if($contact->multi_dial){
						$queryString .= 'PhoneNumbers%5B'.$index.'%5D='.$contact->phone.'&';
						$index++;
					}
				}
				return redirect('http://twimlets.com/simulring?'.$queryString.'Message=%22&');
			break;

		}

 	}
 	
 	// process the return function from gathering pass word input from keypad
 	public function processPassword(Request $request)
 	{

		$response = new VoiceResponse();
		$digits = $request->get('Digits');
		$loftbot_number = substr($request->get('To'), 2);
		$building = Building::where('loftbot_number', $loftbot_number)->first();
		$contacts = $building->contacts;

		if($digits == $building->entry_code){
			foreach($contacts as $contact){
				if($contact->notify_entry){
					Twilio::message($contact->phone, "Someone is buzzing in");
				}
			}
			$response = new VoiceResponse();
			$response->say('Correct, come on in!');
			$response->play('', ['digits' => '9w9w9w9w9w9w9w99w99']);
		}else{
			$response->say('Sorry, that is incorrect, please try again.');
		    $response->redirect('/apps/operator/voice/inbound', ['method' => 'GET']);
		}
		return response($response)->header('Content-Type', 'application/xml');
 	}


 	// inbound text from +1 647 930 6035 (Set loftbot modes)
    public function messageInbound(Request $request)
    {
    	$inbound_number = $request->get('From');
		$message = $request->get('Body');
		$contact = Contact::where('phone', substr($inbound_number, 2))->first();
		
		if($contact->isAdmin){

			$messageArray = explode(" ", $message);
			$building = $contact->building;

			$command = strtolower($messageArray[0]);

			switch($command){
				
				case "mode":
					if(count($messageArray) > 1){
						// Set the a new mode
						$mode_name = "";
						switch(strtolower($messageArray[1]))
						{
							case "multi":
							case 1:
								$mode_name = "Mode set to: Multi-Number";
								$building->mode = 1;
							break;
							case "auto":
							case 2:
								$mode_name = "Mode set to: Auto-Open";
								$building->mode = 2;
							break;
							case "pass":
							case 3:
								$mode_name = "Mode set to: Pass-Code.\n\rPasscode is: ".$building->entry_code;
								$building->mode = 3;
							break;
							default:
								$mode_name = "Mode ".$messageArray[1]." not recognized. Current mode: ".$building->mode;
							break;
						}
						$building->save();
						Twilio::message($contact->phone, $mode_name);

					}else{
						// Return the current mode
						$mode_name = "";
						switch($building->mode)
						{
							case 1:
								$mode_name = "Current mode is: Multi-Number";
							break;
							case 2:
								$mode_name = "Current mode is: Auto-Open";
							break;
							case 3:
								$mode_name = "Current mode is: Pass-Code";
							break;
							default:
							break;
						}
						Twilio::message($contact->phone, $mode_name);
					}
				break;

				case "passcode":
					if(count($messageArray) > 1){
						//check if passcode entered was comprised of numeric digits
						if(ctype_digit($messageArray[1])){
							$building->entry_code = $messageArray[1];
							$building->save();
							Twilio::message($contact->phone, "Passcode set to: ".$messageArray[1]);
						}else{
							Twilio::message($contact->phone, "Passcode must be only digits 0-9, try again.");
						}
					}else{
						Twilio::message($contact->phone, "Current pascode is: ".$building->entry_code);
					}
				break;

				case "notify":
					if(count($messageArray) > 1){
						switch(strtolower($messageArray[1]))
						{
							case "on":
								$contact->notify_entry = 1;
							break;
							case "off":
								$contact->notify_entry = 0;
							break;
							default:
							break;
						}
						
						$contact->save();
						Twilio::message($contact->phone, "Entry notification is set to: ".($contact->notify_entry ? 'ON' : 'OFF'));

					}else{
						Twilio::message($contact->phone, "Entry notification is set to: ".($contact->notify_entry ? 'ON' : 'OFF'));
					}
				break;

			case "message":
				if(count($messageArray) > 1){
	
					$building->entry_message = substr($message, 8);
					$building->save();
					Twilio::message($contact->phone, "New auto entry message is: ".$building->entry_message);

				}else{
					Twilio::message($contact->phone, "Current auto entry message is: ".$building->entry_message);
				}
			break;
		}

		}else{
			Twilio::message($contact->phone, "Access Denied");
		}

		return response(null, 200)->header('Content-Type', 'application/xml');

 	}


}
