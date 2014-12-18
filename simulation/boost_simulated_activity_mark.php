<?php
   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   echo "Hi";
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;
   //Db Connection
   connect_db($school_id); //School id is 58

   $class_ids = get_class_ids($school_id);

   foreach ($class_ids as $class_id) {   		
   		$exam_ids = get_exam_ids($class_id);
   		$section_ids = get_section_ids($class_id);
   		foreach ($exam_ids as $exam_id) {   			
   			$subject_ids = get_subject_ids($exam_id);
   			foreach ($section_ids as $section_id) {   				
   				//$student_ids = get_student_ids($class_id,$section_id);
   				foreach ($subject_ids as $subject_id) {   					
   					if(subject_has_activity($subject_id,$exam_id))
   					{		
   						$activity_ids = get_activity_ids($subject_id, $exam_id);   						
   						foreach ($activity_ids as $activity_id) {   						
	   						if(activity_has_sub_activity($activity_id))
	   						{	   							
	   							$sub_activity_ids = get_sub_activity_ids($activity_id);
	   							foreach ($sub_activity_ids as $sub_activity_id) {

                              $max_mark = get_max_mark_sub_activity($sub_activity_id);
                              boost_sub_activity_mark($sub_activity_id,$max_mark);
                              echo "Updated Sub Activity MArks...";
	   								//insert_into_sub_activity_mark($sub_activity_id,$activity_id,$school_id,$exam_id,$subject_id,$student_ids);
	   								//echo 'Inserted sub activity mark \n';	   								
	   							}
	   						}
	   						else
	   						{
                           $max_mark = get_max_mark_activity($activity_id);
                           boost_activity_mark($activity_id,$max_mark);
                           echo "Updated Activity Marks...";
	   							//insert_into_activity_mark($activity_id,$school_id,$exam_id,$subject_id,$student_ids);
	   							//echo 'Inserted activity mark \n';
	   						}
   						}
   					}
   					else
   					{
                        $max_mark = get_max_exam_mark($subject_id,$exam_id);
                        boost_exam_mark($subject_id,$exam_id,$max_mark);
                        echo "Updated Marks...";
   							//insert_into_mark($school_id,$exam_id,$subject_id,$student_ids);
   							//echo 'Inserted mark \n';
   					}
   				}
   			}
   		}
   }

echo "success";




//Helper functions


   /*Function to get class ids. It takes school id as parameter and returns class id array*/
   function get_class_ids($school_id)
   {
   		
	    $sql     = "select ClassId from class where SchoolId='$school_id'";
	    $res     = mysql_query($sql) or die("Error in fetching ClassId from Class Table".mysql_error());
	    $class_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $class    = $row["ClassId"];
	        array_push($class_ids,$class);
	    }
	    return $class_ids;
   }

   /*Function to get exam ids*/
   function get_exam_ids($class_id)
   {
   		
 	    $sql     = "select ExamId from exams where ClassId='$class_id'";
	    $res     = mysql_query($sql) or die("Error in fetching ExamId from exams Table".mysql_error());
	    $exam_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $exam_id    = $row["ExamId"];
	        array_push($exam_ids,$exam_id);
	    }
	    return $exam_ids;  	
   }

    /*Function to get section ids*/
   function get_section_ids($class_id)
   {
   		
 	    $sql     = "select SectionId from section where ClassId='$class_id'";
	    $res     = mysql_query($sql) or die("Error in fetching SectionId from section Table".mysql_error());
	    $section_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $section_id    = $row["SectionId"];
	        array_push($section_ids,$section_id);
	    }
	    return $section_ids;  	
   }
   /*function to get subject ids*/
   function get_subject_ids($exam_id)
   {
   		
 	    $sql     = "select SubjectId from subjectexams where ExamId='$exam_id'";
	    $res     = mysql_query($sql) or die("Error in fetching SubjectId from subjectexams Table".mysql_error());
	    $subject_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $subject_id    = $row["SubjectId"];
	        array_push($subject_ids,$subject_id);
	    }
	    return $subject_ids;  	
   }  

   /*Function to find whether a subject has activity*/
   function subject_has_activity($subject_id,$exam_id)
   {
   		
 	    $sql     = "select ActivityId from activity where ExamId='$exam_id' and SubjectId='$subject_id'";
	    $res     = mysql_query($sql) or die("Error in fetching ActivityId from activity Table".mysql_error());
	    $rows = mysql_num_rows($res);

	    if($rows > 0) 
	    	return true;
	    else          
	    	return false; 	
   }

   /*Function to find whether a activity has sub activity id*/
   function activity_has_sub_activity($activity_id)
   {
   		
 	    $sql     = "select SubActivityId from subactivity where ActivityId='$activity_id'";
	    $res     = mysql_query($sql) or die("Error in fetching subactivity from subactivity Table".mysql_error());
	    $rows = mysql_num_rows($res);
	    if($rows > 0) 
	    	return true;
	    else          
	    	return false;   
   }
   /*Function to get activity id*/
   function get_activity_ids($subject_id, $exam_id)
   {
   		
 	    $sql     = "select ActivityId from activity where ExamId='$exam_id' and SubjectId='$subject_id'";
	    $res     = mysql_query($sql) or die("Error in fetching ActivityId from activity Table".mysql_error());
	    $activity_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $activity_id    = $row["ActivityId"];
	        array_push($activity_ids,$activity_id);
	    }
	    return $activity_ids;  	
   }

    /*Function to get sub activity ids*/
   function get_sub_activity_ids($activity_id)
   {
   		
 	    $sql     = "select SubActivityId from subactivity where ActivityId='$activity_id'";
	    $res     = mysql_query($sql) or die("Error in fetching SubActivityId from subactivity Table".mysql_error());
	    $sub_activity_ids = array();

	    while($row = mysql_fetch_array($res)){
	        $sub_activity_id    = $row["SubActivityId"];
	        array_push($sub_activity_ids,$sub_activity_id);
	    }
	    return $sub_activity_ids;  	
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
   /*Insert into sub activity*/
   function insert_into_sub_activity_mark($sub_activity_id,$activity_id,$school_id,$exam_id,$subject_id,$student_ids)
   {
   		
   		
   		$max_mark = get_max_mark_sub_activity($sub_activity_id);
   		foreach ($student_ids as $student_id) {
   			$mark = get_random_mark($max_mark);
   			$sql = "INSERT INTO subactivitymark(SubActivityId,ActivityId,SubjectId,StudentId,SchoolId,ExamId,Mark) values('$sub_activity_id','$activity_id','$subject_id','$student_id','$school_id','$exam_id','$mark') "; 
   			mysql_query($sql) or die("Error in inserting marks into subactivitymark!");

   		}
   }
   /*Insert into activity*/
   function insert_into_activity_mark($activity_id,$school_id,$exam_id,$subject_id,$student_ids)
   {
   		
   		
   		$max_mark = get_max_mark_activity($activity_id);
   		
   		foreach ($student_ids as $student_id) {
   			$mark = get_random_mark($max_mark);
   			$sql = "INSERT INTO activitymark(ActivityId,SubjectId,StudentId,SchoolId,ExamId,Mark) values('$activity_id','$subject_id','$student_id','$school_id','$exam_id','$mark') "; 
   			mysql_query($sql) or die("Error in inserting marks into activitymark!");
   		}

   }
   /*Insert into exam*/
   function insert_into_mark($school_id,$exam_id,$subject_id,$student_ids)
   {
   		
   		$max_mark = get_max_exam_mark($subject_id,$exam_id);
   		foreach ($student_ids as $student_id) {
   			$mark = get_random_mark($max_mark);
   			$sql = "INSERT INTO marks(SubjectId,StudentId,SchoolId,ExamId,Mark) values('$subject_id','$student_id','$school_id','$exam_id','$mark') "; 
   			mysql_query($sql) or die("Error in inserting marks into exammark! '$subject_id','$student_id','$school_id','$exam_id','$mark' ");
   		}

   }
   /*Get Maximum Marks for exam*/
   function get_max_exam_mark($subject_id,$exam_id)
   {
 	    $sql     = "select MaximumMark from subjectexams where SubjectId='$subject_id' and ExamId='$exam_id'";
	    $res     = mysql_query($sql) or die("Error in fetching MaximumMark from subjectexams Table".mysql_error());
	    $row = mysql_fetch_array($res);

	    return $row['MaximumMark'];
   }
   /*Get Maximum Marks for activity*/
   function get_max_mark_activity($activity_id)
   {
   		
 	    $sql     = "select MaximumMark from activity where ActivityId='$activity_id'";
	    $res     = mysql_query($sql) or die("Error in fetching MaximumMark from ActivityId Table".mysql_error());
	    $row = mysql_fetch_array($res);

	    return $row['MaximumMark'];
   }
   /*Get Maximum Marks for sub activity*/
   function get_max_mark_sub_activity($sub_activity_id)
   {
   		
 	    $sql     = "select MaximumMark from subactivity where SubActivityId='$sub_activity_id'";
	    $res     = mysql_query($sql) or die("Error in fetching MaximumMark from subActivity Table".mysql_error());
	    $row = mysql_fetch_array($res);

	    return $row['MaximumMark'];

   }

   /*Generate Random Number*/
   function get_random_number($max_num)
   {
   	 return rand(1,$max_num);
   }

   /*Function to boost Mark */
   function boost_sub_activity_mark($sub_activity_id,$max_mark)
   {
      $boundary_mark = ceil($max_mark/2);
      $sql = "select Mark,StudentId from subactivitymark where Mark<'$boundary_mark' and SubActivityId='$sub_activity_id'";
      $res     = mysql_query($sql) or die("Error in fetching subactivity details from subactivitymark Table".mysql_error());
      $marks_details = array();

       while($row = mysql_fetch_array($res)){
            $marks_detail =   array();
           $marks_detail    = $row;
           array_push($marks_details,$marks_detail);
       }
       $no_of_students_below_average = count($marks_details);
       shuffle($marks_details); 
       $students_details_to_be_altered = array_slice($marks_details,0,get_random_number($no_of_students_below_average));

       foreach ($students_details_to_be_altered as $student_detail) {
         
         $mark = $student_detail['Mark'] + floor(($max_mark-$student_detail['Mark'])/get_random_number(3));
         $student_id = $student_detail['StudentId'];
          $sql = "update subactivitymark set Mark='$mark' where SubActivityId='$sub_activity_id' and StudentId='$student_id'";
          mysql_query($sql) or die("Error in Updating sub activity  Mark".mysql_error());
       }
   }


   function boost_activity_mark($activity_id,$max_mark)
   {
      $boundary_mark = ceil($max_mark/2);
      $sql = "select Mark,StudentId from activitymark where Mark<'$boundary_mark' and ActivityId='$activity_id'";
      $res     = mysql_query($sql) or die("Error in fetching activity details from activity Table".mysql_error());
      $marks_details = array();

       while($row = mysql_fetch_array($res)){
            $marks_detail =   array();
           $marks_detail    = $row;
           array_push($marks_details,$marks_detail);
       }
       $no_of_students_below_average = count($marks_details);
       shuffle($marks_details); 
       $students_details_to_be_altered = array_slice($marks_details,0,get_random_number($no_of_students_below_average));

       foreach ($students_details_to_be_altered as $student_detail) {
         
         $mark = $student_detail['Mark'] + floor(($max_mark-$student_detail['Mark'])/get_random_number(3));
         $student_id = $student_detail['StudentId'];
          $sql = "update activitymark set Mark='$mark' where ActivityId='$sub_activity_id' and StudentId='$student_id'";
          mysql_query($sql) or die("Error in Updating Activity Mark".mysql_error());
       }
   }


   function boost_exam_mark($subject_id,$exam_id,$max_mark)
   {
      $boundary_mark = ceil($max_mark/2);
      $sql = "select Mark,StudentId from marks where Mark<'$boundary_mark' and ExamId='$exam_id' and SubjectId='$subject_id'";
      $res     = mysql_query($sql) or die("Error in fetching marks details from marks Table".mysql_error());
      $marks_details = array();

       while($row = mysql_fetch_array($res)){
            $marks_detail =   array();
           $marks_detail    = $row;
           array_push($marks_details,$marks_detail);
       }
       $no_of_students_below_average = count($marks_details);
       shuffle($marks_details); 
       $students_details_to_be_altered = array_slice($marks_details,0,get_random_number($no_of_students_below_average));

       foreach ($students_details_to_be_altered as $student_detail) {
         
         $mark = $student_detail['Mark'] + floor(($max_mark-$student_detail['Mark'])/get_random_number(3));
         $student_id = $student_detail['StudentId'];
          $sql = "update marks set Mark='$mark' where SubjectId='$subject_id' and StudentId='$student_id' and ExamId='$exam_id'";
          mysql_query($sql) or die("Error in Updating  Marks".mysql_error());
       }
   }
 ?>