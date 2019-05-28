<?php

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

if($_POST)
{
    // Verify Mail input Fields
    $user_name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $user_email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $user_message = filter_var($_POST["message"], FILTER_SANITIZE_STRING);
    if(empty($user_name)) {
		$empty[] = "<b>Name</b>";		
	}
	if(empty($user_email)) {
		$empty[] = "<b>Email</b>";
	}
	if(empty($user_message)) {
		$empty[] = "<b>Message</b>";
	}	
	if(!empty($empty)) {
		$output = json_encode(array('type'=>'error', 'text' => implode(", ",$empty) . ' Required!'));
        die($output);
	}
	if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){ //email validation
	    $output = json_encode(array('type'=>'error', 'text' => '<b>'.$user_email.'</b> is an invalid Email, please correct it.'));
		die($output);
	}
	

	// check recaptcha status
	if(isset($_POST['g-recaptcha-response'])){
   		
   		$captcha = $_POST['g-recaptcha-response'];
    	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lcip14UAAAAANl1uPxDaDzgCEtvKlEzrs4OlD00&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		$g_response = json_decode($response);

		if(!$g_response->success===true){; 
			$output = json_encode(array('type'=>'error', 'text' => 'reCAPTCHA failed, please try again'));
			die($output);
		}
	}else{
		$output = json_encode(array('type'=>'error', 'text' => 'Please confirm the reCAPTCHA'));
		die($output);
	}


	// reCAPTCHA OK now send mail
    $message = "<div style='color:black;'><h2>New Message from N<span style='color:red;'>X</span>M Website</h2><p><b><i>From: </i></b> ".$_POST['name']."<br/></p><p><b><i>Reply to E-mail: </i></b> ".$_POST['email']."</p><p><b><i>Message: </i></b> ".$_POST['message']."</p></div>";

	// create mail object
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->Username = 'no-reply@nxmlabs.com';
    $mail->Password = 'NXMLabs2019';
    $mail->setFrom('no-reply@nxmlabs.com', 'NXM Website');
    $mail->addReplyTo($_POST['email']);
    $mail->addAddress('info@nxmlabs.com');
    $mail->Subject = 'NXM Website Message';
    $mail->msgHTML($message);
    $mail->AltBody = 'There is a new message from the NXM Website';

    // send mail
    if (!$mail->send()) {
        die(json_encode(array('type' => 'error', 'text' => "Error sending mail please try again", 'error' => $mail->ErrorInfo)));
    } else {
        die(json_encode(array('type' => 'success', 'text' => "Message sent! we will get back to you shortly")));
    }
}else{
	die(json_encode(array('type' => 'warning', 'text' => "No post data found in request")));
}
?>