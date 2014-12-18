<?php
  
  /* This file is used for homework acknowledgement */

  error_reporting(0);
  ini_set('max_execution_time', 30000000);
   
  include_once "../../connection/connect.php";
  include_once "../helper/assist.php";

  $json    = file_get_contents('php://input');
  $ackcall = array();
  $ackcall = json_decode($json,true);

  $response["success"] = 0;
  
  if(!empty($ackcall)){

      foreach($ackcall["ACK_IDS"] as $ids){
          update_ack_of_sync_slip($ids["ack_id"]);
      }
     $response["success"] = 1;  
  }
  echo json_encode($response);
?>