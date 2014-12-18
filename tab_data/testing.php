<?php
  
    error_reporting(1);
    ini_set('max_execution_time', 30000000);
    
    include_once "../../connection/connect_new.php";
    include_once "../helper/assist.php";

    include_once "parse/parse_insert.php";
    include_once "parse/parse_update.php";
    include_once "parse/parse_delete.php";
    
    
    while(1){
    
    update_tm_script();

    $sql = "select * from `trigger` where `IsNew`=1" ;
    
    $res = mysql_query($sql) or die($sql."Error in fetching from trigger !".mysql_error());
  
    $tab_using_schools = all_school_ids();
    
    array_push($tab_using_schools,1);
    
    $filter_table = array("class","section","subjectteachers");
    
    while($row = mysql_fetch_array($res)){
         
         $trigger_id = $row['TriggerId'];
         echo "fetching from trigger".$trigger_id;

         $school_id  = $row['SchoolId'];
  
         // check if sync is enable 
         if(!is_sync_enable($school_id))
               { echo "sync is stopped !"; continue; }


         $table_name = $row['TableName'];
         $action     = $row['Action'];
         
         $query      = $row['Query'];
         // parse query based on action

         $parse_map = array();
         $is_complex = 0;


         if($action == "INSERT" || $action == "insert")
           { $parse_map = _parse_insert_query($query); 
             $is_complex = is_complex_qinsert($parse_map,$table_name);

             if(in_array($table_name,$filter_table))
               $is_complex = 1;
           }
         elseif ($action == "UPDATE" || $action == "update")
           { $parse_map = _parse_update_query($query);
             $is_complex = is_complex_qupdate($parse_map,$table_name);

             if(in_array($table_name,$filter_table))
               $is_complex = 1;
             
           }  
         else
           { $parse_map = _parse_delete_query($query); 
             $is_complex = is_complex_qdelete($parse_map);

             if(in_array($table_name,$filter_table))
               $is_complex = 1;
      
          }

         $created_at = $row['DateTimeRecordInserted'];

         $apply_filer = false;
         $apply_filer = query_filter($query);
                 
         if( !$apply_filer && in_array($school_id, $tab_using_schools) )
         {   
            if($school_id==1 && $table_name=="subjects"){

                $schools = all_school_ids();
                foreach ($schools as $sch_id) {

                      $tab_id = ignore_tab_for_sync($sch_id,$query,$created_at);
                      $tab_ids = array();
                      $tab_ids['valid'] = get_all_valid_tab($tab_id,$sch_id);

                     foreach($tab_ids['valid'] as $tab){
                         insert_row_in_sync_table($sch_id,$table_name,$action,$query,$parse_map,0,$is_complex,$tab);

                    }
              }
            }
            else{

                $tab_id = ignore_tab_for_sync($school_id,$query,$created_at);
                
                $tab_ids = array();
                $tab_ids['valid'] = get_all_valid_tab($tab_id,$school_id);

               foreach($tab_ids['valid'] as $tab){
                  insert_row_in_sync_table($school_id,$table_name,$action,$query,$parse_map,0,$is_complex,$tab);  
               }

             }
         } 
          update_is_new_flag($trigger_id);
    }



 $sql_1 = "select * from trigger_struc where `IsNew`=1";
 $res_1 = mysql_query($sql_1) or die("Error in fetching from trigger_struc".mysql_error());

     while($row_1 = mysql_fetch_array($res_1)){
         
         $trigger_id = $row_1['TriggerStrucId'];
         $school_id  = $row_1['SchoolId'];
         $table_name = $row_1['TableName'];
         $action     = $row_1['Action'];
         $query      = $row_1['Query'];
         $struc_id   = $row_1['StrucId'];
         $created_at = $row_1['DateTimeRecordInserted'];

         
         // check is sync is enable
         if(!is_sync_enable($school_id))
            continue;

                 
         if(in_array($school_id, $tab_using_schools) )
         {               
                $tab_id = ignore_tab_for_sync($school_id,$query,$created_at);
           
                $tab_ids = array();
                $tab_ids['valid'] = get_all_valid_tab($tab_id,$school_id);

               foreach($tab_ids['valid'] as $tab){
                  insert_row_in_sync_slip($school_id,$table_name,$action,$query,$struc_id,$tab);  
               }
             
         } 
          update_is_new_flag_trigger($trigger_id);
    
    }
}
   // end of for loop

?>


