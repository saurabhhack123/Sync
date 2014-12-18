<?php
	
	error_reporting(0);
	include_once "../../connection/connect_new.php";

    function tab_using_schools(){
		$sql="select distinct SchoolId from map_school_tab";
		$res=mysql_query($sql);

	    $schools = array(); 
		while ($row=mysql_fetch_array($res)) {
		    
		    array_push($schools,$row["SchoolId"]);
		}
		return $schools;
	 }

    $today=date("Y-m-d");    
    date_default_timezone_set('Asia/Calcutta');
	
    // logic for scheduling att sms for tab 
    // $schools = tab_using_schools();
    $schools = array('16');

   
    foreach ($schools as $school_id) {
	     $sql="select * from tab_sms_schedule WHERE SchoolId='$school_id'";
	     $res=mysql_query($sql);
	     $row=mysql_fetch_array($res);
	     
	     $sendAttTime = $row['att_time'];
		 $sendHwTime  = $row['hw_time'];
  
		 $cur_hour    = (int)date('h');
		 		 
		 if($cur_hour==$sendAttTime){
		
		 	$sql="SELECT * FROM tab_queue WHERE SmsType='Att' AND SchoolId=$school_id AND Status=0 AND Date='$today'";
			$result=mysql_query($sql,$con);
			while($row=mysql_fetch_assoc($result)){
				$phone=$row['Phone'];
				$msg=$row['Message'];
				$id=$row['Id'];
				$user_id = $row["UserId"];
				$role = $row["Role"];

				$sql1="INSERT INTO queue(SchoolId,Phone,UserId,Role,Message,Status) values ($school_id,'$phone','$user_id','$role','$msg',0)";
				mysql_query($sql1,$con) or die('Error in inserting into Queue for Att data!'. mysql_error()); 
				
				$sql2="UPDATE tab_queue set Status=1 WHERE Id=$id";
				$result2=mysql_query($sql2,$con);
				
			}			
		 }

		 if($cur_hour==$sendHwTime){
		 	$sql="SELECT * FROM tab_queue WHERE SmsType='HW' AND SchoolId=$school_id AND Status=0";
			$result=mysql_query($sql);
			while($row=mysql_fetch_assoc($result)){
				$phone=$row['Phone'];
				$msg=$row['Message'];
				$id=$row['Id'];
				$user_id = $row["UserId"];
				$role = $row["Role"];
				
				$sql3="INSERT INTO queue(SchoolId,Phone,UserId,Role,Message,Status) values ($school_id,'$phone','$user_id','$role','$msg',0)";
				mysql_query($sql3,$con) or die('Error in inserting into Queue for HW data!'. mysql_error()); 
				
				$sql4="UPDATE tab_queue set Status=1 WHERE Id=$id";
				$result4=mysql_query($sql4,$con);
				
			}
		 }
	 
	}

// end of while
    


?>