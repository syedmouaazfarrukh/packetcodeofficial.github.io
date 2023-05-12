<?php
require("sendgrid/sendgrid-php.php");
/** 1. MAIN SETTINGS
*******************************************************************/


// ENTER YOUR EMAIL
$emailTo = "support@tableconnectapp.com";


// ENTER IDENTIFIER
$emailIdentifier =  "Message sent via contact form from " . $_SERVER["SERVER_NAME"];



/** 2. MESSAGES
*******************************************************************/


// ERROR OR EMPTY FIELD
$errrorEmptyField = "* This Field is required!";


// ERROR FOR INVALID EMAIL
$errrorEmailInvalid = "* This Email is Invalid!";


// SUCCESS MESSAGE
$successMessage = "* The Email was Sent Successfully!";


/** 3. MAIN SCRIPT
*******************************************************************/


if($_POST) {

    $name = addslashes(trim($_POST["name"]));
    $clientEmail = addslashes(trim($_POST["email"]));
    $message = addslashes(trim($_POST["message"]));
	// $fhp_input = addslashes(trim($_POST["phone"]));

    $array = array("nameMessage" => "", "emailMessage" => "", "messageMessage" => "","succesMessage" => "");

    if($name == "") {
    	$array["nameMessage"] = $errrorEmptyField;
    }
	
    if(!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $array["emailMessage"] = $errrorEmailInvalid;
    }
	
    if($message == "") {
        $array["messageMessage"] = $errrorEmptyField;
    }
	
    if($name != "" && filter_var($clientEmail, FILTER_VALIDATE_EMAIL) && $message != "") {
		
		$array["succesMessage"] = $successMessage;
		
		// $headers  = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// $headers .= "From: " . $name . " <" . $clientEmail .">\r\n";
		// $headers .= "Reply-To: " . $clientEmail;
		
		// mail($emailTo, $emailIdentifier, $message, $headers);



        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom($clientEmail, $name);
        $email->setSubject("Message from developer contact form");
        $email->addTo($emailTo, "Table Connect Developer");
        $email->addContent("text/plain", $message);
        $sendgrid = new \SendGrid("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");
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
		
    }

    echo json_encode($array);

}