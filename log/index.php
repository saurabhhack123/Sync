<?php
   error_reporting(E_ALL ^ E_NOTICE);
 
   include_once "../../connection/connect_log.php";
   require "../../../../../schoolonweb/PHPMailer/class.phpmailer.php";
   include_once "../report/helpers.php";
     
   $response["success"] = 0; 
   /*
     
     New Json parama
     { school : , tab_id : , log : , date: }
   */
   
   $json      = file_get_contents('php://input');
   $error_log = json_decode($json,true);
   $school    = $error_log["school"];
   $date      = $error_log["date"];
   $tab_id    = $error_log["tab_id"];
   $stack     = $error_log["log"];
   
   $sql = "insert into `logged`(STACK_TRACE,TabId,School,USER_CRASH_DATE) values('$stack','$tab_id','$school','$date')";
   $res = mysql_query($sql) or die(mysql_error());

   if(!$res) $response["success"] = 0;
   else $response["success"] = 1;
   
   $mail_body = "OMG! App crashed! "."TabId:".$tab_id." Pls check logs";
   
   $emails = array("saurabh@schoolcom.in","vinay@schoolcom.net");
   
   foreach ($emails as $email_id) {
      send_mail($email_id,$mail_body);
   }
            
   echo json_encode($response);
  
 ?>