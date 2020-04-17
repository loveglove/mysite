<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Mail;

class MailController extends Controller
{
  
    //SendGrid Test
    public function sendGridTest(Request $request)
    {   

        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("matt@mattglover.ca", "MG Test");
        $email->setSubject("Sending with SendGrid is Fun");
        $email->addTo("mattjglover@hotmail.com", "Example User");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid("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");
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        return response()->json($response);
    }


}
