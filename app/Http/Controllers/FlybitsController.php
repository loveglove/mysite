<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Mail;
use Twilio;
use Input;
use Auth;
use Storage;
use Carbon\Carbon;

use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;


class FlybitsController extends Controller
{

    /************************************************************
     *** Flybits SendGrid test via Demo App w/ context values ***
     ************************************************************/ 

    public function flybitsEmail(Request $request)
    {   
        // 1) Get array of context data payload from the webhook request
        $data = $request->all();

        // 2) Create SendGrid Mail Object populate with payload values
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("no-reply@example.com", $data["from"]);
        $email->setSubject($data["subject"]);
        $email->addTo($data["email"], "Demo User");
        $email->addContent(
            "text/html", "<strong>".$data["body"]."</strong><br><br><img src='https://www.flybits.com/wp-content/uploads/2018/04/flybits-logo-RGB.png' width='200' alt='Flybits Logo' />"
        );

        // 3) Create a connection to SendGrid with customer API Key
        $sendgrid = new \SendGrid("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");

        // 4) Send the new email object via SendGrid
        $sendgrid->send($email);

        // 5) Respond to Webhook request (nothing to return)
        return response()->noContent();
    }





    /********************************************
     *** Flybits SendGrid test via CSV w/ IDR ***
     ********************************************/ 

    public function flybitsEmailCSV(Request $request)
    {   
        // Map of proxyID to email address
        $PII = array(
            "6d32b2e2-d949-44a8-a117-87bf6c7010b6" => "mattjglover@hotmail.com",
            "26583a23-b13a-4e95-9a01-d316d86fd78b" => "matt.glover@flybits.com",
            "7a2f3575-abda-4c93-8860-0a668c9bd5ad" => "sigifredo.ochoa@flybits.com",
            "43989ad6-3e7e-4695-b13d-65a06c262d63" => "jason.davies@flybits.com"
        );

        // 1) Get array of context data payload from the webhook request
        $data = $request->all();

        // 2) Look up email from PII array where Proxy ID matches
        $sendto = "";
        if(isset($PII[$data["email"]])){
            $sendto = $PII[$data["email"]];
        }

        // 3) If match found, create SendGrid email object and populate with payload values
        if(!empty($sendto)){
            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom("no-reply@example.com", "Flybits");
            $email->setSubject("Been to the ATM recently?");
            $email->addTo($sendto, "Recipient");
            $email->addContent(
                "text/html", "Hello, <br><br>You visited the ATM <strong>".$data["visits"]."</strong> times in the past week...<br><br> Did you know you can earn rewards using your debit card instead? <br>Learn More... <br><br><img src='https://www.flybits.com/wp-content/uploads/2018/04/flybits-logo-RGB.png' width='200' alt='Flybits Logo' />"
            );

            // 4) Create a connection to SendGrid with customer API Key
            $sendgrid = new \SendGrid("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");

            // 5) Send the new email object via SendGrid
            $sendgrid->send($email);
        }

        // 6) Respond to Webhook request (nothing to return)
        return response()->noContent();
    }



 	/****************************************************************
     *** Flybits SMS (Twilio) test via Demo App w/ context values ***
     ****************************************************************/ 

    public function flybitsSMS(Request $request)
    {
        // 1) Get all data from webhook payload
        $data = $request->all();
        
        // 2) Send SMS to recipient phone number with payload text message
        Twilio::message('6475566309', $data["phone"], $data["text"]);
        
        // 3) Respond to webhook request (nothing to return)
        return response()->noContent();
    }


}
