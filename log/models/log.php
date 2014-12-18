<?php

 include_once "../../../connection/connect_log.php"; 

 /**
   Class to show Logs
 */
   
  class Log
  {
  	var $logs;

  	function __construct($from,$to)
  	{
        $sql = "select * from `logged` where DATETIME >='$from' and DATETIME<='$to'";
        $res = $this->exe_query($sql);
        $i=0;
        while($row = mysql_fetch_array($res)){
          
          $this->logs[$i]["STACK_TRACE"] = $row["STACK_TRACE"];
          $this->logs[$i]["LOGCAT"] = $row["LOGCAT"];
          $this->logs[$i++]["USER_CRASH_DATE"]=$row["USER_CRASH_DATE"];
        }
  	}
   
   function exe_query($sql){
      $res = mysql_query($sql);
      if(!$res) die("Error in sql ".$sql);
      else return $res;
   }
   
  }
 
?>