<?php
  /* This file is used to recieve data from tab to server except sliptest and Homework */
    error_reporting(0);
    ini_set('max_execution_time', 30000000);
     
    include_once "../../connection/connect.php";
    include_once "../helper/assist.php";

 /*initialize all variables */

    $syncall  = array();
    $response = array();
    $response["success"] = 0;
 
 /*recieve data from URI request */

    $json      = file_get_contents('php://input');
    $syncall   = json_decode($json,true);
    $tab_id    = $syncall['tab_id'];
    

/* This is for testing tab piloting for switching db dynamically */
    
    $school        = $syncall['school'];
    connect_db($school);

    $school_id = get_school_id($tab_id);

 /* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

 /* preparing json data to send */

    if(!empty($syncall) && $is_valid_req )
    {      
      $succ_ids = array();

      foreach($syncall['Sync'] as $syncpresent){

         $query       = $syncpresent['Query'];
         $action      = $syncpresent['Action'];
         $table_name  = $syncpresent['TableName'];
         $tab_sync_id = $syncpresent['SyncId'];
         
         
         if($query!="" && $table_name!="studentattendance"){
        
           insert_into_track_tab_data($tab_id,$school_id,$query,$action,$table_name);
           insert_row_into_validate_sync($school_id,$tab_id,$query);
           last_time_sync_record($school_id,$tab_id);

         if($table_name=="exmavg" || $table_name=="stavg")
              {
                $valid_tabs = get_all_valid_tab($tab_id,$school_id);
                foreach ($valid_tabs as $tab){                  
                  insert_row_in_sync_table($school_id,$table_name,$action,$query,0,0,1,$tab);
                }
                array_push($succ_ids,$tab_sync_id);
              }   
           else
             { 
               $q_success = _run_query($query);
     
               if($q_success)
                  array_push($succ_ids,$tab_sync_id);
              else
                  array_push($succ_ids,$tab_sync_id);

              }
         }
        }

      
      //**********************************************
       
      if(!empty($succ_ids))
          $response["sync_success"] = implode(",",$succ_ids);

      $response["success"] = 1;
        
     }
    echo json_encode($response);
?>