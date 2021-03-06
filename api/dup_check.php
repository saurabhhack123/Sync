<?php
  /* This file is used to for duplicates for structed exam like activity and subactivity */
    
    error_reporting(0);
    ini_set('max_execution_time', 30000000);
  
    include_once "../../connection/connect.php";
    include_once "../helper/assist.php";

  /* initialize all variables */

    $syncall  = array();
    $response = array();

    $response["success_act"] = 0;
    $response["success_sub"] = 0;
 
  /*recieve data from URI request */

    $json        = file_get_contents('php://input');
    $syncall     = json_decode($json,true);
    

    $act_ids     = $syncall["act_ids"];
    $sub_act_ids = $syncall["subact_ids"]; 

    
    $act_ids_arr     = explode(",",$act_ids);
    $sub_act_ids_arr = explode(",",$sub_act_ids);
  
    $tab_id      = $syncall["tab_id"];

   /* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

   /* preparing json data to send */
    
    if(!empty($syncall) && $is_valid_req )
    {      
      $act_ids_track = array();
      $sub_act_ids_track = array();
    
      foreach($act_ids_arr as $act_id){
             if(is_valid_act($act_id) && $act_id!="")
                  array_push($act_ids_track,$act_id);             
      }

      foreach($sub_act_ids_arr as $sub_id){
             if(is_valid_subact($sub_id) && $sub_id!="")
                  array_push($sub_act_ids_track,$sub_id);               
      }
     
    if(!empty($act_ids_track) && $act_ids_track[0]!="")
          $response["success_act"] = 1;

    if(!empty($sub_act_ids_track))
          $response["success_sub"] = 1;
     
     $response["act_ids"] = implode(",",$act_ids_track);
     $response["sub_ids"] = implode(",",$sub_act_ids_track); 
    } 
    echo json_encode($response);
?>
