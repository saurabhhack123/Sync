<?php
  error_reporting(E_ALL ^ E_NOTICE);
  ini_set('max_execution_time', 30000000);

  include_once "../../connection/connect.php";
  include_once "../helper/assist.php";

  $json = file_get_contents('php://input');
  $ackcall_json = array();
  $ackcall_json = json_decode($json,true);
  
/* This is for testing tab piloting for switching db dynamically */
    
  $school  = $ackcall_json["school"];
  $ackcall = array();
  $ackcall = $ackcall_json["ack"];
  $tab_id  = $ackcall_json["tab_id"];
  
  connect_db($school); 

  $response["success"] = 0;
  $response["remain"]  = 0;
  $response["remain"]  = remaining_records($tab_id);  
  last_time_sync_record($school,$tab_id);

  if(!empty($ackcall)){

      foreach($ackcall as $ack_map){
          update_ack_of_sync_mgt($ack_map["ack_id"]);
      }


   $response["success"] = 1;
  
  }
  
  echo json_encode($response);
 ?>