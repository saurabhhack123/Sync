<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;

    //Db Connection
   connect_db($school_id); //School id is 58


   $class_ids=get_class_ids($school_id);
   $is_new = 1;
   $start_date = $current_day = strtotime('2014-08-01');
   $end_date = strtotime('2015-03-20');
   $homework_id = 161414134934256;
	$list_of_message = array('Read Chapter 1','Prepare for Speech Competition','Think one innovation','Find out New words introduced in Chapter 8','Complete Taks to be done section','Simulate a object related to subject','Find out the answers for given question paper','Prepare a Skit Related to subject ','Prepare a chart to Explain the assigned section','Prepare for Seminar','Copy Question and Answer Section from  text book for unit 5','Prepare for Quiz','Complete given assignment within two days','Write Assignment No Three','Learn New Innovation Related to Subject','Bring Text Book','Create a Presentation on Your Favorite topic related to subject','Be Prepared for SlipTest','Exam Portion has been changed');

	   foreach ($class_ids as $class_id) {

	   		$section_ids=get_section_ids($class_id);
	   		$subject_ids=get_subject_ids($class_id);
	   		foreach ($section_ids as $section_id) {
	   			shuffle($subject_ids);
		   		shuffle($list_of_message);
		   		$subject_count = count($subject_ids);
		   		$homework_number = get_random_number($subject_count);
		   		$subjects_for_which_homework_should_be_inserted = array_slice($subject_ids,0,$homework_number);
		   		$homework_subjects = implode(",", $subjects_for_which_homework_should_be_inserted);
		   		$homework_messages_to_be_inserted = array_slice($list_of_message, 0,$homework_number);
		   		$homework_messages = implode("#", $homework_messages_to_be_inserted);
		   		$teacher_id	=	get_teacher_id($class_id,$section_id);
		   		while($current_day < $end_date)
	            {
	            		shuffle($subject_ids);
				   		shuffle($list_of_message);
				   		$subject_count = count($subject_ids);
				   		$homework_number = get_random_number($subject_count);
				   		$subjects_for_which_homework_should_be_inserted = array_slice($subject_ids,0,$homework_number);
				   		$homework_subjects = implode(",", $subjects_for_which_homework_should_be_inserted);
				   		$homework_messages_to_be_inserted = array_slice($list_of_message, 0,$homework_number);
				   		$homework_messages = implode("#", $homework_messages_to_be_inserted);
		                $date=date('Y-m-d', $current_day);
		                $current_day = strtotime("+1 day", $current_day);
		                $day=date('l',$current_day);
		                
		                 if(strcmp($day, "Sunday") != 0)
		                 {
		                 	//echo $date;
		                     insert_homework($homework_id,$school_id,$class_id,$section_id,$teacher_id,$homework_subjects,$homework_messages,$is_new,$date);
		                     $homework_id++;
		                     var_dump($homework_id);
		                     echo "Inserted  ..";
		                 }
	                 
	            }
	            $current_day = strtotime('2014-08-01');
	   		}
	   		


	   		
	   		
	   }


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
   function get_subject_ids($class_id)
   {
   		
 	    $sql     = "select DISTINCT SubjectId from subjectteachers where ClassId='$class_id'";
	    $res     = mysql_query($sql) or die("Error in fetching SubjectId from subjectexams Table".mysql_error());
	    $subject_ids = array();

	    while($row = mysql_fetch_array($res)){
	    	
	        $subject_id    = $row["SubjectId"];
	        array_push($subject_ids,$subject_id);
	    }
	    return $subject_ids;  	
   }  



   function get_teacher_id($class_id,$section_id)
   {
   		
 	    $sql     = "select ClassTeacherId from section where ClassId='$class_id' and SectionId='$section_id'";
	    $res     = mysql_query($sql) or die("Error in fetching ClassTeacherId from Section Table".mysql_error());
	    $row = mysql_fetch_array($res);
	    //var_dump($row);
	    return $row['ClassTeacherId'];
   }
   function insert_homework($homework_id,$school_id,$class_id,$section_id,$teacher_id,$homework_subjects,$homework_messages,$is_new,$date)
   {
            $sql = "INSERT INTO homeworkmessage(HomeworkId,SchoolId,ClassId,SectionId,TeacherId,SubjectIds,Homework,IsNew,HomeworkDate) 
            values('$homework_id','$school_id','$class_id','$section_id','$teacher_id','$homework_subjects','$homework_messages','$is_new','$date') "; 
            mysql_query($sql) or die("Error in inserting homework! '$homework_id',$school_id','$class_id','$section_id','$teacher_id','$homework_subjects','$homework_messages','$is_new','$date'".mysql_error());
   }
   function get_random_number($max)
   {
   		return rand(1,$max);
   }



?>