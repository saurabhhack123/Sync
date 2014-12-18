<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;
   $class_id   =  556;

    //Db Connection
   connect_db($school_id); //School id is 58
insert_good_marks(58,777);
insert_good_marks(58,551);
insert_good_marks(58,776);
insert_good_marks(58,778);
insert_good_marks(58,557);
insert_good_marks(58,555);
insert_good_marks(58,558);
insert_good_marks(58,550);
insert_bad_marks(58,553);
insert_bad_marks(58,547);
insert_bad_marks(58,779);
insert_bad_marks(58,552);
insert_average_marks(58,554);
insert_average_marks(58,546);
insert_average_marks(58,548);
insert_average_marks(58,549);
echo "Complete";

function insert_good_marks($school_id,$class_id)
{
   $sliptests = get_slip_tests($school_id,$class_id);


   foreach ($sliptests as $sliptest) {
   	$student_ids = get_student_ids($sliptest['ClassId'],$sliptest['SectionId']);
   	insert_into_sliptest_58_with_good_marks($school_id,$sliptest['ClassId'],$sliptest['SectionId'],$sliptest['SubjectId'],$sliptest['NewSubjectId'],$sliptest['SlipTestId'],$sliptest['MaximumMark'],$student_ids);
   	echo 'inserted....';
   }
}

function insert_average_marks($school_id,$class_id)
{
   $sliptests = get_slip_tests($school_id,$class_id);


   foreach ($sliptests as $sliptest) {
      $student_ids = get_student_ids($sliptest['ClassId'],$sliptest['SectionId']);
      insert_into_sliptest_58_with_average_marks($school_id,$sliptest['ClassId'],$sliptest['SectionId'],$sliptest['SubjectId'],$sliptest['NewSubjectId'],$sliptest['SlipTestId'],$sliptest['MaximumMark'],$student_ids);
      echo 'inserted....';
   }
}

function insert_bad_marks($school_id,$class_id)
{
   $sliptests = get_slip_tests($school_id,$class_id);


   foreach ($sliptests as $sliptest) {
      $student_ids = get_student_ids($sliptest['ClassId'],$sliptest['SectionId']);
      insert_into_sliptest_58_with_bad_marks($school_id,$sliptest['ClassId'],$sliptest['SectionId'],$sliptest['SubjectId'],$sliptest['NewSubjectId'],$sliptest['SlipTestId'],$sliptest['MaximumMark'],$student_ids);
      echo 'inserted....';
   }
}



   //helper function

   function get_slip_tests($school_id,$class_id)
   {
   		$sql     = "select SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,MaximumMark from sliptest where SchoolId='$school_id' and ClassId='$class_id'";
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
   function insert_into_sliptest_58_with_good_marks($school_id,$class_id,$section_id,$subject_id,$new_subject_id,$slip_test_id,$max_mark,$student_ids)
   {
         $students_count = count($student_ids);
         shuffle($student_ids);
         $first_level_of_students   =  ceil(6*$students_count/10);
         $second_level_of_students = ceil(3*$students_count/10);
         for($i=0;$i<$first_level_of_students;$i++)
         {
            $min_limit=floor(7*$max_mark/10);
            $max_limit=$max_mark;
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$first_level_of_students;$i<$second_level_of_students;$i++)
         {
            $min_limit=floor(6*$max_mark/10);
            $max_limit=ceil(8*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$second_level_of_students;$i<$students_count;$i++)
         {
            $min_limit=floor(3*$max_mark/10);
            $max_limit=ceil(6*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
   		
   }



   function insert_into_sliptest_58_with_average_marks($school_id,$class_id,$section_id,$subject_id,$new_subject_id,$slip_test_id,$max_mark,$student_ids)
   {
         $students_count = count($student_ids);
         shuffle($student_ids);
         $first_level_of_students   =  ceil(3*$students_count/10);
         $second_level_of_students = ceil(5*$students_count/10);
         for($i=0;$i<$first_level_of_students;$i++)
         {
            $min_limit=floor(7*$max_mark/10);
            $max_limit=$max_mark;
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$first_level_of_students;$i<$second_level_of_students;$i++)
         {
            $min_limit=floor(6*$max_mark/10);
            $max_limit=ceil(8*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$second_level_of_students;$i<$students_count;$i++)
         {
            $min_limit=floor(3*$max_mark/10);
            $max_limit=ceil(6*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         
   }

   function insert_into_sliptest_58_with_bad_marks($school_id,$class_id,$section_id,$subject_id,$new_subject_id,$slip_test_id,$max_mark,$student_ids)
   {
         $students_count = count($student_ids);
         shuffle($student_ids);
         $first_level_of_students   =  ceil(3*$students_count/10);
         $second_level_of_students = ceil(6*$students_count/10);
         for($i=0;$i<$first_level_of_students;$i++)
         {
            $min_limit=floor(4*$max_mark/10);
            $max_limit=ceil(8*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$first_level_of_students;$i<$second_level_of_students;$i++)
         {
            $min_limit=floor(2*$max_mark/10);
            $max_limit=ceil(5*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         for($i=$second_level_of_students;$i<$students_count;$i++)
         {
            $min_limit=floor(7*$max_mark/10);
            $max_limit=ceil(10*$max_mark/10);
            $student_id=$student_ids[$i];
            $mark = get_random_mark($min_limit,$max_limit);
            $sql = "INSERT INTO sliptestmark_58(SchoolId,ClassId,SectionId,SubjectId,NewSubjectId,SlipTestId,StudentId,Mark) values('$school_id','$class_id','$section_id','$subject_id','$new_subject_id','$slip_test_id','$student_id','$mark') "; 
            mysql_query($sql) or die("Error in inserting marks into sliptestmark_58".mysql_error());         
         }
         
   }

   /*Generate Random Number*/
   function get_random_mark($min_mark,$max_mark)
   {
   	 return rand($min_mark,$max_mark);
   }


  ?>