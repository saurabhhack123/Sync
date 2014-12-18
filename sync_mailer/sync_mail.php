<?php

 error_reporting(0);

 include_once "../../connection/connect.php";

 /**
   Class to show Logs
 */

  class Log
  {
  	var $logs;

  	function __construct($from,$to)
  	{
        $sql = "select * from `logged` where DATETIME >='$from' and DATETIME<='$to' ";
        $res = $this->exe_query($sql);
        $i=0;
        while($row = mysql_fetch_array($res)){
          
          $this->logs[$i]["STACK_TRACE"] = $row["STACK_TRACE"];
          $this->logs[$i]["LOGCAT"] = $row["LOGCAT"];
          $this->logs[$i++]["USER_CRASH_DATE"]=$row["USER_CRASH_DATE"];
        }
  	}
   
   function exe_query($sql){
      $res = mysql_query($sql);
      if(!$res) die("Error in sql ".$sql);
      else return $res;
   } 
  }

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
       $res_1 = mysql_query($sql_1) or die("Error".mysql_error());
       

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
     
      $mail->Subject    = "App crashed!";
      $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
      
      $mail->MsgHTML($body);
      $mail->AddAddress($email_id);
       
      if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
      } else {
        return;
      }
   }
    
    // run through two db 
     
     connect_db(16);

     $sql = "select distinct SchoolId from map_school_tab";

     $res = mysql_query($sql) or die("Error in fetching from map_school_tab");
     $schools = array();
     while($row=mysql_fetch_array($res)){
       array_push($schools,$row["SchoolId"]);
     }
    
	foreach ($schools as $school_id) {
	  
	  $nsync_tabs = array();

      date_default_timezone_set("Asia/Kolkata");
	  
	  $today = date("Y-m-d H:i:s");
      $from  = date('Y-m-d H:i:s', strtotime(' -24 hour'));


	  $nsync_tabs = get_not_sync_tab($school_id,$from,$today); 


	  $school_name = get_school_name($school_id);
      $mailbody = "";
        
      // logs objects

	  $log_entry =  new Log($from,$today);
      $logs = $log_entry->logs;
   
      
      $html_log = "";
      $html_log.= "<h4>App crash report<h4>";
      
	  foreach ($logs as $log) {

	    $start  = strpos($log["LOGCAT"],"D/id");
	    $tab_id = substr($log['LOGCAT'], $start+18,17);
	    $user_crash_date = $log["USER_CRASH_DATE"];
	    
		$html_log.="<h5>Tab :</h5>".$tab_id;
		$html_log.="<h5>Crash Date</h5>".$user_crash_date."<br>";
		
	  }
      
	  if(!empty($nsync_tabs) || !empty($logs))
	  {
	     
	     $email_id_1  = "saurabh@schoolcom.in";

	     $html = ""; 
	     $html.= "<h4>Following Tablet's of ".$school_name." are not sync properly !<h4>";
	     
	     foreach ($nsync_tabs as $tab_id=>$q_arr) {
	     	$html.="<h5>".$tab_id."</h5>";
	     	$html.="<p>";
	     	foreach ($q_arr as $query) {
	     	   $html.= "<ol>".$query."</ol>"; 
	     	}
	     	$html.="</p>"; 
	     }

       // getting failed logged data 
       
       $log_foot = "<br>Pls find the stack trace and Logcat in app-admin<br>";

       if(!empty($logs)) 
		       $mailbody.=$html.$html_log.$log_foot;
       

       send_mail($email_id_1,$mailbody); 

	  } 	
	}

?>