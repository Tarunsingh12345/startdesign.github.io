<?php

if(isset($_POST['service_submit'])) {
 
    // EDIT THE 2 LINES BELOW AS REQUIRED
    //$email_to = "gauravsharma@startdesigns.com";
    $email_to = "info@startdesigns.com";
    $email_to_reponder = $_POST['semail'];

    $email_subject = "services-us";
    $email_subject_responder = "Start Desgins Services";
    $from = 'no-reply@startdesigns.com';
 
    $error = false;
    $error_message = '';
    $response = [];
    // validation expected data exists
    if(!isset($_POST['sname']) ||
        !isset($_POST['semail']) ||
        !isset($_POST['phone']) 
       // !isset($_POST['smessage'])
        ) {
        $error_message .= 'We are sorry, but there appears to be a problem with the form you submitted.<br />';
        $response['status'] = false;
        $response['message'] = $error_message;
        echo json_encode($response); die;
    }
 
    
    $first_name = $_POST['sname']; // required
    $email_from_template = $_POST['semail']; // required
    $email_from = $from; // required
    $number = $_POST['phone']; //  required
    $message = $_POST['smessage']; // required
 
    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    

  if(!preg_match($email_exp,$email_from)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
    $error = true;
  }
        
    $string_exp = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$first_name)) {
    $error_message .= 'The Name you entered does not appear to be valid.<br />';
    $error = true;
  }
    
 /* if(strlen($message) < 2) {
    $error_message .= 'The Message you entered do not appear to be valid.<br />';
    $error = true;
  }*/
    

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }


  if($error) {
    $response['status'] = false;
    $response['message'] = $error_message;
    echo json_encode($response); die;
  }else{

    $data["sname"]   = clean_string($first_name);
    $data["semail"]   = clean_string($email_from_template);
    $data["snumber"]       = clean_string($number);
    $data["smessage"]      = clean_string($message);
    $data["year"]      = date('Y');
    $data["base_url"]      = 'https://www.startdesigns.in/startdesigns2019';
    $data["email_to_reponder"] = clean_string($email_to_reponder);

    $template = file_get_contents("service_template.html");
    $responder_template = file_get_contents("service_responder_template.html");

    foreach($data as $key => $value)
    {
        $template = str_replace('{{ '.$key.' }}', $value, $template);
        $responder_template = str_replace('{{ '.$key.' }}', $value, $responder_template);
    }

    $email_message = $template;
    $email_message_responder = $responder_template;
    // create email headers
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
    // Create email headers
    $headers .= 'From: '.$email_from."\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($email_to, $email_subject, $email_message, $headers);
    @mail("$email_to_reponder", $email_subject_responder, $email_message_responder, $headers);
    $response['status'] = true;
    $response['message'] = 'Thank you.We will contact you soon.';
    echo json_encode($response); die; 
  }
}
 


?>