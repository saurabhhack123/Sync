<?php



   error_reporting(0);
   ini_set('max_execution_time', 30000000);
   

   
   include_once "../../connection/connect.php";
   //include_once "../helper/assist.php";
   
   $school_id	=	58;

    //Db Connection
   connect_db($school_id); //School id is 58

$portion = array();
$portion[44]=array('Thirikadukam','Kanthiyadikal Kaditham','Puranaanuru','Ilamail Periyaar Ketta Vina','kanpom padipom');
$portion[45]=array('SHOWING GRATITUDE','BEING PERSEVERANT','HELPING ONES OWN','EATING HEALTH','DEALING WITH DISAPPOINTMENT');
$portion[46]=array('Probability','Problem Solving','Graphs','Geometry','Average');
$portion[47]=array('Sound','Pressure','Force','Pollution','Light');
$portion[48]=array('Markets Around Us','Advertising','Media','Discovery','Democracy');
$portion[49]=array('Food','Family','Transport','Neighbour','Festival');
$portion[51]=array('Internet','History of Computers','Software','Hardware','Programming Languages');
$portion[70]=array();
$portion[88]=array();
$portion[89]=array();
$portion[94]=array();
$portion[95]=array();
$portion[97]=array('Accounts','Budget','Economics','Tax','Final Analysis');
$portion[98]=array('Demand','Supply','Profit and Loss','Cost and Revenue','Production');
$portion[99]=array('Insurance','Warehouse','PartnerShip','Internal Trade','Fiscal');
$portion[107]=array();
$portion[121]=array('Think Logical Think Right','Sudoku','Bird World','Women Power','Adventure');
$portion[138]=array();
$portion[140]=array();
$portion[145]=array();
$portion[146]=array();
$portion[148]=array();
$portion[151]=array();
$portion[152]=array();
$portion[153]=array();
$portion[154]=array();
$portion[155]=array();
$portion[156]=array();
$portion[157]=array();
$portion[158]=array();
$portion[159]=array('Demand','Supply','Profit and Loss','Cost and Revenue','Production');
$portion[160]=array('Poem1','Poem2','Poem3','Poem4','Poem5');
$portion[161]=array();
$portion[162]=array();
$portion[165]=array();
$portion[207]=array();
$portion[217]=array('Sun','Air','Light','Water','Wild Animals');
$portion[219]=array();
$portion[251]=array();
$portion[254]=array();
$portion[287]=array();
$portion[299]=array();
$portion[300]=array();
$portion[318]=array();
$portion[319]=array();
$portion[344]=array();
$portion[345]=array();


$sliptest_with_empty_portion=array();


	    $sql     = "SELECT SlipTestId,SubjectId FROM `sliptest` where `sliptest`.`PortionName` = ' ' and SchoolId='58'";
	    $res     = mysql_query($sql) or die("Error in fetching ClassId from Class Table".mysql_error());
	   

	    while($row = mysql_fetch_array($res)){
	        
	        array_push($sliptest_with_empty_portion,$row);
	    }
foreach($sliptest_with_empty_portion as $sliptest)
{
	shuffle($portion[$sliptest['SubjectId']]);
	$portion_name=$portion[$sliptest['SubjectId']][1];
	$sliptest_id=$sliptest['SlipTestId'];
	//var_dump($portion_name);
	$sql     = "UPDATE sliptest set PortionName='$portion_name' where SlipTestId='$sliptest_id'";
	$res     = mysql_query($sql) or die("Error in fetching ClassId from Class Table".mysql_error());

}



?>


