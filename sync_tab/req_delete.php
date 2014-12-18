<?php
/* this file is used to send json data from server to tab except sliptest */

   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   include_once "../../connection/connect.php";
   include_once "parse_delete.php";
   
/* This is for testing tab piloting for switching db dynamically */

   $school        = $_POST['school'];
   connect_db($school);
   
 /*recieve data from URI request */ 
   $tab_id        = $_POST['tab_id'];
   $req_call      = $_POST['req_call']; 
   
   $table_name    = "sync_mgt";       // for mormal syncing 
   $map_rows      = array();
   $map_rows      = generate_map(); // mapping of offset to chunk size
   
   $school_id     = get_school_id($tab_id);   
   
  /*check if data is available for tab*/

   $is_data = is_data_for_tab($school_id,$tab_id);

   last_time_sync_record($school_id,$tab_id);
 
  /* validating tab and prepare json data */

   $is_valid_req = is_tab_valid($tab_id);
   
   if($is_data && $is_valid_req){
   
     $rows  = get_rows_for_delete_tab($table_name,$school_id,$tab_id);

     if($rows <= 3000){
       $offset = 1; 
       
       $response = _get_nsync_delete_data($school_id,$tab_id,$table_name,$offset);

       $response["req_again"] = 0;    
       
     }else{

       $offset = $map_rows[$req_call];

       if($req_call >1) $offset+=($req_call-1);
        
        $response    = _get_nsync_delete_data($school_id,$tab_id,$table_name,$offset);

        if($rows > $map_rows[$req_call]+3000)  $response["req_again"] = $req_call+1;
        else  $response["req_again"] =0;
     }

    echo json_encode($response);
   }else{
    
    $response["success"]=0;
    echo json_encode($response);
   
   }
?>