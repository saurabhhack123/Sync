<?php

	error_reporting(0);
	include_once "../../connection/connect_new.php";
	require "../../../../../schoolonweb/PHPMailer/class.phpmailer.php";
    include_once "helpers.php";
    
    // have to change 

    $tab_using_schools = array('26');

    foreach ($schools as $school_id) {
	  
	  $nsync_tabs = array();

      date_default_timezone_set("Asia/Kolkata");
	  
	  $today = date("Y-m-d H:i:s");
      $from  = date('Y-m-d H:i:s', strtotime('-24 hour'));

	  $nsync_tabs  = get_not_sync_tab($school_id,$from,$today); 
	  $school_name = get_school_name($school_id);
	  
      $mailbody = "";
      
	  if(!empty($nsync_tabs))
	  {  
	     $email_id  = "saurabh@schoolcom.in";
	     $html      = ""; 
	     $html.= "<h4>Following Tablet's of ".$school_name." are not sync properly !<h4>";
	     
	     foreach ($nsync_tabs as $tab_id=>$q_arr) {
	     	$html.="<h5>".$tab_id."</h5>";
	     	$html.="<p>";
	     	foreach ($q_arr as $query) {
	     	   $html.= "<ol>".$query."</ol>"; 
	     	}
	     	$html.="</p>"; 
	     }
	     $mailbody.="html";       
         send_mail($email_id,$mailbody);
	  }
  } 	
?>