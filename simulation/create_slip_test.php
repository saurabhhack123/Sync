<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;

    //Db Connection
   connect_db($school_id); //School id is 58


   $class_ids=get_class_ids($school_id);


   $max_mark=array(10,15,20,25);
   $slip_test_count=array(25,26,27,28,29,30,31,32,33,34,35);


   $start_date = $current_day = strtotime('2014-06-01');
   $end_date = strtotime('2015-04-30');

   $slip_test_name = '';
   $is_activity	=	0;
   $grade = 0;
   $count = 0;
   $new_subject_id = 0;
   $extra_portion	=	0;
   $average_mark = 0;
   $test_date = '2014-10-10';
   $mark_entered = 0;
   $employee_id = 0;
   $weightage = 0;
   $class_details_array = array();
   $class_details_count=0;

   foreach ($class_ids as $class_id) {

   		$section_ids=get_section_ids($class_id);
   		$subject_ids=get_subject_ids($class_id);
   		
   		foreach ($subject_ids as $subject_id) {

   			$portion_details = get_portion_details($school_id,$class_id,$subject_id);
   			
   			$no_of_portions = count($portion_details);
   			
   			if($no_of_portions>0)
   			{
	   			foreach ($section_ids as $section_id) {
	   				//$selected_portion = rand(0,$no_of_portions);
	   				//shuffle($max_mark);
	   				//insert_into_portion($school_id,$class_id,$section_id,$slip_test_name,$is_activity,$grade,$count,$subject_id,$new_subject_id,$portion_details[$selected_portion]['PortionId'],$extra_portion,$portion_details[$selected_portion]['Portion'],$max_mark[0],$average_mark,$test_date,$mark_entered,$employee_id,$weightage);
	   				//echo 'inserted';
                  $class_details_array[$class_details_count] = array('school_id'=>$school_id,'class_id'=>$class_id,'section_id'=>$section_id,'subject_id'=>$subject_id);
                  $class_details_count++;
	   			}
   			}
   		}
   }
   


   while($current_day < $end_date)
   {
      $date=date('Y-m-d', $current_day);
       $current_day = strtotime("+1 day", $current_day);
       $day=date('l',$current_day);
       if(strcmp($day, "Sunday") != 0)
        {
            shuffle($class_details_array);
            shuffle($slip_test_count);
            $class_selected_for_slip_test = array_slice($class_details_array, 0,$slip_test_count[0]);
            foreach ($class_selected_for_slip_test as $data) {
                        $portion_details = get_portion_details($data['school_id'],$data['class_id'],$data['subject_id']);
                        $no_of_portions = count($portion_details);
                        $selected_portion = rand(0,$no_of_portions);
                        shuffle($max_mark);
                        insert_into_sliptest($data['school_id'],$data['class_id'],$data['section_id'],$slip_test_name,$is_activity,$grade,$count,$data['subject_id'],$new_subject_id,$portion_details[$selected_portion]['PortionId'],$extra_portion,$portion_details[$selected_portion]['Portion'],$max_mark[0],$average_mark,$date,$mark_entered,$employee_id,$weightage);

                      } 
                      echo "********inserted*********";         
            
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
   		
 	    $sql     = "select DISTINCT SubjectId from subjectexams where ClassId='$class_id'";
	    $res     = mysql_query($sql) or die("Error in fetching SubjectId from subjectexams Table".mysql_error());
	    $subject_ids = array();

	    while($row = mysql_fetch_array($res)){
	    	
	        $subject_id    = $row["SubjectId"];
	        array_push($subject_ids,$subject_id);
	    }
	    return $subject_ids;  	
   }  

   /*Function to get  portion name and portion id*/


   function get_portion_details($school_id,$class_id,$subject_id)
   {
   		
   		
 	    $sql     = "select PortionId,Portion from portion where SchoolId='$school_id' and ClassId='$class_id' and SubjectId='$subject_id'";
	    $res     = mysql_query($sql) or die("Error in fetching portion details from portion Table".mysql_error());
	    $portion_details = array();

	    while($row = mysql_fetch_array($res)){
	        $portion_detail    = array();
	        $portion_detail 	=$row;
	        array_push($portion_details,$portion_detail);

	    }
	    return $portion_details; 

   }


   /*insert into sliptest*/
   function insert_into_sliptest($school_id,$class_id,$section_id,$slip_test_name,$is_activity,$grade,$count,$subject_id,$new_subject_id,$portion_id,$extra_portion,$portion_name,$max_mark,$average_mark,$test_date,$mark_entered,$employee_id,$weightage)
   {
   		    $sql = "INSERT INTO sliptest(SchoolId,ClassId,SectionId,SlipTestName,IsActivity,Grade,Count,SubjectId,NewSubjectId,Portion,ExtraPortion,PortionName,MaximumMark,AverageMark,TestDate,MarkEntered,EmployeeId,Weightage) 
   		    values('$school_id','$class_id','$section_id','$slip_test_name','$is_activity','$grade','$count','$subject_id','$new_subject_id','$portion_id','$extra_portion','$portion_name','$max_mark','$average_mark','$test_date','$mark_entered','$employee_id','$weightage') "; 
            mysql_query($sql) or die("Error in inserting sliptest! ");
   }

?>