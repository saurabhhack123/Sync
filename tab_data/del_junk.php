<?php

/** 
Code by : Saurabh
This file is used to delete the entries from tab queue tables
**/

error_reporting(0);

include_once "../../connection/connect_new.php";


class DelJunk{      
     
     function _run_query($query,$msg){
        $res = mysql_query($query) or die($msg."<-->".mysql_error());
        if (!$res){
          return false;
        }
        return $res;
     }

     function exe_query($query,$msg=""){
        mysql_query($query) or die($msg."<-->".mysql_error());
     }

     function delete_track_tab_data($track_id){
    
        $sql  ="delete from track_tab_data where TrackId='$track_id'";
        $this->exe_query($sql,"error in deleting from track_tab_data".$sql);
     }

    
     function delete_validate_sync($val_sync_id){
        
        $sql  ="delete from validate_sync where Validate_Sync_Id='$val_sync_id'";
        $this->exe_query($sql,"error in deleting from validate_sync".$sql);
     }


     function delete_trigger(){
      
        $sql  ="delete from trigger where IsNew=0";
        $this->exe_query($sql,"error in deleting from trigger".$sql);
    }
    

     function delete_trigger_struc(){ 
      
        $sql  ="delete from trigger_struc where IsNew=0";
        $this->exe_query($sql,"error in deleting from trigger_struc".$sql);
     }

     
     function delete_sync_mgt(){
   
        $sql  ="delete from sync_mgt where IsAck=1";
        $this->exe_query($sql,"error in deleting from sync_mgt".$sql);
     }
   
    
    function delete_sync_slip(){
      
        $sql  ="delete from sync_slip where IsAck=1";
        $this->exe_query($sql,"error in deleting from sync_slip".$sql);
   }
      
    
    function is_in_trigger($query){
        
      $encode_query = mysql_real_escape_string($query);
     
      $sql  = "select TriggerId from `trigger` where IsNew=0 and Query='$encode_query' ";
    
      $res  = $this->_run_query($sql,"error in fetching from trigger".$sql);
      
      return mysql_num_rows($res)>0?true:false;
     }

    function is_in_trigger_struc($query){
          
      $encode_query = mysql_real_escape_string($query);

      $sql  = "select TriggerStrucId from trigger_struc where IsNew=0 and Query='$encode_query'";
      $res  = $this->_run_query($sql,"error in fetching from trigger_struc".$sql);
      return mysql_num_rows($res)>0?true:false;
    }
  
    function archive_sync_mgt(){
    
      $sql="select * from sync_mgt where IsAck=1";
      $res = $this->_run_query($sql,"error in fetching from sync_mgt");

        while($row = mysql_fetch_array($res)){

        $mgt_id     = $row['Sync_Mgt_Id'];
        $school_id  = $row['SchoolId'];
        $table_name = $row['TableName'];
        $action     = $row['Action'];
        $query      = $row['Query'];
        $map        = $row['Map'];
        $is_ack     = $row['IsAck'];
        $is_complex = $row['Is_complex'];
        $tab_id     = $row['TabId'];  
        $date_time  = $row['DateTimeRecordInserted'];

        $encode_query = mysql_real_escape_string($query);


        $sql="INSERT INTO sync_mgt_archive(Sync_Mgt_Id,SchoolId,TableName,Action,Query,Map,IsAck,Is_complex,TabId,DateTimeRecordInserted) VALUES ('$mgt_id','$school_id','$table_name','$action','$encode_query','$map','$is_ack','$is_complex','$tab_id','$date_time')";
        $this->exe_query($sql,"Error in inserting into sync_mgt_archive!");
      }
    }

     function archive_sync_slip(){

     $sql="select * from sync_slip where IsAck=1";
     $res = $this->_run_query($sql,"error in fetching from sync_slip");

        while($row = mysql_fetch_array($res)){

        $syc_slip_id = $row['SyncSlipId'];
        $school_id   = $row['SchoolId'];
        $table_name  = $row['TableName'];
        $action      = $row['Action'];
        $query       = $row['Query'];
        $slip_id     = $row['SlipId'];
        $is_ack      = $row['IsAck'];
        $tab_id      = $row['TabId']; 
        $date_time   = $row['DateTimeRecordInserted'];
        
        $encode_query = mysql_real_escape_string($query);

        $sql="INSERT INTO sync_slip_archive (SyncSlipId,SchoolId,TableName,Action,Query,SlipId,IsAck,TabId,DateTimeRecordInserted) VALUES ('$syc_slip_id','$school_id','$table_name','$action','$encode_query','$slip_id',$is_ack','$tab_id','$date_time')";
        $this->exe_query($sql,"Error in inserting into sync_slip_archive!");
        }
    }
 

   function archive_track_tab_data(){  
      
      $sql  ="select * from track_tab_data";
      $res  = $this->_run_query($sql,"error in fetching from track_tab_data");

      while($row = mysql_fetch_array($res)){
        
        $flag = false;
        
        $track_id   = $row['TrackId'];
        $query      = $row['Query'];
        $tab_id     = $row['TabId'];
        $school_id  = $row['SchoolId'];
        $table_name = $row['TableName'];
        $action     = $row['Action'];
        $created_at = $row['CreatedAt'];

        $flag = ($this->is_in_trigger($query) || $this->is_in_trigger_struc($query));  

        if($flag)
        { 
         $encode_query = mysql_real_escape_string($query);          
         
         $sql="INSERT INTO archive_track_tab_data (TrackId,TabId,SchoolId,Action,Query,TableName,CreatedAt) VALUES ('$track_id','$tab_id','$school_id','$action','$encode_query','$table_name','$created_at')";
         $this->exe_query($sql,"Error in inserting into archive_track_tab_data!");
         $this->delete_track_tab_data($track_id);
        }
      }
    }
  
   function archive_validate_sync(){  
    
      $sql  = "select * from validate_sync";
      $res  = $this->_run_query($sql,"error in fetching from track_tab_data");

      while($row = mysql_fetch_array($res)){
      
        $val_sync_id = $row['Validate_Sync_Id'];
        $school_id   = $row['SchoolId'];
        $tab_id      = $row['TabId'];
        $query       = $row['Query'];
        $date_time   = $row['DateTimeRecordInserted'];
      
        $flag = ($this->is_in_trigger($query) || $this->is_in_trigger_struc($query));  

        if($flag)
        {

         $encode_query = mysql_real_escape_string($query); 
         $sql="INSERT INTO archive_validate_sync(Validate_Sync_Id,SchoolId,TabId,Query,DateTimeRecordInserted) VALUES ('$val_sync_id','$school_id','$tab_id','$encode_query','$date_time')";
        
         $this->exe_query($sql,"Error in inserting into archive_validate_sync!");
         $this->delete_validate_sync($val_sync_id);
        }
       }
    }
}

  /* creating instance of the class and calling the function */



// have to add fn later to fetch distinct tab using schls

 $tab_using_schools = array('26'); 
 
 foreach ($tab_using_schools as $school_id) {
    # code...
   
  $sweep = new DelJunk();

  $sweep->archive_track_tab_data();
  $sweep->archive_validate_sync();

  $sweep->delete_trigger();
  $sweep->delete_trigger_struc();
  
  $sweep->archive_sync_mgt();
  $sweep->archive_sync_slip();
  
  $sweep->delete_sync_mgt();
  $sweep->delete_sync_slip();

 } 


?>
