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
use App\Log;

use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;

use GuzzleHttp\Client;
use Exception;



class FlybitsController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('flybits');

    }


    /************************************************************
     *** Flybits SendGrid test via Demo App w/ context values ***
     ************************************************************/ 

    public function flybitsEmail(Request $request)
    {   
        // 1) Get array of context data payload from the webhook request
        $data = $request->all();

        // 2) Create SendGrid Mail Object populate with payload values
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("matt.glover@flybits.com", $data["from"]);
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



    public function flybitsEmail2(Request $request)
    {   
        // 1) Get array of context data payload from the webhook request
        $requestdata = $request->all();

        $data = [
           'to' => $requestdata["email"],
           'from' => $requestdata["from"],
           'subject' => $requestdata["subject"],
           'body' => $requestdata["body"],
        ];


        Mail::send([], $data, function($message) use ($data) {
            $message->to($data['to']);
            $message->subject($data['subject']);
            $message->setBody('<h1>Hi! </h1><br>'.$data["body"], 'text/html'); 
        });

        return response()->noContent();
    }





    /********************************************
     *** Flybits SendGrid test via CSV w/ IDR ***
     ********************************************/ 

    public function flybitsEmailCSV(Request $request)
    {   
        // Map of proxyID to email address
        $PII = array(
            // "6d32b2e2-d949-44a8-a117-87bf6c7010b6" => "Sebastian.Lozano@mastercard.com",
            // "26583a23-b13a-4e95-9a01-d316d86fd78b" => "Hernan.PardinasOsadchuk@mastercard.com",
            "7a2f3575-abda-4c93-8860-0a668c9bd5ad" => "matt.glover@flybits.com",
            "43989ad6-3e7e-4695-b13d-65a06c262d63" => "jason.davies@flybits.com"
        );

        // 1) Get context data payload from the webhook request
        $data = $request->all();

        // 2) Look up email from PII array where Proxy ID matches
        $sendto = "";
        if(isset($PII[$data["email"]])){
            $sendto = $PII[$data["email"]];
        }

        // 3) If match found, create SendGrid email object and populate with payload values
        if(!empty($sendto)){
            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom("matt.glover@flybits.com", "Flybits Demo");
            $email->setSubject("Been to the ATM recently?");
            $email->addTo($sendto, "Recipient");
            $email->addContent(
                "text/html", "Hello, <br><br>You visited the ATM <strong>".$data["visits"]."</strong> times in the past week...<br><br> Did you know you can earn rewards using your debit card instead? <br>Learn More... <br><br><img src='https://www.flybits.com/wp-content/uploads/2018/04/flybits-logo-RGB.png' width='200' alt='Flybits Logo' />"
            );

            // 4) Create a connection to SendGrid with customer API Key
            $sendgrid = new \SendGrid("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");

            // 5) Send the new email object via SendGrid
            $sendgrid->send($email);

            // Log::create(['building_id' => 1, 'type' => 'Flybits Webhook', 'data' => $sendto]);
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

    
    public function flybitsCreateEngagement(Request $request)
    {


        $validatedData = $request->validate([
            'jwt' => 'required|string',
            'host' => 'required|string',
            'id1' => 'required|string'
        ]);

        $request->flash();
        $reqdata = $request->all();

        $jwt = $reqdata["jwt"];
        $host = $reqdata["host"];

        // $jwt = "eyJhbGciOiJIUzI1NiIsImtpZCI6IjVCRDlEQjg5LTJCMkQtNEZCNC1BNUM0LUJEOEVBQTZENDQ5RCIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2MDI2MjQ5NzMsIm5iZiI6MTU5NzQ0MDk3MywidXNlcklEIjoiNEQ2RDFCOUEtMzQxMi00MUFGLUI5NjEtOTk5NUY4QkU5MzhEIiwiZGV2aWNlSUQiOiI0M0Q4QjQxMy0yNDkzLTQ3NzMtOTk3My0yRjU0OUEyRjAzMTYiLCJ0ZW5hbnRJRCI6IjVCRDlEQjg5LTJCMkQtNEZCNC1BNUM0LUJEOEVBQTZENDQ5RCIsImlzU0EiOmZhbHNlfQ.KOQA7ZCk75bl7WyaLMWIy7G_WCTkvDL8W3pMrgt6Xhc";
        // $host = "https://v3.flybits.com";
        // $id1 = "772493DC-F4D7-42D0-82B9-DF142CE7EA04";
        // $id2 = "945CB2C5-433C-499A-A489-8934E0076CA8";
        // $id3 = "AFFF00F8-00C6-471B-9CC3-BA4016F34310";

        $data = array();
        if(!empty($reqdata["id1"])){
            array_push($data, ["id" => $reqdata["id1"], "name" => $reqdata["name1"], "states" => $reqdata["states1"]]);
        }
        if(!empty($reqdata["id2"])){
            array_push($data, ["id" => $reqdata["id2"], "name" => $reqdata["name2"], "states" => $reqdata["states2"]]);
        }
        if(!empty($reqdata["id3"])){
            array_push($data, ["id" => $reqdata["id3"], "name" => $reqdata["name3"], "states" => $reqdata["states3"]]);
        }
        if(!empty($reqdata["id4"])){
            array_push($data, ["id" => $reqdata["id4"], "name" => $reqdata["name4"], "states" => $reqdata["states4"]]);
        }
        if(!empty($reqdata["id5"])){
            array_push($data, ["id" => $reqdata["id5"], "name" => $reqdata["name5"], "states" => $reqdata["states5"]]);
        }
        if(!empty($reqdata["id6"])){
            array_push($data, ["id" => $reqdata["id6"], "name" => $reqdata["name6"], "states" => $reqdata["states6"]]);
        }
        if(!empty($reqdata["id7"])){
            array_push($data, ["id" => $reqdata["id7"], "name" => $reqdata["name7"], "states" => $reqdata["states7"]]);
        }
        if(!empty($reqdata["id8"])){
            array_push($data, ["id" => $reqdata["id8"], "name" => $reqdata["name8"], "states" => $reqdata["states8"]]);
        }
        if(!empty($reqdata["id9"])){
            array_push($data, ["id" => $reqdata["id9"], "name" => $reqdata["name9"], "states" => $reqdata["states9"]]);
        }
        if(!empty($reqdata["id10"])){
            array_push($data, ["id" => $reqdata["id10"], "name" => $reqdata["name10"], "states" => $reqdata["states10"]]);
        }

        $results = array();

        foreach($data as $d){
            switch($d["states"])
            {
                case 0:
                    $name = "Not Eng - ".$d["name"];
                    $status = $this->fbEngageRuleAPI($d["id"], $name, "false", $jwt, $host);
                    $label = $status["status"] ? "Success, rule created with name: " : "Failed, could not create rule: ";
                    array_push($results, ["status" => $status["status"], "message" => $label.$name, "response" => $status["response"]]);
                    sleep(2);
                break;

                case 1:
                    $name = "Has Eng - ".$d["name"];
                    $status = $this->fbEngageRuleAPI($d["id"], $name, "true", $jwt, $host);
                    $label = $status["status"] ? "Success, rule created with name: " : "Failed, could not create rule: ";
                    array_push($results, ["status" => $status["status"], "message" => $label.$name, "response" => $status["response"]]);
                    sleep(2);
                break;

                case 2;
                    $name = "Not Eng - ".$d["name"];
                    $status = $this->fbEngageRuleAPI($d["id"], $name, "false", $jwt, $host);
                    $label = $status["status"] ? "Success, rule created with name: " : "Failed, could not create rule: ";
                    array_push($results, ["status" => $status["status"], "message" => $label.$name, "response" => $status["response"]]);
                    sleep(2);
                    $name = "Has Eng - ".$d["name"];
                    $status = $this->fbEngageRuleAPI($d["id"], $name, "true", $jwt, $host);
                    $label = $status["status"] ? "Success, rule created with name: " : "Failed, could not create rule: ";
                    array_push($results, ["status" => $status["status"], "message" => $label.$name, "response" => $status["response"]]);
                    sleep(2);
                break;
            }
        }

        return json_encode($results);

    }


    public function fbEngageRuleAPI($contentID, $ruleName, $state, $jwt, $host)
    {

        $path = "/context/rules";
        $url = $host.$path;

        $headers = [
            'Content-Type' => 'application/json',
            'X-Authorization' => $jwt,
        ];

        $ts = time();
        $rn = preg_replace('/\s+/', '_', $ruleName);

        $body = [
            "name" => $ruleName,
            "scope" => "tenant",
            "stringRepresentation" => "".$rn."() :- (boolEq(ctx.flybits.contentDeviceAnalytics.query.engaged.".$contentID.",".$state."))"
        ];

        try {

            $client = new Client;
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            return [
                "status" => true,
                "response" => json_decode($response->getBody()->getContents(), true)
            ];

        } catch (Exception $e) {

            return [
                "status" => false,
                "response" => $e->getResponse()->getBody()->getContents()
            ];
        }

        throw new Exception($e->getResponse()->getBody()->getContents());

    }


    public function flybitsSetProject(Request $request)
    {

        $request->flash();
        $reqdata = $request->all();

        $email = $reqdata["email"];
        $pass = $reqdata["password"];
        $project = $reqdata["project"];
        $host = $reqdata["host"];

        $result1 = $this->flybitsSignInAPI($email, $pass, $project, $host);

        if($result1["status"]){
            sleep(2);
            $result2 = $this->flybitsGetContentAPI($result1["jwt"], $host);
            
            return [
                "status" => true,
                "content" => $result2["content"],
                "jwt" => $result1["jwt"],
                "rawData" => $result2["rawData"]
            ];


        }else{
            return [
                "status" => false,
                "error" => "Error logging into project",
                "response" => $result1["response"]
            ];
        }
        return $result;

    }



    public function flybitsSignInAPI($email, $pass, $project, $host)
    {

        $path = "/sso/auth/authenticate";
        $url = $host.$path;

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = [
            "email" => $email,
            "password" => $pass,
            "projectId" => $project
        ];

        try {

            $client = new Client;
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            $headers = $response->getHeaders();

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                "status" => true,
                "content" => $data,
                "headers" => $headers,
                "jwt" => $headers["X-Authorization"][0]
            ];

        } catch (Exception $e) {

            return [
                "status" => false,
                "response" => $e->getMessage()
            ];
        }

        throw new Exception($e->getResponse()->getBody()->getContents());
       
    }


    public function flybitsGetContentAPI($jwt, $host)
    {

        $path = "/kernel/content/instances?limit=200";
        $url = $host.$path;

        $headers = [
            'Content-Type' => 'application/json',
            'X-Authorization' => $jwt,
        ];

        try {

            $client = new Client;
            $response = $client->get($url, [
                'headers' => $headers,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // dd($data);

            $contentList = array();

            foreach($data["data"] as $d){
               array_push($contentList, ["id" => $d["id"], "name" => $d["localizations"]["en"]["name"], "type" => $d["templateType"]]);
            }

            // dd($contentList);

            return [
                "status" => true,
                "content" => $contentList,
                "rawData" => $data
            ];

        } catch (Exception $e) {

            return [
                "status" => false,
                "response" => $e->getMessage()
            ];
        }

        throw new Exception($e->getResponse()->getBody()->getContents());

    }




}
