<?php
   error_reporting(0);
 
   require "../../../../../../schoolonweb/PHPMailer/class.phpmailer.php";
   
   function send_mail($email_id,$body)
   {
      $mail = new PHPMailer(true);
      
      $mail->IsSMTP();
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
      $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
      $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
      $mail->Username   = "tab@schoolcom.in";    // GMAIL username
      $mail->Password   = "devilreborn";         // GMAIL password
      
      $body  = eregi_replace("[\]",'',$body);
      $mail->SetFrom('tab@schoolcom.in','Tablet SchoolCom');
      $mail->AddReplyTo('tab@schoolcom.in','Tablet SchoolCom');
     
      $mail->Subject    = "App crashed Testing! Don't Mind :)";
      $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
      
      $mail->MsgHTML($body);
      $mail->AddAddress($email_id);
       
      if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
      } else {
        return;
      }
   }

   $mail_body = "OMG! App crashed! for TabId:d82a3f7f6f5942a0 , Pls check logs in app_admin";
   $email_id  = "mangal@schoolcom.in";
   send_mail($email_id,$mail_body);
            
  
 ?>