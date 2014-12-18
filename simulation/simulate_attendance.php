<?php
   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;
   $type_of_leave = 'A';
   $start_date = $current_day = strtotime('2014-12-10');
   $end_date = strtotime('2015-01-20');
   $excluded_class_ids=array(1449,1450,1453,1456,1459,1483,1473,1461,1463,1466,1469);


   //Db Connection
   connect_db($school_id); //School id is 58

   $class_ids = get_class_ids($school_id);

   foreach ($class_ids as $class_id) {   		
   		
   		$section_ids = get_section_ids($class_id);
         foreach ($section_ids as $section_id) {

            if(in_array($section_id, $excluded_class_ids))
                continue;
            $student_ids = get_student_ids($class_id,$section_id);
            while($current_day < $end_date)
            {
                 $date=date('Y-m-d', $current_day);
                 $current_day = strtotime("+1 day", $current_day);
                 $day=date('l',$current_day);
                
                 if(strcmp($day, "Sunday") != 0)
                 {
                     insert_attendance($student_ids,$class_id,$section_id,$school_id,$date,$type_of_leave);
                     echo "Inserted  ..";
                 }
                 
            }
                  
            $current_day = strtotime('2014-12-10');
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
   function insert_attendance($student_ids,$class_id,$section_id,$school_id,$date,$type_of_leave)
   {
      $no_of_absentees = get_random_absentees_no();
      shuffle($student_ids);
      $absentees_array = array_slice($student_ids, 0, $no_of_absentees);
      foreach ($absentees_array as $absentee) {
            
            $sql = "INSERT INTO studentattendance(StudentId,SchoolId,ClassId,SectionId,TypeOfLeave,DateAttendance) values('$absentee','$school_id','$class_id','$section_id','$type_of_leave','$date') "; 
            mysql_query($sql) or die("Error in inserting Attendance! ");
         }

   }
   function get_random_absentees_no()
   {
      return rand(0,10);
   }


 ?>