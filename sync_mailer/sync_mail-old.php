<?php
 error_reporting(E_ALL ^ E_NOTICE);
include_once "../../connection/connect.php";

require "../../../../../schoolonweb/PHPMailer/class.phpmailer.php";

function get_tabs($school_id){
	$sql = "select TabId from map_school_tab where SchoolId='$school_id'";
	$res = mysql_query($sql);
	$tabs = array();
	while($row=mysql_fetch_array($res)){
		array_push($tabs,$row["TabId"]);
	}
	return $tabs;
}

function get_not_sync_tab($school_id,$from,$today){
   
    $tabs = get_tabs($school_id);

    
    $nsync_tabs = array();
    foreach ($tabs as $tab_id){
       
       $sql_1 = "select distinct Query from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and DateTimeRecordInserted>='$from' and DateTimeRecordInserted<='$today'";   
       $res_1 = mysql_query($sql_1);

       while($row_1=mysql_fetch_array($res_1)){
       	$nsync_tabs["$tab_id"][] = $row_1["Query"];
       }

       $sql_2 = "select distinct Query from sync_slip where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and DateTimeRecordInserted>='$from' and DateTimeRecordInserted<='$today'";   
       $res_2 = mysql_query($sql_2);

       while($row_2=mysql_fetch_array($res_2)){
       	$nsync_tabs["$tab_id"][] = $row_2["Query"];
       }
    }
   
     return $nsync_tabs; 
  }


function get_school_name($school_id){
   $sql = "select SchoolName from school where SchoolId='$school_id'";
   $res = mysql_query($sql);
   $row = mysql_fetch_array($res);
   return $row["SchoolName"];
}

function send_mail($emailid,$mailbody)
	{

	 echo $emailid."<br>";
	 echo $mailbody."<br/>";
	 	
	 $body=$mailbody;
	 $mail = new PHPMailer(true);

	 $mail->IsSMTP();                          
	 $mail->SMTPAuth   = true;                
	 $mail->Port       = 25;                
	
	 $mail->Host       = "mail.schoocom.info"; 
	 $mail->Username   = "noreply+schoolcom.info";     
	 $mail->Password   = "noreply@123456";            

	 $mail->IsSendmail(); 
	 $mail->AddReplyTo("noreply@schoolcom.info","SchoolCom");

	 $mail->From       = "noreply@schoolcom.info";
	 $mail->FromName   = "SchoolCom";
     try { 
			$date=date('Y-m-d');
			$to=$emailid;

			$mail->AddEmbeddedImage("rocks.png", "my-attach", "rocks.png");
			$mail->Body = 'Embedded Image: <img alt="PHPMailer" src="cid:my-attach"> Here is an image!';
			 
			$mail->AddAddress("$to");
			$mail->Subject  = "Tablet Syncing Status: $date";
			
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			$mail->WordWrap   = 80; 

			$mail->MsgHTML($body); 
			$mail->IsHTML(true); 
			$mail->Send(); 
		}
		catch (phpmailerException $e) 
		{
			echo $e->errorMessage();
		}
     }
     


     $sql = "select distinct SchoolId from map_school_tab";
     $res = mysql_query($sql) or die("Error in fetching from map_school_tab");
     $schools = array();
     while($row=mysql_fetch_array($res)){
       array_push($schools,$row["SchoolId"]);
     }
    
	foreach ($schools as $school_id) {
	  
      date_default_timezone_set("Asia/Kolkata");
	  
	  $today = date("Y-m-d H:i:s");
      $from  = date('Y-m-d H:i:s', strtotime(' -1 hour'));

	  $nsync_tabs = get_not_sync_tab($school_id,$from,$today); 
	  $school_name = get_school_name($school_id);
      $mailbody = "";
    
	  if(!empty($nsync_tabs))
	  {
	     
	     $email_id_1  = "saurabh@schoolcom.in";
	     $email_id_2  = "mangal@schoolcom.in";
	     $email_id_3  = "vinay@schoolcom.net";
	     
	     $mail_body = "<h3>Following Tablet's of ".$school_name." are not sync properly !<h3>"."<br/>";
	     $mail_body.= "Failed Queries"."<br>";
	     $html = "";

	     foreach ($nsync_tabs as $tab_id=>$q_arr) {
	     	$html.="<div><h4>".$tab_id."</h4><div>";
	     	$html.="<div>";
	     	foreach ($q_arr as $query) {
	     	   $html.= "<ol>".$query."</ol>"; 
	     	}
	     	$html.="</div>"; 
	     }
 
       $mail_body.=$html;
        
	   send_mail($email_id_1,$mail_body); 
	   send_mail($email_id_2,$mail_body); 
	   send_mail($email_id_3,$mail_body); 
	        
	  
	  } 	
	}
	
?>