<?php
	
include_once "../../connection/connect.php";

function tab_using_schools(){
	$sql = "select distinct SchoolId from map_school_tab";
	$res = mysql_query($sql);
    $schools = array(); 
	while ($row=mysql_fetch_array($res)) {
	    array_push($schools,$row["SchoolId"]);
	}
	return $schools;
}

// this code written by bhuvan , don't blame me :) 
    
	date_default_timezone_set('Asia/Calcutta');
	$today = date("Ymd");
	$date = date("Y-m-d");
	
    // logic for scheduling att sms for tab 
    
    $schools = tab_using_schools();
    foreach ($schools as $school_id) {
     $sql = "select att_time from tab_sms_schedule SchoolId='$school_id'";
     $res = mysql_query($sql);
     $row = mysql_fetch_array($res);
     
     $att_time = $row["att_time"];



    }
    



	$time="";
	echo "Fetching from Table Queue...\n";
	$te1="select * from queue where Status=0 order by DateTime Asc";
	$q=mysql_query($te1,$con);
	if(!$q)
	{
		die('Could not Connect'.mysql_error());
	}
	$id="";
	$phone=""; 
	$i=0;
	$numrows=mysql_num_rows($q);
	if($numrows>0)
	{
			while($a=mysql_fetch_assoc($q))
			{
			$phone1=split(',',$a['Phone']);

			$no=count($phone1)-1;
			$no1=$no+1;
			echo "Number of Phone Numbers : $no1 \n";

			$mess=$a['Message'];
			$datetime=$a['DateTime'];
			$schoolid=$a['SchoolId'];
			$senderid="SCLCOM";
			$sqlschool="select * from school where SchoolId='$schoolid'";
			$resultschool=mysql_query($sqlschool) or die("Error: ".mysql_error());
			while($rowschool=mysql_fetch_array($resultschool))
			{
				$senderid=$rowschool['SenderID'];
			}
			$te2="update tab_queue set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";

			$q2=mysql_query($te2,$con);

			$phone1=split(',',$a['Phone']);

			$k=0;
			$ff=0;
			$dd=$no;
			$ph="";
			for($j=0;$j<=$no;)
			{
			$ph="";
			$phon=$phone1[$j];
			$phon=str_replace("\n","",$phon);
			$phon=str_replace("\r","",$phon);
			$ph=$ph.$phon;
			echo "sending to ".$ph."\n";
			if(($phone1[$j++])!="")
			
			$ph=$ph.",".$phone1[$j];

			$j++;
		

			if($ph!='91000000000')
					{			
			$url="http://hp.365dayssms.com/sms.aspx?ID=vishwasisya@365dayssms.com&Pwd=happysms&SenderID=$senderid&PhNo=".$ph."&Text=".urlencode($mess);
			$url="http://alerts.sinfini.com/api/web2sms.php?workingkey=3443cbj3s5y91mstfsnh&sender=$asenderid&to=$ph&message=".urlencode($mess);
			//$url="http://promo.liveair.in/api/sentsms.php?username=condemo&password=admin.123456&to=$ph&message=".urlencode($mess)."&sender=SCLCOM&priority=1";
			$url="http://alertbox.in/pushsms.php?username=bhuvan&api_password=45c48do6oep0mx6yf&sender=$senderid&to=$ph&message=".urlencode($mess)."&priority=11";
			$url="http://liveair.in/pushsms.php?username=transpromo&password=admin@123&sender=$senderid&to=$ph&message=".urlencode($mess)."&priority=11";
			$url="http://bulksmsservice.co.in/api/sentsms.php?username=condemo&api_password=eyyyyy33mp&to=$ph&message=".urlencode($mess)."&sender=$senderid&priority=2";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL,$url);
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$strPage = curl_exec($curl);
			}
			}
			echo "Completed Sending SMS to $no1 Phone Numbers -$senderid-\n";

			$te2="delete from tab_queue where SchoolId='$schoolid' and Status=1 and DateTime='$datetime'";
			$q2=mysql_query($te2,$con);


			}
}

?>

    



