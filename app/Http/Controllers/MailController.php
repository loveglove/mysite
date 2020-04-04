<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Mail;

class MailController extends Controller
{
  
    // FLYBITS SendGrid Test
    public function flybitsEmail(Request $request)
    {   
        // get array of context data payload
        $data = $request->all();

        // Send Grip Web API
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("no-reply@example.com", $data["from"]);
        $email->setSubject($data["subject"]);
        $email->addTo($data["email"], "Demo User");
        $email->addContent("text/plain", $data["body"]);
        $email->addContent(
            "text/html", "<strong>".$data["body"]."</strong><br><br><img src='https://www.flybits.com/wp-content/uploads/2018/04/flybits-logo-RGB.png' width='200' alt='Flybits Logo' />"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        return response()->json($data);
    }

}
