<?php
  /* this file is helper for syncing */
  error_reporting(0);
  ini_set('max_execution_time', 30000000);
  
  include_once "../../connection/connect_new.php";
  
  function is_sync_enable($school_id){
    
    $sql = "select * from block_queue where school_id='$school_id' and stp_sync_scp=1";
    $res = mysql_query($sql) or die(mysql_error().$sql);
    return mysql_num_rows($res) > 0 ?0:1;
  }

  function is_tab_valid($tab_id){
    
    $sql  = "select TabId from map_school_tab where TabId LIKE '%$tab_id%'";
    $res  = mysql_query($sql) or die("Error in fetching from map_school_tab".mysql_error());
    $rows = mysql_num_rows($res);
    return $rows>0?true:false;   
  }
  
  function  delete_prev_entries($school_id,$tab_id){
      $sql_1 = "delete from sync_mgt  where SchoolId='$school_id' and TabId='$tab_id'";
      $sql_2 = "delete from sync_slip where SchoolId='$school_id' and TabId='$tab_id'";

      mysql_query($sql_1) or die("Error in deleting from sync_mgt :".mysql_error());
      mysql_query($sql_2);
   
  }

  function ack_pending($query,$tab_id){

    $encode_query = mysql_real_escape_string($query);
    
    $sql  = "select SlipId from sync_slip where Query='$encode_query' and TabId='$tab_id'";
    $res  = mysql_query($sql) or die("Error in fetching sync_slip".mysql_error());
    $row  = mysql_fetch_array($res);
    $rows = mysql_num_rows($res);
    if($rows > 0) return $row["SlipId"];
    else return false;
  }
  
  function exe_query($query,$msg=""){
     mysql_query($query) or die($msg."<-->".mysql_error());
  }

  function _run_query($query){
    $res = mysql_query($query);
    if (!$res){
      return false;
    }
    return true;
  }

  function get_school_id($tab_id){
    $sql = "select SchoolId from map_school_tab where TabId LIKE '%$tab_id%'";
    $res = mysql_query($sql) or die("Error in fetching from map_school_tab".mysql_error());
    $row = mysql_fetch_array($res);
    return $row['SchoolId'];
  }

  function query_filter($query){
    $bad_words = array();
    array_push($bad_words,"LastLoginTime");
    array_push($bad_words,"IPAddress");
    
    foreach ($bad_words as $bad_word) {

      if (strpos($query,$bad_word) !== false) {
         return true;
       }
    }
    return false;
  }

  function insert_row_into_validate_sync($school_id,$tab_id,$query){

    $encode_query = mysql_real_escape_string($query);
    
    $sql          = "insert into validate_sync(SchoolId,Tab_Id,Query) values('$school_id','$tab_id','$encode_query')";
    exe_query($sql,"Error in inserting into Validate sync !");
  }

  function insert_into_track_tab_data($tab_id,$school_id,$query,$action,$table_name){

    $encode_query = mysql_real_escape_string($query);
    $sql          = "insert into track_tab_data(TabId,SchoolId,Query,Action,TableName)
                     values ('$tab_id','$school_id','$encode_query','$action','$table_name')";
    exe_query($sql,"Error in inserting into track_tab_data !");
  }

  function  is_cdata_for_tab($school_id,$tab_id){

    $sql  = "select Sync_Mgt_Id from sync_mgt where SchoolId='$school_id' and TabId LIKE '%$tab_id%' and IsAck=0 and Is_complex=1";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);
    
    return ($rows>0)?true:false;
  }

  function  is_data_for_tab($school_id,$tab_id){

    $sql  = "select Sync_Mgt_Id from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=1";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows > 0) return true;
    else          return false;
  }

  function is_slipdata_for_tab($school_id,$tab_id){

    $sql  = "select * from sync_slip where SchoolId='$school_id' and TableName='sliptest' and TabId='$tab_id' and IsAck=0";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows > 0) return true;
    else          return false;

  }

  function is_hwdata_for_tab($school_id,$tab_id){

    $sql  = "select * from sync_slip where SchoolId='$school_id' and TableName='homeworkmessage' and TabId='$tab_id' and IsAck=0";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows > 0) return true;
    else          return false;

  }

  function is_strucdata_for_tab($school_id,$tab_id){
    $sql  = "select * from sync_slip where SchoolId='$school_id' and (TableName!='sliptest' and TableName!='homeworkmessage') and TabId='$tab_id' and IsAck=0";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows > 0) return true;
    else          return false;
  }

  
  function get_data_for_tab($school_id,$tab_id,$table_name,$offset){
    
    $data["Sync"] = array();
    
    if($offset==1)
      $sql  = "select * from $table_name where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=1 order by DateTimeRecordInserted ASC";
    else
      $sql  = "select * from $table_name where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=1 order by DateTimeRecordInserted ASC LIMIT $offset,5000";
    
    $res  =  mysql_query($sql) or die("$sql Error in fetching from sync_mgt !".mysql_error());
    $rows =  mysql_num_rows($res);

    if($rows == 0)
    {  $data["success"] = 0 ; }
    else{
      while($row = mysql_fetch_array($res)){
          $temp = array();
          $temp['Ack_Id']    = $row['Sync_Mgt_Id']; 
          $temp['Query']     = $row['Query'];
          $temp['TableName'] = $row['TableName'];
          $temp['Action']    = $row['Action'];
          $temp['SchoolId']  = $row['SchoolId'];
          array_push($data["Sync"], $temp);
       } 
       $data["success"] = 1;
     }
   return $data;
  }

  function get_slipdata_for_tab($school_id,$tab_id){

    $data["Sync"] = array();
    
    $sql  = "select * from sync_slip where SchoolId='$school_id' and TabId='$tab_id' and TableName='sliptest' and IsAck=0 order by DateTimeRecordInserted ASC";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows == 0)
    {  $data["success"] = 0 ;}
    else{
      while($row = mysql_fetch_array($res)){
          $temp = array();
          $temp['Ack_Id']    = $row['SyncSlipId']; 
          $temp['Query']     = $row['Query'];
          $temp['TableName'] = $row['TableName'];
          $temp['Action']    = $row['Action'];
          $temp['SchoolId']  = $row['SchoolId'];
          $temp['SlipId']    = $row['SlipId'];
          
          array_push($data["Sync"], $temp);
       } 
     $data["success"] = 1;
     }
   return $data;
  }

function get_hwdata_for_tab($school_id,$tab_id){

    $data["Sync"] = array();
    
    $sql  = "select * from sync_slip where SchoolId='$school_id' and TabId='$tab_id' and TableName='homeworkmessage' and IsAck=0 order by DateTimeRecordInserted ASC";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows == 0)
    {  $data["success"] = 0 ;}
    else{
      while($row = mysql_fetch_array($res)){
          $temp = array();
          $temp['Ack_Id']    = $row['SyncSlipId']; 
          $temp['Query']     = $row['Query'];
          $temp['TableName'] = $row['TableName'];
          $temp['Action']    = $row['Action'];
          $temp['SchoolId']  = $row['SchoolId'];
          $temp['HomeworkId'] = $row['SlipId'];
          
          
          if($temp['TableName']!='sliptest')  $temp['HomeworkId'] = $row['SlipId'];
          else       $temp['SlipId']  = $row['SlipId'];
          
          array_push($data["Sync"], $temp);
       } 
     $data["success"] = 1;
     }
   return $data;
  }

  function get_strucdata_for_tab($school_id,$tab_id){
    $data["Sync"] = array();
    
    $sql  = "select * from sync_slip where SchoolId='$school_id' and TabId='$tab_id' and (TableName!='sliptest' and TableName!='homeworkmessage') and IsAck=0 order by DateTimeRecordInserted ASC";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows == 0)
    {  $data["success"] = 0 ;}
    else{
      while($row = mysql_fetch_array($res)){
          $temp = array();
          $temp['Ack_Id']    = $row['SyncSlipId']; 
          $temp['Query']     = $row['Query'];
          $temp['TableName'] = $row['TableName'];
          $temp['Action']    = $row['Action'];
          $temp['SchoolId']  = $row['SchoolId'];
          $temp['StructId']   = $row['SlipId'];
          
          array_push($data["Sync"], $temp);
       } 
     $data["success"] = 1;
     }
   return $data;
  }

  function update_ack_of_sync_mgt($ack_id){

    $sql = "update sync_mgt Set IsAck=1 where Sync_Mgt_Id='$ack_id'";
    exe_query($sql,"Error in updating sync_mgt!");
  }

  function update_ack_of_sync_slip($ack_id){

    $sql = "update sync_slip Set IsAck=1 where SyncSlipId='$ack_id'";
    exe_query($sql,"Error in updating sync_slip!");
  }

  function ignore_tab_for_sync($school_id,$query,$created_at){
   
    $encode_query = str_replace("'","''",$query);
    $sql          = "select * from validate_sync where SchoolId='$school_id' and Query='$encode_query'";
    $res          = mysql_query($sql) or die("Error in fetching from validate_sync!".mysql_error());
    $tab_id       = "0";
    
    while($row = mysql_fetch_array($res)){
      $date_time = $row['DateTimeRecordInserted'];
      $time_diff = abs(strtotime($date_time) - strtotime($created_at)) ;
      
      if($time_diff<=500000) 
      {
        $tab_id = $row['Tab_Id'];
        break;
      } 
     
    }
     return $tab_id;
  }

  function get_all_tabs($school_id){

    $sql     = "select * from map_school_tab where SchoolId='$school_id'";
    $res     = mysql_query($sql) or die("Error in fetching from map_school_tab!".mysql_error());
    $tab_ids = array();

    while($row = mysql_fetch_array($res)){
        $tab    = $row["TabId"];
        array_push($tab_ids,$tab);
    }
    return $tab_ids;
  }

  function get_all_valid_tab($tab_id,$school_id){

    $sql     = "select TabId from map_school_tab where SchoolId='$school_id'";    
    $res     = mysql_query($sql) or die("Error in fetching from map_school_tab!".mysql_error());
    $tab_ids = array();

    while($row = mysql_fetch_array($res)){
       $tab    = $row["TabId"];
       if($tab!=$tab_id)
       {
        array_push($tab_ids,$tab);
       }
    }
    return $tab_ids;
  }

  function school_ids(){
    $schools = array();
    $sql = "select SchoolId from school";
    $res = mysql_query($sql) or die("Error in fetching school_id".mysql_error());

    while($row = mysql_fetch_array($res)){
       array_push($schools,$row["SchoolId"]);
    }
    return $schools;
  }

  function insert_row_in_sync_table($school_id,$table_name,$action,$query,$map,$is_ack,$is_complex,$tab_id){
    
    $encode_query = mysql_real_escape_string($query);    
    $map          = mysql_real_escape_string(json_encode($map));
    
    $sql = "insert into sync_mgt(SchoolId,TableName,Action,Query,Map,IsAck,Is_complex,TabId) values('$school_id','$table_name',
                            '$action','$encode_query','$map','$is_ack','$is_complex','$tab_id')";  
    exe_query($sql,"Error in inserting into sync_mgt!"); 
  }

   function insert_row_in_sync_slip($school_id,$table_name,$action,$query,$struc_id,$tab_id){
    
    $encode_query = mysql_real_escape_string($query);

    
    $sql = "insert into sync_slip(SchoolId,TableName,Action,Query,SlipId,IsAck,TabId) values('$school_id','$table_name',
                            '$action','$encode_query','$struc_id',0,'$tab_id')";

    exe_query($sql,"Error in inserting into sync_mgt!");

  }

  function update_is_new_flag($trigger_id){
    
    $sql = "update `trigger` set `IsNew`=0 where `TriggerId`='$trigger_id'";
    exe_query($sql,"Error in update trigger!");
  }
   
  function update_is_new_flag_trigger($trigger_id){ 
    $sql = "update `trigger_struc` set `IsNew`=0 where `TriggerStrucId`='$trigger_id'";
    exe_query($sql,"Error in update trigger!");
  } 

  function parse_query($action,$query){
      $query_map = array();
   
      if($action=="INSERT" || $action=="insert") 
        { $query_map = query_insert_parse($query);}
      
      return $query_map;
   } 


   function query_insert_parse($query){
      $open_brace_first   = strpos($query,"(",0)+1;
      $close_brace_first  = strpos($query,")",0);
      $open_brace_second  = strpos($query,"(",$open_brace_first)+1;
      $close_brace_second = strpos($query,")",$open_brace_second);
      
      $cols     = substr($query,$open_brace_first,$close_brace_first-$open_brace_first);
      $cols_val = substr($query, $open_brace_second,$close_brace_second-$open_brace_second);
    
      $cols_arr     = explode(",",$cols);
      $cols_val_arr = explode(",",$cols_val);
     
      $qmap = array();
      $len  = count($cols_arr);
      for($i=0;$i<$len;$i++){
         $key = trim($cols_arr[$i]);
         $val = trim($cols_val_arr[$i],"'");
         $qmap["$key"] = $val;
      }
      return $qmap;
   }

   function get_class_name($class_id){
    $sql = "select ClassName from class where ClassId='$class_id'";
    $res = mysql_query($sql) or die("Error in fetching from class".mysql_error());
    $row = mysql_fetch_array($res);
    return $row["ClassName"];
   }

   function get_section_name($section_id){
    $sql = "select SectionName from section where SectionId='$section_id'";
    $res = mysql_query($sql) or die("Error in fetching from section".mysql_error());
    $row = mysql_fetch_array($res);
    return $row["SectionName"];
   }

   function get_student_details($student_id){
      $sql     = "select * from students where StudentId='$student_id'";
      $res     = mysql_query($sql) or die("Error in fetching from students".mysql_error());
      $details = array();

      while($row=mysql_fetch_array($res)){
        $class_name         = get_class_name($row["ClassId"]);
        $section_name       = get_section_name($row["SectionId"]);
        $details["Name"]    = $row["Name"];
        $details["Roll_no"] = $row["RollNoInClass"];
        $details["Class"]   = $class_name;
        $details["Section"] = $section_name;
        $details["Mobile1"] = $row["Mobile1"];
        $details["Mobile2"] = $row["Mobile2"];
       }

      return $details;
   }

  function get_teacher_detail($teacher_id){

      $sql     = "select * from teacher where TeacherId='$teacher_id'";
      $res     = mysql_query($sql) or die("Error in fetching from teacher".mysql_error());
      
      $details = array();
      $row = mysql_fetch_array($res);
      $details["Name"]    = $row["Name"];
      $details["Mobile"]  = $row["Mobile"]; 
      $details["tch_id"]  = $row["TeacherId"]; 
      return $details;
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
   
/*---------function to check entry in attendance table----------*/  

function is_att_dup($section_id,$date_att,$query,$school_id){
   
   $sql = "select StudentId from studentattendance where SectionId='$section_id' and DateAttendance='$date_att'";
   $res = mysql_query($sql) or die(mysql_error());
   $rows= mysql_num_rows($res);

   if($rows > 0){
      // find the culprit now
      $culprit_tab = "";
      $culprit_tab = look_up_culprit_att($query,$school_id);
   }
  return $culprit_tab;
}

/*---------function to check entry in attendance table----------*/ 

function send_att_sms_to_parents($school_id,$action,$query){
       
       $att_map = array();
       
       // handle NA 
       
        if(strpos($query,"NA")){
          return 1;  
        }


       $att_map = parse_query($action,$query);

       $absent_date    =  $att_map["DateAttendance"]; 
       $student_id     =  $att_map["StudentId"];

       $details = array();
       $details = get_student_details($student_id);

       $name    = $details["Name"];
       $class   = $details["Class"];
       $section = $details["Section"];
       $roll_no = $details["Roll_no"];
       $mobile1 = "91".$details["Mobile1"];
       $mobile2 = "91".$details["Mobile2"];

       $msg="Dear Parent, Your ward ".$name." of ".$class."-".$section." ".$roll_no." is absent Today.".$absent_date;
       
       
       if($mobile1!="" && $details["Mobile1"]!="")  {
    
               $sql_1="INSERT into tab_queue(SchoolId,Phone,Message,Status,UserId,Role,SmsType,Date) values ('$school_id','$mobile1','$msg','0','$student_id','student','Att','$absent_date')";
               mysql_query($sql_1) or die('Error in inserting into Queue !'. mysql_error()); 
       
       }  
       if($mobile2!="" && $details["Mobile2"]!="") {
               $sql_2="INSERT into tab_queue(SchoolId,Phone,Message,Status,UserId,Role,SmsType,Date) values ('$school_id','$mobile2','$msg','0','$student_id','student','Att','$absent_date')";
               mysql_query($sql_2) or die('Error in inserting into Queue !'. mysql_error()); 
       } 
       return 1;               
   } 
  
  function parse_hw_query($query){

      $open_brace_first   = strpos($query,"(",0)+1;
      $close_brace_first  = strpos($query,")",0);
      $open_brace_second  = strpos($query,"(",$open_brace_first)+1;
      $close_brace_second = strpos($query,")",$open_brace_second);
      
      $cols     = substr($query,$open_brace_first,$close_brace_first-$open_brace_first);
      $cols_val = substr($query, $open_brace_second,$close_brace_second-$open_brace_second);
      
      $cols_arr     = explode(",",$cols);
      
      // logic has to change 
      $cols_val_arr = explode(",",$cols_val);
      
      $col_val_arr_mod = array();
      
      for($i=0;$i<=4;$i++){
        array_push($col_val_arr_mod,$cols_val_arr[$i]);
      }
      
      $pos1 = strpos($cols_val,"'");
      $pos2 = strpos($cols_val,"'",$pos1+2);
      $pos3 = strpos($cols_val,"'",$pos2+2);
      $pos4 = strpos($cols_val,"'",$pos3+2);
      $pos5 = strpos($cols_val,"'",$pos4+2);
      $pos6 = strpos($cols_val,"'",$pos5+2);
      
      
      array_push($col_val_arr_mod,substr($cols_val,$pos1,$pos2-$pos1+1));
      array_push($col_val_arr_mod,substr($cols_val,$pos3,$pos4-$pos3+1));
      array_push($col_val_arr_mod,substr($cols_val,$pos5,$pos6-$pos5+1));
      
         
      $qmap = array();
      $len  = count($cols_arr);
      for($i=0;$i<$len;$i++){
         $key = trim($cols_arr[$i]);
         
         $val = trim($col_val_arr_mod[$i],"'");
         $qmap["$key"] = $val;
      }
    return $qmap;
  }


 // function to send hw sms 

    function send_hw_sms_to_parents($school_id,$action,$query){
      
       $hw_map = array();
      
       // since this query is complex 
       $hw_map = parse_hw_query($query);

       
       $hw_date=$hw_map["HomeworkDate"]; 
       $hw_sub_ids=$hw_map["SubjectIDs"];
       $hw_homework_dtl=$hw_map["Homework"];

       $hw_school_id=$hw_map["SchoolId"];
       $hw_class_id=$hw_map["ClassId"];
       $hw_section_id=$hw_map["SectionId"];
       $date=$hw_map["HomeworkDate"];
        
       $studentIds=array();
       $subjectIds=array();
       $homeworks=array();
      
       $studentIds=get_student_ids($hw_section_id);
       
       $subjectIds=explode(',',$hw_sub_ids);
       $homeworks=explode('#',$hw_homework_dtl);
       

       $task=''; $i=0;
       for($i=0;$i<count($subjectIds);$i++){
        $subName=subject_name($subjectIds[$i]);
        if($i==0)
           $task.=$subName.': '.$homeworks[$i];
        else      
           $task.=', '.$subName.': '.$homeworks[$i];
       }

   

       $class_name = get_class_name($hw_class_id);
       $section_name = get_section_name($hw_section_id);

       $msg='HW: '.$class_name.'-'.$section_name.': Date:'.$date.''.$task;
       
       
       foreach($studentIds as $student_id){

       $details = array();
       $details = get_student_details($student_id);

       $name=$details["Name"];
       $class=$details["Class"];
       $section=$details["Section"];
       $roll_no=$details["Roll_no"];
       $mobile1="91".$details["Mobile1"];
       $mobile2="91".$details["Mobile2"];

       
       if($mobile1!="" && $details["Mobile1"]!=""){
    
               $sql_1="INSERT into tab_queue(SchoolId,Phone,Message,Status,UserId,Role,SmsType,Date) values 
               ('$hw_school_id','$mobile1','$msg','0','$student_id','student','HW','$date')";
     
                mysql_query($sql_1) or die('Error in inserting into Queue !'. mysql_error());        
       }  

       if($mobile2!="" && $details["Mobile2"]!="") {
               $sql_2="INSERT into tab_queue(SchoolId,Phone,Message,Status,UserId,Role,SmsType,Date) values 
               ('$hw_school_id','$mobile2','$msg','0','$student_id','student','HW','$date')";
   
               mysql_query($sql_2) or die('Error in inserting into Queue !'. mysql_error()); 
       } 
     
     }
       return 1;               
   } 


  function subject_name($subject_id){
    $sql = "select SubjectName from subjects where SubjectId='$subject_id'";
    $res = mysql_query($sql) or die("Error in fetching subjects".mysql_error());
    $row = mysql_fetch_array($res);
    return $row["SubjectName"];
   }



   function get_not_sync_tab($school_id,$from,$today){
   
     $sql = "select * from sync_mgt where SchoolId='$school_id' and DateTimeRecordInserted>='$from' and DateTimeRecordInserted<='$today'";
     $res = mysql_query($sql) or die("Error in fetching from sync mgt !".mysql_error());
     $nsync_tabs = array();

     while($row = mysql_fetch_array($res)){
        if( ($row['IsAck']==0) && (!in_array($row['TabId'],$nsync_tabs)) ){
               array_push($nsync_tabs,$row['TabId']);
        }
     }
     return $nsync_tabs; 
  }

  function all_school_ids(){
     
     $sql = "select distinct SchoolId from map_school_tab";
     $res = mysql_query($sql) or die("Error in fetching from map_school_tab");
     $school_ids = array();
     while($row=mysql_fetch_array($res)){
       array_push($school_ids,$row["SchoolId"]);
     }
     return $school_ids;
  }

  function get_cols_of_table($table){     
      $sql  = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='tabletproduction' AND `TABLE_NAME`='$table'";
      $res  =  mysql_query($sql) or die("Error in fetching from schema".mysql_error());
      $cols = array();
      while($row=mysql_fetch_array($res)){
        array_push($cols,$row['COLUMN_NAME']);
      }
      return $cols;
  }

  function get_query_id($query){
    
    $res  = mysql_query($query) or die("Error in insert sliptest".mysql_error());
    $q_id = mysql_insert_id();

    return $q_id;
  }
  
  function insert_into_sync_slip($school_id,$table_name,$action,$query,$query_id,$ack,$tab_id){
    $encode_query = mysql_real_escape_string($query);

    $sql = "insert into sync_slip(SchoolId,TableName,Action,Query,SlipId,IsAck,TabId) values('$school_id','$table_name',
      '$action','$encode_query','$query_id','$ack','$tab_id')";

    mysql_query($sql) or die("Error in inserting into sync_slip".mysql_error());

  }
  
  function  last_time_sync_record($school_id,$tab_id){

    $sql  = "select SchoolId from last_sync_record where SchoolId='$school_id' and TabId='$tab_id'";
    $res  = mysql_query($sql) or die("Error in fetching from last_sync_record".mysql_error());
    $rows = mysql_num_rows($res);
    
    date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d H:i:s"); 

    if($rows==0){
      $sql_1 = "insert into last_sync_record(SchoolId,TabId,Date) values('$school_id','$tab_id','$date')";
      mysql_query($sql_1) or die("Error in insertion in last_sync_record".mysql_error());
    }else{
      $sql_2 = "update last_sync_record set Date='$date' where SchoolId='$school_id' and TabId='$tab_id'";
      mysql_query($sql_2) or die("Error in updating".mysql_error());
    } 
  }

   function generate_map(){
        $map_rows = array();
        $map_rows[1] = 1;              // mapping of offset
        for($i=2;$i<=200;$i++)
             $map_rows[$i]=($i-1)*5000;
         
        return $map_rows;   
    }

     function get_map(){
        $map_rows = array();
        $map_rows[1] = 1;              // mapping of offset
        for($i=2;$i<=200;$i++)
             $map_rows[$i]=($i-1)*5000;
         
        return $map_rows;   
    }

    function get_rows($table_name,$school_id){     
      $sql   = "select * from $table_name where SchoolId='$school_id'";
      $res   = mysql_query($sql) or die("Error in fetching from $table_name".mysql_error());
      $rows  = mysql_num_rows($res);
      return $rows;
    }

 

    function get_rows_for_tab($table_name,$school_id,$tab_id){
      $sql   = "select TabId from $table_name where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=1";
      $res   = mysql_query($sql) or die("Error in fetching from $table_name".mysql_error());
      $rows  = mysql_num_rows($res);
      return $rows;

    }

    function fetch_specific_rows($table_name,$school_id,$offset){
      
      if($offset==1 || $offset=="")
            $offset=0;
          
      $sql   = "select * from $table_name where SchoolId='$school_id' LIMIT $offset,5000";
      $res   = mysql_query($sql) or die($sql."offset".$offset."Error in fetching from $table_name".mysql_error());
      return $res;
    }

    function fetch_rows($table_name,$school_id){
      
      $sql   = "select * from $table_name where SchoolId='$school_id'";  
      $res   = mysql_query($sql) or die("Error in fetching from $table_name".mysql_error());
      return $res; 
    }

    function look_up_culprit($query,$school){
      
      $last = strrpos($query,",");
      $second_last  = strrpos(substr($query,0,$last),",");
      $sub_encode_query = substr($query,0,$second_last);

      $last_1        = strrpos($sub_encode_query,",");
      $encode_query  = mysql_escape_string(substr(($sub_encode_query),0,$last_1));


      $sql = "select TabId from track_tab_data where Query LIKE '%$encode_query%' and SchoolId='$school' and TableName='homeworkmessage'";
      $res = mysql_query($sql) or die("Error in fetching from track_tab_data".mysql_error());
      $row = mysql_fetch_array($res);

      return $row["TabId"];
    }

    function look_up_culprit_att($query,$school){
      
      $encode_query  = mysql_escape_string($query);

      $sql = "select TabId from track_tab_data where Query LIKE '%$encode_query%' and SchoolId='$school' and TableName='studentattendance'";
      $res = mysql_query($sql) or die("Error in fetching from track_tab_data".mysql_error());
      $row = mysql_fetch_array($res);

      
      return $row["TabId"];
    }


    function is_present_hw($section_id,$hw_date,$query,$school){

      $sql = "select Homework from homeworkmessage where SectionId='$section_id' and HomeworkDate='$hw_date'";
      $res = mysql_query($sql) or die("Error in fetching from homeworkmessage".mysql_error());
      $row = mysql_num_rows($res);
      
      $culprit_tab = "";

      if($row > 0){
       // find the culprit now
        $culprit_tab = look_up_culprit($query,$school);

      }
      return $culprit_tab;
    }

    function is_present_slip($sliptest_id){
       $sliptest_id = str_replace("'","", $sliptest_id);
       $sql = "select SlipTestId from sliptest where SlipTestId='$sliptest_id'";
       $res = mysql_query($sql) or die("Error in fetching from sliptest".mysql_error());
       $rows= mysql_num_rows($res);

       return ($rows > 0)?true:false;
    }

    function delete_hw_sync_slip($school_id,$table_name,$action,$section_id,$hw_date){
      

      $query = "delete from $table_name where SectionId='$section_id' and HomeworkDate='$hw_date'";
      $encode_query = mysql_real_escape_string($query);
      $all_tabs = array(); 
      $all_tabs = get_all_tabs($school_id);   
      // create a hack to sync_mgt 
      
      foreach ($all_tabs as $tab_id) {
            $sql = "insert into sync_mgt(SchoolId,TableName,Action,Query,IsAck,TabId) values('$school_id','$table_name',
                            '$action','$encode_query',0,'$tab_id')";

            mysql_query($sql) or die("Error in insert sync_mgt ".mysql_error());
      }
      return 1;
      
    }

    function remaining_records($tab_id){

       $sql = "select (select count(*) from sync_mgt where TabId='$tab_id' and IsAck=0) as count1,
               (select count(*) from sync_slip where TabId='$tab_id' and IsAck=0) as count2";
       $res = mysql_query($sql) or die(mysql_error().$sql);

       $row = mysql_fetch_array($res);

       return ($row["count1"]+$row["count2"]);

     }

     function update_tm_script(){
      
      $date_time = date("Y-m-d h:i:s");

      $sql = "update chk_server set update_time='$date_time' where id=1";
      mysql_query($sql) or die(mysql_error().$sql);
     }

?>

