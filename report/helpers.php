<?php
 
 // helper function are live here

 error_reporting(0);
 ini_set('max_execution_time', 30000000);
 
 include_once "../../connection/connect.php";

 function get_student_details($student_id){
      $sql     = "select * from students where StudentId='$student_id'";
      $res     = mysql_query($sql) or die("Error in fetching from students".mysql_error());
      $details = array();

      while($row=mysql_fetch_array($res)){
        $details["Name"]    = $row["Name"];
        $details["Roll_no"] = $row["RollNoInClass"];
        $details["Class"]   = $class_name;
        $details["Section"] = $section_name;
        $details["Mobile1"] = $row["Mobile1"];
        $details["Mobile2"] = $row["Mobile2"];
       }

      return $details;
   }

  function subject_name($subject_id){
    $sql = "select SubjectName from subjects where SubjectId='$subject_id'";
    $res = mysql_query($sql) or die("Error in fetching subjects".mysql_error());
    $row = mysql_fetch_array($res);
    return $row["SubjectName"];
   }

 function get_student_ids($section_id){
    $sql = "select StudentId from students where SectionId='$section_id'";
    $res = mysql_query($sql) or die("Error in fetching from students".mysql_error());
    
    $student_ids = array();
    while($row=mysql_fetch_array($res)){
      array_push($student_ids,$row['StudentId']);
    }
    return $student_ids;
   }

 function fetch_classes($school_id,$date_prev,$date_curr){
    
    $sql = "select distinct ClassId from sliptest where TestDate<='$date_curr' and TestDate>='$date_prev' and SchoolId='$school_id'";
    $res = mysql_query($sql) or die(mysql_error().$sql);

    $class_ids = array();

    while($row = mysql_fetch_array($res)){
      array_push($class_ids,$row["ClassId"]);
    }

    return $class_ids;    
 }

 function fetch_sections($class_id,$date_prev,$date_curr){

    $sql = "select distinct SectionId from sliptest where TestDate<='$date_curr' and TestDate>='$date_prev' and ClassId='$class_id'";
    $res = mysql_query($sql) or die(mysql_error().$sql);

    $section_ids = array();

    while($row = mysql_fetch_array($res)){
      array_push($section_ids,$row["SectionId"]);
    }

    return $section_ids;
 }

function fetch_slip_ids($section_id,$date_prev,$date_curr){

    $sql = "select SlipTestId,SlipTestName,MaximumMark,PortionName,SubjectId,TestDate from sliptest where TestDate<='$date_curr' and TestDate>='$date_prev' and SectionId='$section_id'";
    $res = mysql_query($sql) or die(mysql_error().$sql);

    $all_slips = array();

    while($row = mysql_fetch_array($res)){
      
      $slipids_ids = array();
      $slipids_ids["slip_id"]   = $row["SlipTestId"];
      $slipids_ids["slip_name"] = $row["SlipTestName"];
      $slipids_ids["max_mark"]  = $row["MaximumMark"];
      $slipids_ids["portion"]   = $row["PortionName"];
      $slipids_ids["sub_id"]    = $row["SubjectId"];
      $slipids_ids["test_date"] = $row["TestDate"];
      $all_slips[] = $slipids_ids;
    }

    return $all_slips;
}


function fetch_stu_slip_mark($slip_id,$student_id,$school_id){
   
   $sql = "select Mark from sliptestmark_$school_id where SlipTestId='$slip_id' and StudentId='$student_id'"; 
   $res = mysql_query($sql) or die(mysql_error().$sql);
    
   $row = mysql_fetch_array($res);
   $mark= $row["Mark"]; 

   return ($mark=="")?0:$mark;
}

function class_teacher($section_id){

  $sql = "select ClassTeacherId from section where SectionId='$section_id'";
  $res = mysql_query($sql) or die(mysql_error().$sql);
  $row = mysql_fetch_array($res);
  return $row["ClassTeacherId"];
}

function send_sms($school_id,$mobile,$msg,$student_id){
   
   $msg = mysql_real_escape_string($msg); 
   $sql="INSERT into queue_transaction(SchoolId,Phone,Message,Status,UserId,Role) values('$school_id','$mobile','$msg','0','$student_id','student')";

   if($mobile!="" && $mobile!="910")
        mysql_query($sql) or die('Error in inserting into Queue !'. mysql_error()); 
   
}

function is_uploaded_hw($curr_date,$section_id){
    
    $sql = "select HomeWorkId from homeworkmessage where HomeworkDate='$curr_date' and SectionId='$section_id'";
    $res = mysql_query($sql) or die(mysql_error().$sql);
    return mysql_num_rows($res)>0?1:0;

}


function is_uploaded_att($curr_date,$section_id){
    $sql = "select SectionId from studentattendance where DateAttendance='$curr_date' and SectionId='$section_id'";
    $res = mysql_query($sql) or die(mysql_error().$sql);
    return mysql_num_rows($res)>0?1:0;
}

function classes($school_id){
   
   $sql = "select ClassId from class where SchoolId='$school_id'";
   $res = mysql_query($sql) or die(mysql_error().$sql);

   $class_ids = array();
   while($row = mysql_fetch_array($res)){

    array_push($class_ids,$row["ClassId"]);
   }
   return $class_ids;
}


function sections($class_id){
   
   $sql = "select SectionId from section where ClassId='$class_id'";
   $res = mysql_query($sql) or die(mysql_error().$sql);

   $section_ids = array();
   while($row = mysql_fetch_array($res)){

    array_push($section_ids,$row["SectionId"]);
   }
   return $section_ids;
}

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
?>

