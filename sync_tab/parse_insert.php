 <?php
  ini_set('max_execution_time', 30000000);
  error_reporting(0);


  include_once "../../connection/connect.php";

  function generate_map(){
        $map_rows = array();
        $map_rows[1] = 1;              // mapping of offset
        for($i=2;$i<=25;$i++)
             $map_rows[$i]=($i-1)*3000;
         
        return $map_rows;   
  }

  function get_school_id($tab_id){
    $sql = "select SchoolId from map_school_tab where TabId='$tab_id'";
    $res = mysql_query($sql) or die("Error in fetching from map_school_tab".mysql_error());
    $row = mysql_fetch_array($res);
    return $row['SchoolId'];
  }

  function  is_data_for_tab($school_id,$tab_id){

    $sql  = "select * from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and Action='INSERT' and IsAck=0 and Is_complex=0";
    $res  = mysql_query($sql) or die("Error in fetching from sync_mgt !".mysql_error());
    $rows = mysql_num_rows($res);

    if($rows > 0) return true;
    else          return false;
  }

 function  last_time_sync_record($school_id,$tab_id){

    $sql  = "select * from  last_sync_record where SchoolId='$school_id' and TabId='$tab_id'";
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

  function is_tab_valid($tab_id){
    $sql  = "select * from map_school_tab where TabId='$tab_id'";
    $res  = mysql_query($sql) or die("Error in fetching from map_school_tab".mysql_error());
    $rows = mysql_num_rows($res);
    if($rows > 0)
      return true;
    else
     return false;   
  }

  function get_rows_for_insert_tab($table_name,$school_id,$tab_id){
      $sql   = "select * from $table_name where SchoolId='$school_id' and TabId='$tab_id' and Action='INSERT' and IsAck=0 and Is_complex=0";
      $res   = mysql_query($sql) or die("Error in fetching from $table_name".mysql_error());
      $rows  = mysql_num_rows($res);
      return $rows;

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

/********************************************* assist function's  ********************** */
 
  function _get_unique_tables($school_id,$tab_id,$table_name,$offset){
 
    if($offset==1)
       $offset=0;
    
    $tables = array();
    $sql = "select distinct TableName from $table_name where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Action='INSERT' and Is_complex=0 order by DateTimeRecordInserted ASC LIMIT $offset,3000";  
    $res = mysql_query($sql) or die("Error in fetching unique table".mysql_error());
    while($row=mysql_fetch_array($res)){
      array_push($tables,$row["TableName"]);
    }  
    return $tables;   
 }

 function _prep_insert_json($school_id,$tab_id,$table,$offset){
        $insert_arr = array();

        $tab_cols = get_cols_of_table($table);
        $sql_i = "select Query,Sync_Mgt_Id,Map from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=0 and TableName='$table'   
                and Action='INSERT' order by DateTimeRecordInserted ASC LIMIT $offset,3000";
        $res_i = mysql_query($sql_i) or die("Error in fetching sync_mgt".mysql_query($sql_i));
        

        while($row_i = mysql_fetch_array($res_i)){
           // query can't be complex in insert
       
           $map  = array();
           $map  = json_decode($row_i["Map"],1);
   
           $ack_id = $row_i["Sync_Mgt_Id"]; 

           $insert_col_arr = array();
           
           foreach ($tab_cols as $col) {
               if(!$map["$col"])
                  $map["$col"]=0;
           }

           $map["ack_id"]= $ack_id;
           
           array_push($insert_arr,$map);              
        } // end of while
     return $insert_arr;
 }
  
 // main function , return json 

 function _get_nsync_insert_data($school_id,$tab_id,$table_name,$offset){

     $data["Sync"] = array();

     if($offset==1)
        $offset=0;

     $u_tables = _get_unique_tables($school_id,$tab_id,$table_name,$offset);


     if(empty($u_tables))
       $data["success"]= 0;
     else
       $data["success"]= 1;

     $temp = array();

     foreach($u_tables as $table){
        
        $tab_cols = get_cols_of_table($table);
  
        /** preparing insert json  ***********/        

        $insert_arr = array();

        $insert_arr = _prep_insert_json($school_id,$tab_id,$table,$offset);
        
        $temp["TableName"] = $table; 
        $temp["insert_col"]= $tab_cols; 
        $temp["insert"]=$insert_arr;
       
       /** json preparation end for insertion */
       
            
        array_push($data["Sync"], $temp);
     } // end of foreach
     
     return $data;
  } // end of function

?>
 