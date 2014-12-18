<?php

/* this file is used to recieve hw data from tab to server */
   error_reporting(0);
    
   include_once "../../connection/connect.php";
   $school = 16;
   connect_db($school);   

   // {"Sync":[{"TableName":"homeworkmessage","SectionId":1069,"date":"2014-08-07","Action":"insert","Query":"insert into homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIDs,Homework,HomeworkDate) values(161407406816840,16,133,1069,4795,'45','duplicate','2014-08-07')"}],"tab_id":"baf1018cd28add57","school":16}

   function look_up_culprit($query,$school){
      
      $last = strrpos($query,",");
      $second_last  = strrpos(substr($query,0,$last),",");
      $sub_encode_query = substr($query,0,$second_last);
      $encode_query = substr(mysql_real_escape_string($sub_encode_query),1);

      $sql = "select TabId from track_tab_data where Query LIKE '%$encode_query%' and SchoolId='$school' and TableName='homeworkmessage'";
      $res = mysql_query($sql) or die("Error in fetching from track_tab_data".mysql_error());
      $row = mysql_fetch_array($res);

      return $row["TabId"];
    }

    function is_present_hw($section_id,$hw_date,$query,$school){
      $sql = "select Homework from homeworkmessage where SectionId='$section_id' and HomeworkDate='$hw_date'";
             
      $res = mysql_query($sql) or die("Error in fetching from homeworkmessage".mysql_error());
      $row = mysql_num_rows($res);
      
      $culprit_tab = "";

      if($row > 0){
       // find the culprit now
      	echo "culprit is found!";
        $culprit_tab = look_up_culprit($query,$school);

      }
      return $culprit_tab;
    }
// {"Sync":[{"TableName":"homeworkmessage","SectionId":2597,"date":"2014-08-07","Action":"insert","Query":"insert into homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIDs,Homework,HomeworkDate) values(161407410479636,16,139,2597,4783,'44','dummy','2014-08-07')"}],"tab_id":"baf1018cd28add57","school":16}
// {"TableName":"homeworkmessage","SectionId":1070,"date":"2014-08-07","Action":"insert","Query":"insert into homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIDs,Homework,HomeworkDate) values(161407407731272,16,133,1070,4804,'44','dup','2014-08-07')"}],"tab_id":"baf1018cd28add57","school":16}  
    $section_id = 2597;
    $hw_date    = "2014-08-07"; 
    $query = "insert into homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIDs,Homework,HomeworkDate) values(161407410479636,16,139,2597,4783,'44','dummy','2014-08-07')";
    $school = 16;

    $culprit_tab = is_present_hw($section_id,$hw_date,$query,$school);
    echo $culprit_tab;

?>