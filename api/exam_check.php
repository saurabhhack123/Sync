<?php
  /* This file is used to recieve data from tab to server except sliptest and Homework */
    error_reporting(0);
    ini_set('max_execution_time', 30000000);

    include_once "../../connection/connect.php";
    include_once "../helper/assist.php";
    
    function students($section_id){
     
       $sql = "select StudentId from students where SectionId='$section_id'";
       $res = mysql_query($sql) or die(mysql_error());

       $students = array();
       while($row = mysql_fetch_array($res)){
        array_push($students,$row["StudentId"]);
       }
       return $students;
    }

    function is_valid_mark($exam_id,$sub_id,$sec_id){
      
      $students  = students($sec_id);
      $students_str = implode(",",$students);

      $sql = "select * from marks where ExamId='$exam_id' and SubjectId='$sub_id' and StudentId in ($students_str)";
      $res = mysql_query($sql) or die(mysql_error());
      
      return mysql_num_rows($res) > 0 ?true:false;
    }

  /*initialize all variables */

    $syncall  = array();
    $response = array();
    $response["success"] = 1;
 
  /*recieve data from URI request */

    $json        = file_get_contents('php://input');
    $syncall     = json_decode($json,true);
     
    $exam_info   = $syncall["exam"];


   
  /* This is for testing tab piloting for switching db dynamically */
    
    $school      = $syncall['school'];
    $tab_id      = $syncall["tab_id"];
    
    connect_db($school);

    $school_id   = get_school_id($tab_id);

  /* validating tablet */
 
    $is_valid_req = is_tab_valid($tab_id);

  /* preparing json data to send */
    
    if(!empty($syncall) && $is_valid_req )
    { 
      $track_exam_map = array();

      foreach($exam_info as $exam_map){

           $exam_id = $exam_map["exam_id"];
           $sub_id  = $exam_map["sub_id"];
           $sec_id  = $exam_map["sec_id"];

           if(is_valid_mark($exam_id,$sub_id,$sec_id)){
             $mini_map = array();
             $mini_map["exam_id"] = $exam_id;
             $mini_map["sub_id"]  = $sub_id;
             $mini_map["sec_id"]  = $sec_id;
             $track_exam_map[] = $mini_map;
           }
      }

     $response["exam"] = $track_exam_map;
   }
    echo json_encode($response);
?>