<?php

/* this file is used to recieve hw data from tab to server */
   error_reporting(0);
   ini_set('max_execution_time', 30000000);
    
   include_once "../../connection/connect.php";
   include_once "../helper/assist.php";
   
  /*initialize all variables */  

    $syncall = array();
    $response = array();

    $response["success"] = 0;

 /*recieve data from URI request */ 
 
    $json       = file_get_contents('php://input');
    $syncall    = json_decode($json,true);
 
  /*

 {"Sync":[{"TableName":"homeworkmessage","SectionId":2597,"HomeworkId":"161408348020981","date":"2014-08-18","Action":"insert","Query":"insert into homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIDs,Homework,HomeworkDate,IsNew) values(161408348020981,16,139,2597,4783,'53','na hw','2014-08-18',1)"}],"tab_id":"baf1018cd28add57","school":16}

  */
   
    $tab_id     = $syncall['tab_id'];
    $school     = $syncall['school'];
 
/* This is for testing tab piloting for switching db dynamically */

    connect_db($school);
    $school_id  = get_school_id($tab_id);

   /* validating tablet */

    $is_valid_req = is_tab_valid($tab_id);
 
   /* prepare json data to send */

     if((!empty($syncall) && $is_valid_req))
     {
        $hw_ids       = array();
        $sections_diff_tab = array();
        $sections_same_tab = array();

        foreach($syncall['Sync'] as $syncpresent){         
         
         $query      = $syncpresent['Query'];
         $action     = $syncpresent['Action'];
         $table_name = $syncpresent['TableName'];
         $section_id = $syncpresent['SectionId'];
         $hw_date    = $syncpresent['date'];
         $hw_id      = $syncpresent['HomeworkId'];

         // for response   

         $culprit_tab = "";
         $culprit_tab = is_present_hw($section_id,$hw_date,$query,$school);
   
         if($culprit_tab!=""){
            // if culprit found !
            // check if duplicate is coming from same tab or different

              if($culprit_tab!=$tab_id)
                 array_push($sections_diff_tab,$hw_id);
              else
                 array_push($sections_same_tab,$hw_id);            
        
          }else{

              $is_run = _run_query($query);
              array_push($hw_ids,$hw_id);

               if($culprit_tab!="")
                   $valid_tabs = get_all_tabs($school_id);
               else
                   $valid_tabs = get_all_valid_tab($tab_id,$school_id);

               foreach($valid_tabs as $tab){
                   if($tab==$tab_id)
                     continue;

                   if($is_run)
                       insert_into_sync_slip($school_id,$table_name,$action,$query,$query_id,0,$tab);
               }

              if($action=="insert" && $culprit_tab==""){
                     $suc=send_hw_sms_to_parents($school_id,$action,$query); 
              }
         }
         
         if($culprit_tab=="")            
            insert_into_track_tab_data($tab_id,$school_id,$query,$action,$table_name);
         
            insert_row_into_validate_sync($school_id,$tab_id,$query);
            last_time_sync_record($school_id,$tab_id);    
          }
          
          if(!empty($hw_ids))
            $response["sync_success"] = implode(",",$hw_ids);

          if(!empty($sections_same_tab))
            $response["sync_del"] = implode(",",$sections_same_tab);
          
          if(!empty($sections_diff_tab))
            $response["sync_dup"] = implode(",",$sections_diff_tab);

          $response["success"] = 1;
     }

   echo json_encode($response);

?>