<?php

 error_reporting(0);
 ini_set('max_execution_time', 30000000);

 include_once "../../connection/connect.php";
 
 // db connect
 connect_db($school_id); 
 
 $school_id = 58;
 
 function is_act($exam_id){

 	$sql = "select * from activity where ExamId='$exam_id'";
 	$res = mysql_query($sql) or die(mysql_error());
 	if(mysql_num_rows($res)>0)
 		return true;
 	else
 		return false;
 }
 
 function max_mark_exam($exam_id,$subject_id){
      
      $sql = "select MaximumMark from subjectexams where ExamId='$exam_id' and SubjectId='$subject_id'";
      $res = mysql_query($sql) or die(mysql_error().$sql);
      $row = mysql_fetch_array($res);
      return $row["MaximumMark"];
 }

 function update_marks($exam_id,$student_id,$subject_id,$mark){
    
    $sql = "update marks set Mark='$mark' where ExamId='$exam_id' and SubjectId='$subject_id' and StudentId='$student_id'";
    echo $sql."<br>";
    mysql_query($sql) or die(mysql_error().$sql);

 }
 function update_marks_to_threshold($exam_id){
      
      $sql = "select Mark,StudentId,SubjectId from marks where ExamId='$exam_id'";
      $res = mysql_query($sql) or die(mysql_error().$sql);

      while($row = mysql_fetch_array($res)){ 
             $mark       = $row["Mark"];
             $student_id = $row["StudentId"];
             $subject_id = $row["SubjectId"]; 
             
             $max_mark    = max_mark_exam($exam_id,$subject_id);
             $max_mark_75 = round(0.75*$max_mark);
             $max_mark_50 = round(0.50*$max_mark);

             if($mark < $max_mark_75  && $mark >$max_mark_50 || $mark>$max_mark_75){
                  
                  $update_mark = ($max_mark_75 - $mark)/2;
                  echo "75*".$student_id."->".$mark."-".$max_mark."-".$update_mark."<br>";
                  update_marks($exam_id,$student_id,$subject_id,$mark+$update_mark);

             }elseif ($mark <= $max_mark_50) {
                  $update_mark = ($max_mark - $mark)/2;
                  echo "50*".$student_id."->".$mark."-".$max_mark."-".$update_mark."<br>";
                  update_marks($exam_id,$student_id,$subject_id,$mark+$update_mark);
             
             }elseif($mark > $max_mark) {
                  update_marks($exam_id,$student_id,$subject_id,$max_mark);
             }else{

             }
 }
}

 $sql = "select avg(Mark) as avg_mark,ExamId from marks where SchoolId='$school_id' and SubjectId NOT IN (-1,-2,-3) group by ExamId";
 $res = mysql_query($sql) or die("Error in fetching exma".$sql);
 
 $below_50_avg = array();

 while ($row = mysql_fetch_array($res)) {
     
    $avg_mark = round($row["avg_mark"],2); 
 	if($avg_mark < 50)
 	  {  array_push($below_50_avg,$row["ExamId"]);
 	 	 echo $row["ExamId"]."-".$avg_mark."<br>";
 	  }
 }


$filter_arr   = array_chunk($below_50_avg,count($below_50_avg)/2);
$sel_below_50 = $filter_arr[0];
var_dump($sel_below_50);

$exam_in_marks = array();

foreach ($sel_below_50 as $exam_id) {
   
   if(!is_act($exam_id))
   	   array_push($exam_in_marks,$exam_id);

}

foreach ($exam_in_marks as $exam_id) {
	# code...
    echo "----------------------------------------"."<br>";
    update_marks_to_threshold($exam_id);
    echo "----------------------------------------"."<br>";

}

 ?>