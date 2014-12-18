<html>
<head>
  <title></title>
</head>
<body>
<?
include_once "../../connection/connect.php";

//  please take care abt sliptest table insert trigger

$tab_arr = array();
array_push($tab_arr,'students');
array_push($tab_arr,'activity');
array_push($tab_arr,'activitymark');
array_push($tab_arr,'class');
array_push($tab_arr,'exams');
array_push($tab_arr,'marks');
array_push($tab_arr,'portion');
array_push($tab_arr,'school');
array_push($tab_arr,'section');
array_push($tab_arr,'sliptest');
array_push($tab_arr,'sliptestmark_22');
array_push($tab_arr,'studentattendance');
array_push($tab_arr,'subactivity');
array_push($tab_arr,'subactivitymark');
array_push($tab_arr,'subjectexams');
array_push($tab_arr,'subjects');
array_push($tab_arr,'subjectteachers');
array_push($tab_arr,'teacher');
array_push($tab_arr,'homeworkmessage');

function p($msg)
{
  echo "<pre>";
  print_r($msg);
  echo "</pre>"."<br>";
}

foreach ($tab_arr as $table) {

$trigger_name = "catch_".$table."_insert";

$insert_trigger = "DROP TRIGGER IF EXISTS `$trigger_name`//
CREATE TRIGGER `$trigger_name` BEFORE INSERT ON `$table`
 FOR EACH ROW BEGIN
    DECLARE original_query VARCHAR(2000);
    SET original_query = (SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE id = CONNECTION_ID());
    INSERT INTO `trigger`(`TableName`,`Action`,`Query`,`IsNew`,`SchoolId`) VALUES ('$table','INSERT',original_query,1,new.SchoolId);
END
//";

p($insert_trigger);




$trigger_name = "catch_".$table."_update";

$update_trigger = "

DROP TRIGGER IF EXISTS `$trigger_name`//
CREATE TRIGGER `$trigger_name` BEFORE UPDATE ON `$table`
 FOR EACH ROW BEGIN
    DECLARE original_query VARCHAR(2000);
    SET original_query = (SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE id = CONNECTION_ID());
    INSERT INTO `trigger`(`TableName`,`Action`,`Query`,`IsNew`,`SchoolId`) VALUES ('$table','UPDATE',original_query,1,new.SchoolId);
END
//";

p($update_trigger);

$trigger_name = "catch_".$table."_delete";

$delete_trigger = "
DROP TRIGGER IF EXISTS `$trigger_name`//
CREATE TRIGGER `$trigger_name` AFTER DELETE ON `$table`
 FOR EACH ROW BEGIN
    DECLARE original_query VARCHAR(2000);
    SET original_query = (SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE id = CONNECTION_ID());
    INSERT INTO `trigger`(`TableName`,`Action`,`Query`,`IsNew`,`SchoolId`) VALUES ('$table','DELETE',original_query,1,old.SchoolId);
END
//";

p($delete_trigger);


}


?>

</body>
</html>







