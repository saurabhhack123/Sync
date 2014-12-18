<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;
   $cut_off = 8;
   $class_id = 548;

    //Db Connection
   connect_db($school_id); //School id is 58

   $sliptests = get_slip_tests($school_id,$class_id);


   foreach ($sliptests as $sliptest) {
   	
   	boost_sliptest_58($sliptest['SlipTestId'],$sliptest['MaximumMark'],$cut_off);
   	echo 'inserted....';
   }



   //helper function

   function get_slip_tests($school_id,$class_id)
   {
   		$sql     = "select SlipTestId,MaximumMark from sliptest where SchoolId='$school_id' and ClassId='$class_id' ";
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
   function boost_sliptest_58($slip_test_id,$max_mark,$cut_off)
   {


      $min_mark = ceil($cut_off*$max_mark/10);
      $sql = "select Mark,StudentId from sliptestmark_58 where SlipTestId='$slip_test_id'";
      $res     = mysql_query($sql) or die("Error in fetching marks details from marks Table".mysql_error());
      $marks_details = array();

       while($row = mysql_fetch_array($res)){
            $marks_detail =   array();
           $marks_detail    = $row;
           array_push($marks_details,$marks_detail);
       }
       

       foreach ($marks_details as $student_detail) {
         
         $mark = get_random_number($min_mark,$max_mark);
         $student_id = $student_detail['StudentId'];
          $sql = "update sliptestmark_58 set Mark='$mark' where SlipTestId='$slip_test_id' and StudentId='$student_id'";
          mysql_query($sql) or die("Error in Updating  Marks".mysql_error());
       }
   }

   /*Generate Random Number*/
   function get_random_number($min_mark,$max_mark)
   {
   	 return rand($min_mark,$max_mark);
   }


  ?>