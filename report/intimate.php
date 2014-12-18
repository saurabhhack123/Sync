<?php

error_reporting(0);
include_once "../../connection/connect_new.php";
include_once "helpers.php";
include_once "../helper/assist.php";

// have to add fn later to fetch distinct tab using schls

$tab_using_schools = array('26'); 

$curr_date = date("Y-m-d");

foreach ($tab_using_schools as $school_id) {
	# code...
    
    // fetch classess
    $classes   = classes($school_id);
    
    foreach ($classes as $class_id) {
     	# code...

     	// fetch sections 
     	$sections = sections($class_id);

        foreach($sections as $section_id){
     		
            // fetch teacher ids

            $class_tch  = class_teacher($section_id);
            $tch_detail = array();
            $tch_msg = "";

            // check for att and Hw

            $is_hw    = is_uploaded_hw($curr_date,$section_id);
            $is_att   = is_uploaded_att($curr_date,$section_id);

            if(!$is_att || !$is_hw){
                // send sms 
                $tch_detail = get_teacher_detail($class_tch);

                $tch_name = $tch_detail["Name"];
                $tch_mob  = $tch_detail["Mobile"];
                $tch_id   = $tch_detail["tch_id"];

                $tch_msg  = "Dear ".$tch_name.", you haven't uploaded";
                
                if(!$is_att)
                   $tch_msg.=" attendance";

                if(!$is_hw && $is_att)
                   $tch_msg.= " Homework";

                if(!$is_hw && !$is_att)
                   $tch_msg.= " and Homework";

                $tch_msg.=" for today <$curr_date>";
                send_sms($school_id,$tch_mob,$tch_msg,$tch_id);
            }
     	}
    } 
}

?>