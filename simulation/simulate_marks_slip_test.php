<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;

    //Db Connection
   connect_db($school_id); //School id is 58

   $sliptests = get_slip_tests($school_id);


   foreach ($sliptests as $sliptest) {
   	$student_ids = get_student_ids($sliptest['ClassId'],$sliptest['SectionId']);
   	insert_into_sliptest_58($school_id,$sliptest['ClassId'],$sliptest['SectionId'],$sliptest['SubjectId'],$sliptest['NewSubjectId'],$sliptest['SlipTestId'],$sliptest['MaximumMark'],$student_ids);
   	echo 'inserted....';
   }



   //helper function

   function get_slip_tests($school_id)
   {
   		$sql     = "select SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,MaximumMark from sliptest where SchoolId='$school_id' ";
	    $res     = mysql_query($sql) or die("Error in fetching sliptest details from sliptest Table".mysql_error());
	    $sliptest_details = array();

	    while($row = mysql_fetch_array($res)){
	        $sliptest_detail    = array();
	        $sliptest_detail 	=$row;
	        array_push($sliptest_details,$sliptest_detail);

	    }
	    return $sliptest_details; 
   }


   /*Function to get students id of that particular class*/

   function get_student_ids($class_id,$section_id)
   {

   		
  	    $sql     = "select StudentId from students where ClassId='$class_id' and SectionId='$section_id'";
	    $res     = mysql_query($sql) or die("Error in fetching StudentId from student Table".mysql_error());
	    $student_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $student_id    = $row["StudentId"];
	        array_push($student_ids,$student_id);
	    }
	    return $student_ids;    		
   }

   /*Function to insert into sliptest marks*/
   function insert_into_sliptest_58($school_id,$class_id,$section_id,$subject_id,$new_subject_id,$slip_test_id,$max_mark,$student_ids)
   {
   		foreach ($student_ids as $student_id) {
   			$mark = get_random_mark($max_mark);
   			$sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
   			mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());
   		}
   }

   /*Generate Random Number*/
   function get_random_mark($max_mark)
   {
   	 return rand(0,$max_mark);
   }


  ?>