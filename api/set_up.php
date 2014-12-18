<?php

 /** This file is used to create first time syncing for tab **/

    error_reporting(0);
    header('Content-Type: text/html; charset=utf-8');

    ini_set('max_execution_time', 30000000);

    include_once "../../connection/connect.php";
    include_once "../helper/assist.php";

    
 /*recieve data from URI request */    
 
    $tab_id     = $_REQUEST['tab_id'];
    $table_name = $_REQUEST['table_name'];
    $req_call   = $_REQUEST['req_call'];
    $school_id  = get_school_id($tab_id);

  /*initialize all variables */

    $syncall             = array();
    $response            = array();
    $map_rows            = array();
    $map_rows            = generate_map(); // mapping of offset to chunk size
    $response["success"] = 0;
   

/* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

 /* delete all the entries which are already present in sync_mgt and sync_slip */

    delete_prev_entries($school_id,$tab_id);

 /* preparing data to send */
    
    if(!empty($table_name) && $is_valid_req)
     {   
         last_time_sync_record($school_id,$tab_id);

         if($table_name=="subjects"){
             $sql = "select * from $table_name";
             $res = mysql_query($sql) or die("Error in fetching from $table_name".mysql_error());
             $response["req_again"]=0;
         }
         else{    
              $rows  = get_rows($table_name,$school_id);
             
              if($rows <= 5000){

                $res = fetch_rows($table_name,$school_id); 
                $response["req_again"] = 0;
              }else{
                $offset = $map_rows[$req_call];
                
                if($req_call >1) $offset+=($req_call-1);

                $res    = fetch_specific_rows($table_name,$school_id,$offset);
                if($rows > $map_rows[$req_call]+5000)  $response["req_again"] = $req_call+1;
                else  $response["req_again"] =0;
                
              }
         }  
          
         /** preparing json to send */  

         $cols = get_cols_of_table($table_name); 

         $date_table = array();
        
         while($row=mysql_fetch_array($res)){
           $temp = array();
           foreach ($cols as $col) {
                  $temp["$col"] = $row["$col"];  
           }
           $data_table[] = $temp;
         }   

          $response["Sync"] = $data_table;

          $response["success"] = 1;
     }
     
     echo json_encode($response,true); 
?>


