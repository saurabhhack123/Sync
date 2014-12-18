<?php
error_reporting(0);

ini_set('max_execution_time', 30000000);


 include_once "../../connection/connect.php"; 
 
 /* helper function's  */

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

    $sql  = "select * from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and Action='DELETE' and IsAck=0 and Is_complex=0";
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

  function get_rows_for_delete_tab($table_name,$school_id,$tab_id){
      $sql   = "select * from $table_name where SchoolId='$school_id' and TabId='$tab_id' and Action='DELETE' and IsAck=0 and Is_complex=0";
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

function _get_unique_tables($school_id,$tab_id,$table_name,$offset){
 
    if($offset==1)
       $offset=0;
    
    $tables = array();
    $sql = "select distinct TableName from $table_name where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Action='DELETE' and Is_complex=0 order by DateTimeRecordInserted ASC LIMIT $offset,3000";  
    $res = mysql_query($sql) or die("Error in fetching unique table".mysql_error());
    while($row=mysql_fetch_array($res)){
      array_push($tables,$row["TableName"]);
    }  
    return $tables;   
 }


function create_arr_where($q_map){
  $where = array();
  foreach ($q_map as $col => $val) {
    array_push($where,$col);
  }
  return $where;
}

function get_query_map_by_sync_id($sync_id){
  $sql = "select Map from sync_mgt where Sync_Mgt_Id='$sync_id'";
  $res = mysql_query($sql) or die("error in fetching query".mysql_error());
  $row = mysql_fetch_array($res);
  return json_decode($row["Map"],1);
}


function _prep_delete_json($school_id,$tab_id,$table,$offset){
   
    $sql = "select Query,Sync_Mgt_Id,Map from sync_mgt where SchoolId='$school_id' and TabId='$tab_id' and IsAck=0 and Is_complex=0 and TableName='$table'   
                and Action='DELETE' order by DateTimeRecordInserted ASC LIMIT $offset,3000";
    
    $res = mysql_query($sql) or die("Error in fetching sync_mgt".mysql_query($sql_i));
    
    $chunk = array();
    
     while($row = mysql_fetch_array($res)){
      // check for complex query 
      $group = array();
      $query  = $row["Query"];
      $ack_id = $row["Sync_Mgt_Id"];
      
      $q_map = array();
      $q_map = json_decode($row["Map"],1);

      $group["ack_id"]=$ack_id;
      $group["where_keys"] = create_arr_where($q_map);  
      $chunk["process"][] = $group;    
    }

   
   $help_chunk = $chunk["process"];
 
   $grp_ids = array();   
   $all_ids = array();

   for($i=0;$i<count($help_chunk);$i++)
       $all_ids[]= $help_chunk[$i]["ack_id"];
   
   for($i=0;$i<count($help_chunk)-1;$i++){
      for($j=$i+1;$j<count($help_chunk);$j++){
            
            $is_where_keys = $help_chunk[$i]["where_keys"]===$help_chunk[$j]["where_keys"];

            if($is_where_keys)
              {  
                 $grp_ids[$help_chunk[$i]["ack_id"]][] = $help_chunk[$j]["ack_id"];
              }
      }
   }

  foreach ($grp_ids as $key=>$id_arr) {     
       foreach ($id_arr as $val) {
         if($grp_ids[$val])
           unset($grp_ids[$val]);
       }
   } 
   
   $final = array();
   $skip = array();

  foreach ($all_ids as $id) {
    $temp = array();
    
    if(in_array($id,$skip))
      continue;

    array_push($temp,$id);

    if($grp_ids[$id]){        
        foreach ($grp_ids[$id] as $a) {
             array_push($temp,$a);
             array_push($skip,$a);           
          }  
    } 
    $final[] = $temp;
   }
   
  // final will be containing group's 
   $all_grps = array();
 
   foreach ($final as $group_arr) {
          
          $grp_temp = array();
          $grp_temp["table_name"] = $table;
          $q_map = array();
          $q_map = get_query_map_by_sync_id($group_arr[0]); 
 
          $grp_temp["del_col"] = create_arr_where($q_map);
          
          $grp_temp["del_val"] = array();

          foreach ($group_arr as $sync_mgt_id) {
               
               $q_map = array();
               $q_map = get_query_map_by_sync_id($sync_mgt_id);
               
               $temp = array();
               
               foreach($q_map as $p_key=>$p_val){
                $temp[$p_key]=str_replace("'","",$p_val);;
               }
            
               $temp["ack_id"] = $sync_mgt_id;
          
               $grp_temp["del_val"][] = $temp;  
          }
          
          $all_grps[] = $grp_temp;
   } // end of foreach
 return $all_grps;

}


function _get_nsync_delete_data($school_id,$tab_id,$table_name,$offset){
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
        
        /** preparing update json  ***********/        

        $delete_arr = array();
        
        $delete_arr = _prep_delete_json($school_id,$tab_id,$table,$offset);

        
       /** json preparation end for updation */
        
        $data["Sync"] = array_merge($data["Sync"],$delete_arr);

     } // end of foreach
    
     return $data;
}

?>
