<?php

/** 1. MAIN SETTINGS
 *******************************************************************/


// SUBSCRIBE FORM MODE [ "file" OR "mailchimp" OR "sendgrid" }
$mode = "sendgrid";


/** 1.1. MAIN SETTINGS [ FILE MODE ]
 *******************************************************************/


// ENTER PATH TO FILE
$file_path = $_SERVER["DOCUMENT_ROOT"] . "/";


// ENTER NAME OF FILE 
$file_name = "subscriber-list.txt";


/** 1.2. MAIN SETTINGS [ MAILCHIMP MODE ]
 ******************************************************************/


// ENTER MAILCHIMP API KEY
$mailchimp_api_key =  "449829568ba83076d6e604a5d27e73e3-us5";


// ENTER MAILCHIMP LIST ID
$mailchimp_list_id =  "9ed1727975";


/** 3. MESSAGES
 *******************************************************************/


// ENTER ERROR MESSAGE
$varError = "* An error occurred!";


// ENTER VALIDATION ERROR MESSAGE
$varErrorValidation = "* This email is invalid!";


// ENTER EMPTY ERROR MESSAGE
$varErrorEmpty = "* This Field is required!";


// ENTER SUCCESS MESSAGE
$varSuccess = "* Thanks for your interest!";


/** 5. MAIN SCRIPT
 *******************************************************************/


if ($mode === "mailchimp") {
    include("MailChimp.php");
}

use \DrewM\MailChimp\MailChimp;

if ($_POST) {

    $subscriber_email = $_POST['email'];
    //$subscriber_fhp_input = $_POST['phone'];
    $array = array();

    if ($subscriber_email == "") {

        $array["valid"] = 0;
        $array["message"] = $varErrorEmpty;
    } else {

        // if( !filter_var($subscriber_email, FILTER_VALIDATE_EMAIL) || $subscriber_fhp_input != "") {
        if (!filter_var($subscriber_email, FILTER_VALIDATE_EMAIL)) {

            $array["valid"] = 0;
            $array["message"] = $varErrorValidation;
        } else {

            if ($mode === "file") {

                file_put_contents($file_path . $file_name, strtolower($subscriber_email) . "\r\n", FILE_APPEND);

                if (file_exists($file_path . $file_name)) {

                    $array["valid"] = 1;
                    $array["message"] = $varSuccess;
                } else {

                    $array["valid"] = 0;
                    $array["message"] = $varError;
                }
            }

            if ($mode === "mailchimp") {

                $MailChimp = new MailChimp($mailchimp_api_key);

                $result = $MailChimp->post("lists/$mailchimp_list_id/members", [
                    'email_address' => $subscriber_email,
                    'status'        => 'subscribed',
                ]);

                if ($MailChimp->success()) {

                    $array["valid"] = 1;
                    $array["message"] = $varSuccess;
                } else {

                    $array["valid"] = 0;
                    $array["message"] = $varError;
                }
            }

            if ($mode === "sendgrid") {

                $curl = curl_init();

                $params = array("contacts" => array(array("email" => $subscriber_email)), "list_ids" => array("95ef4d3b-712b-43d1-94c2-954061c42aca"));
                $json_post_fields = json_encode($params);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => $json_post_fields,
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer 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",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    // echo "cURL Error #:" . $err;
                    $array["valid"] = 0;
                    $array["message"] = $varError;
                } else {
                    // echo $response;
                    $array["valid"] = 1;
                    $array["message"] = $varSuccess;
                }
            }
        }
    }

    echo json_encode($array);
}
