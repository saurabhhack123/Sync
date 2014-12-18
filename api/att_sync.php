<?php

/* This file is used to recieve data from tab to server for attendance */
    
    error_reporting(0);
    ini_set('max_execution_time', 30000000);

    include_once "../../connection/connect.php";
    include_once "../helper/assist.php";

 /* initialize all variables */

    $syncall  = array();
    $response = array();
    $response["success"] = 0;
 
 /* recieve data from URI request */

    $json      = file_get_contents('php://input');
    $syncall   = json_decode($json,true);

    $tab_id    = $syncall['tab_id'];
    $section   = $syncall['section'];
    $date_att  = $syncall['date'];

    $school_id = get_school_id($tab_id);

 /* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

 /* preparing json data to send */
 
    if(!empty($syncall) && $is_valid_req)
    {     
                
     foreach($syncall['Sync'] as $syncpresent){
         
         $query      = $syncpresent['Query'];
         $action     = $syncpresent['Action'];
         $table_name = $syncpresent['TableName'];
         
         // making check for duplication handle 

         // add a check for duplicate entry for attendance 
         // if same tab send success as 1 and break the code flow 
         // if different tab , than send success as 2 and break the code flow
        
          $culprit_tab = "";

          $culprit_tab = is_att_dup($section,$date_att,$query,$school_id);
                    
          if($culprit_tab!=$tab_id && $culprit_tab!="")
          {
             $response["success"]=2;
             echo json_encode($response);
             die();
          }
          if($culprit_tab==$tab_id && $culprit_tab!="")
          {
             $response["success"]=1;
             echo json_encode($response);
             die();   
          }

         if($query!=""){

           insert_into_track_tab_data($tab_id,$school_id,$query,$action,$table_name);
           insert_row_into_validate_sync($school_id,$tab_id,$query);
           last_time_sync_record($school_id,$tab_id);

           if(in_array($table_name,array('exmavg','stavg')))
              {
                $valid_tabs = get_all_valid_tab($tab_id,$school_id);
                foreach ($valid_tabs as $tab){                  
                  insert_row_in_sync_table($school_id,$table_name,$action,$query,0,0,1,$tab);
                }
              }   
           else
             { exe_query($query,"Error in inserting absentees!");}
         }
        }
        $response["success"] = 1;
     }
     else
     {
      $response["success"]=0;
     }
    
    echo json_encode($response);
?>