<?php

//This script is to revert back changes done by simulate data
   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

 
   include_once "../../connection/connect.php";
     $school_id	=	58;
   //Db Connection
   connect_db($school_id);
   $sql="delete from marks where SchoolId=58";
   mysql_query($sql) or die("Error Deleting marks");
      $sql="delete from activitymark where SchoolId=58";
   mysql_query($sql) or die("Error Deleting marks");
      $sql="delete from subactivitymark where SchoolId=58";
   mysql_query($sql) or die("Error Deleting marks");

   echo "success";

   ?>