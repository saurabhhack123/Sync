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


$class_ids = get_class_ids($school_id);


foreach ($class_ids as $class_id) {
	insert_portion($school_id,$class_id,$portion);
	echo "Inserted..";
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


   function insert_portion($school_id,$class_id,$portion)
   {
   		foreach ($portion as $key => $value) {
   			$subject_id = $key;
   			foreach ($value as $portion_name) {
   				 $sql = "INSERT INTO portion(SchoolId,ClassId,SubjectId,Portion,NewSubjectId) values('$school_id','$class_id','$subject_id','$portion_name',0) "; 
            	mysql_query($sql) or die("Error in inserting portion! ");
   			}
   		}
   }

?>