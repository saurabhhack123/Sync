<?php
   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   
/* this file is used to recieve sliptest data from tab to server */

   include_once "../../connection/connect.php";
   include_once "../helper/assist.php";

  /*initialize all variables */  

    $syncall = array();
    $response = array();
    $response["success"] = 0; 

 /*recieve data from URI request */ 

    $json    = file_get_contents('php://input');
    $syncall = json_decode($json,true);
    $tab_id  = $syncall['tab_id'];


/* This is for testing tab piloting for switching db dynamically */

    $school = $syncall['school'];

    connect_db($school);
    
    $school_id  = get_school_id($tab_id);

 /* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

 /* prepare json data to send */

     if(!empty($syncall) && $is_valid_req)
     {  
        $sliptest_send = array();
        $slip_diff_tab = array();

        foreach($syncall['Sync'] as $syncpresent){         
         $query      = $syncpresent['Query'];
         $action     = $syncpresent['Action'];
         $table_name = $syncpresent['TableName'];
         $sliptest_id= $syncpresent['SlipTestId'];

         // code 
         
         $is_culprit_tab = is_present_slip($sliptest_id);
         
         $sliptest_id    = str_replace("'","", $sliptest_id);
         
         if($is_culprit_tab){
            // if culprit found ! 
            // check if duplicate is coming from different tab
              array_push($slip_diff_tab,$sliptest_id);
         }else{
              exe_query($query);
              array_push($sliptest_send,$sliptest_id);

              $valid_tabs = get_all_valid_tab($tab_id,$school_id);
              
             foreach($valid_tabs as $tab){
               insert_into_sync_slip($school_id,$table_name,$action,$query,$sliptest_id,0,$tab);
             }
         }

               
         insert_into_track_tab_data($tab_id,$school_id,$query,$action,$table_name);
         insert_row_into_validate_sync($school_id,$tab_id,$query);  
         last_time_sync_record($school_id,$tab_id);

       }
         if(!empty($sliptest_send))
            $response["sync_success"] = implode(",",$sliptest_send);

         if(!empty($slip_diff_tab))
            $response["sync_dup"]     = implode(",",$slip_diff_tab);

         $response["success"]        = 1; 
     }

     echo json_encode($response);
?>