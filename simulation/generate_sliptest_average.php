<?php


   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;

    //Db Connection
   connect_db($school_id); //School id is 58

   $sliptest_ids = get_slip_tests($school_id);


   foreach ($sliptest_ids as $sliptest_id) {
   		$average_mark	=	get_average_slip_test_marks($sliptest_id);
   		update_slip_test($sliptest_id,$average_mark);
   		echo "Updated..";
   }

   echo "SUCCESS";


   function get_slip_tests($school_id)
   {
   		$sql     = "select SlipTestId from sliptest where SchoolId='$school_id' ";
	    $res     = mysql_query($sql) or die("Error in fetching sliptest details from sliptest Table".mysql_error());
	    $sliptest_ids = array();

	    while($row = mysql_fetch_array($res)){
	        
	        $sliptest_detail 	=$row['SlipTestId'];
	        array_push($sliptest_ids,$sliptest_detail);

	    }
	    return $sliptest_ids; 
   }

   function get_average_slip_test_marks($sliptest_id)
   {
   		$sql     = "select AVG(Mark) as average_mark from sliptestmark_58 where SlipTestId='$sliptest_id' ";
	    $res     = mysql_query($sql) or die("Error in fetching marks details from sliptestmark_58 Table".mysql_error());
	    $sliptest_marks = array();

	    /*while($row = mysql_fetch_array($res)){
	        
	        $sliptest_mark 	=$row['Mark'];
	        array_push($sliptest_marks,$sliptest_mark);

	    }
	    //var_dump($sliptest_marks);
	    $average_mark = array_sum($sliptest_marks)/count($sliptest_marks);*/
       $row = mysql_fetch_array($res);

	    return $row['average_mark']; 

   }


   function update_slip_test($sliptest_id,$average_mark)
   {
   		$sql = "update sliptest set AverageMark='$average_mark', MarkEntered=1 where SlipTestId='$sliptest_id'";
   	$res     = mysql_query($sql) or die("Error in uPDATING SLIPTESTMARK Table".mysql_error());

   }




   ?>