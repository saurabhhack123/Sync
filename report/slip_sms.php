<?php

error_reporting(0);

include_once "../../connection/connect_new.php";     
include_once "helpers.php";

// have to add fn later to fetch distinct tab using schls

$tab_using_schools = array('26');

foreach ($tab_using_schools as $school_id) {
    # code...
    
    $curr_date = date("Y-m-d");
    // $date_prev = date('Y-m-d');
    $date_prev = date('Y-m-d',(strtotime ( '-8 day' , strtotime ($curr_date) ) ));
   
    $classes   = fetch_classes($school_id,$date_prev,$curr_date);

    foreach ($classes as $class_id) {
        # code...
        // fetch sections 

        $sections = array();
        $sections = fetch_sections($class_id,$date_prev,$curr_date);

        foreach($sections as $section_id){
            
            // get all students 
            
            $students      = get_student_ids($section_id);
 
            // fetch all the sliptest conduct in that duration

            $sliptest_info = fetch_slip_ids($section_id,$date_prev,$curr_date);

            // frame msg
         
            foreach($students as $student_id){
                # code...
                    // iterate over slip_info
                    // ex: English -Grammer-8/10-<Date Conducted>
                    $student_details = get_student_details($student_id);
                    
                    $slip_msg = ""; 
                    $slip_msg = "Evaluations for <$date_prev> to <$curr_date>";
                    
                    for($i=0;$i<count($sliptest_info);$i++){

                        $subject_name  = subject_name($sliptest_info[$i]["sub_id"]);
                        $slip_name     = $sliptest_info[$i]["slip_name"];
                        $portion_name  = $sliptest_info[$i]["portion"];
                        $date_cond     = $sliptest_info[$i]["test_date"];
                        $max_mark      = $sliptest_info[$i]["max_mark"];
                        $stu_slip_mark = fetch_stu_slip_mark($sliptest_info[$i]["slip_id"],$student_id,$school_id);      
                        
                        if($i>0)
                          $slip_msg.= " , ".$subject_name."-".$stu_slip_mark."/".$max_mark."<".$date_cond.">"; 
                        else
                          $slip_msg.= $subject_name."-".$stu_slip_mark."/".$max_mark."<".$date_cond.">"; 
                       }
                  
                 // send sms to student parents
                 if($student_details["Mobile1"]!="")
                   send_sms($school_id,$student_details["Mobile1"],$slip_msg,$student_id);
                 
                 if($student_details["Mobile2"]!="")
                   send_sms($school_id,$student_details["Mobile2"],$slip_msg,$student_id);
            
            } // end of foreach
        }// end of foreach
    }// end of foreach 
} // end of foreach

?>