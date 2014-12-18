<?php
  error_reporting(0);
  ini_set('max_execution_time', 30000000);

/* this file is used to send hw data from server to tab */

   include_once "../../connection/connect.php";
   include_once "../helper/assist.php";

/* recieve data from URI request */   

   $tab_id    = $_POST['tab_id'];
   $school_id = get_school_id($tab_id);

/* check whether data is available or not */

   $is_data = is_hwdata_for_tab($school_id,$tab_id);
   last_time_sync_record($school_id,$tab_id);

/* validating tablet */
 
   $is_valid_req = is_tab_valid($tab_id);

/* prepare json data to send */

   if($is_data && $is_valid_req)
     $response = get_hwdata_for_tab($school_id,$tab_id);
   else
     $response["success"]=0;
   
   
   echo json_encode($response);
 ?>