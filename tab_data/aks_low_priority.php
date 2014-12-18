<?php
function get_diff($old, $new){
    $from_start = strspn($old ^ $new, "\0");
    $from_end = strspn(strrev($old) ^ strrev($new), "\0");

    $old_end = strlen($old) - $from_end;
    $new_end = strlen($new) - $from_end;

    $start = substr($new, 0, $from_start);
    $end = substr($new, $new_end);
    $new_diff = substr($new, $from_start, $new_end - $from_start);
    $old_diff = substr($old, $from_start, $old_end - $from_start);

    //$new = "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
    //$old = "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";
    return $new_diff;
}

$con=mysql_pconnect("localhost","root","bhuvanbhash2109");
if(!$con)
{
    die('Could not Connect'.mysql_error());
}

$d=mysql_select_db("tabletproduction",$con);

function get_sms_api_details() {
    $api_query = "select * from sms_api where active = '1'";
    $api_result = mysql_query($api_query);

    $api = mysql_fetch_array($api_result);

    return $api;
}
function get_sms_api_by_id($id) {
    $api_query = "select * from sms_api where id='".$id."'";
    $api_result = mysql_query($api_query);

    $api = mysql_fetch_array($api_result);

    return $api;
}


while(1)
{

    $sms_api_details = get_sms_api_details();


    date_default_timezone_set('Asia/Calcutta');
    $today = date("Ymd");
    $date = date("Y-m-d");
    $time="";
    echo "Fetching from Table queue_low_priority ...\n";
    $te1="select * from queue_low_priority where Status=0 order by DateTime Asc";
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
            if($a['SchoolId']==70){
                $datetime=$a['DateTime'];

                //Making status to 1 for ARB INTERNATIONAL  SCHOOL
                $sql="update queue_low_priority set Status=1 where Status=0 and SchoolId=70 and DateTime='$datetime'";
                $result=mysql_query($sql,$con);

            }else{

                $phone1=split(',',$a['Phone']);
                $no=count($phone1)-1;
                $no1=$no+1;
                echo "Number of Phone Numbers : $no1 \n";

                $mess=$a['Message'];
                $datetime=$a['DateTime'];
                $schoolid=$a['SchoolId'];
                $user_id = $a['UserId'];
                $role = $a['Role'];

                $senderid="SCLCOM";
                $sqlschool="select * from school where SchoolId='$schoolid'";
                $resultschool=mysql_query($sqlschool) or die("Error: ".mysql_error());
                while($rowschool=mysql_fetch_array($resultschool))
                {
                    $senderid=$rowschool['SenderID'];
                }

                $te2="update queue_low_priority set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";

               $q2=mysql_query($te2,$con);
//phone1 ??
                $phone1=split(',',$a['Phone']);
                $k=0;
                $ff=0;
                $dd=$no;// count of phone num 
                $ph="";
                for($j=0;$j<=$no;)
                {

                    $ph="";
                    $ph=$ph.trim($phone1[$j]);// conctn phone numb
                    if(($phone1[$j++])!=""&&($phone1[$j++])!="91")
                        $ph=$ph.",".trim($phone1[$j]);

                    $j++;

                    $phone91=split(',',$ph);
                    $phone911 = "";
                    // indluding 91 .. 12 dig
                    if(strlen($phone91[0])==12){
                        $phone911 .= substr($phone91[0],2,11);
                    } else {
                        $phone911 .= $phone91[0];
                    }
                    //why?
                    if(isset($phone91[1]) && strlen($phone91[1])==12) {
                        $phone911 .= ','.substr($phone91[1],2,11);
                    } else if(isset($phone91[1])) {
                        $phone911 .= ','.$phone91[1];
                    }
                    //$phone911=substr($phone91[0],2,11).",".substr($phone91[1],2,11);

                    $url = $sms_api_details['send_sms_api'];
                    echo $url;

                     // changed url
                    
                    // $url="http://myvaluefirst.com/smpp/sendsms?username=Bhashsoftpr1&password=bhash123&to=BHASH_PHONE_NUMBERS_TO_SEND&from=BHASH_SMS_SENDER_ID&text=BHASH_SMS_TEXT&dlr-url=http%3A%2F%2F198.23.106.98%2Ftrack_sms%2Fvfreport.php%3Funique_id%3D%257%26reason%3D%252%26to%3D%25p%26from%3D%25P%26time%3D%25t%26status%3D%25d";
                    $url = str_replace("BHASH_SMS_SENDER_ID",$senderid,$url);
                    $url = str_replace("BHASH_SMS_TEXT",urlencode($mess),$url);
                    $is_tamil = 0;
                    if(strlen($mess) != strlen(utf8_decode($mess))) {
                        //sending tamil sms
                        if(strpos($url,"103.247.98.91")==true) {

                            $sms_api_details = get_sms_api_by_id(1);
                            $url = $sms_api_details['unicode_api'];
                            $url = str_replace("BHASH_SMS_SENDER_ID",$senderid,$url);
                            $url = str_replace("BHASH_SMS_TEXT",urlencode($mess),$url);

                        } else if(strpos($url, 'schoolcom.in')!==false){

                            $sms_api_details = get_sms_api_by_id(3);
                            $url = $sms_api_details['unicode_api'];
                            $url = str_replace("BHASH_SMS_SENDER_ID",$senderid,$url);
                            $url = str_replace("BHASH_SMS_TEXT",urlencode($mess),$url);

                        }  else {

                            $url = $sms_api_details['unicode_api'];
                            $url = str_replace("BHASH_SMS_SENDER_ID",$senderid,$url);
                            $url = str_replace("BHASH_SMS_TEXT",urlencode($mess),$url);

                        }

                        if($sms_api_details['require_country_code']==0) {
                            $url = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$phone911,$url);
                            $ph = $phone911;
                        } else if($sms_api_details['require_country_code']==1){
                            $url = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$ph,$url);

                        }

                        $is_tamil = 1;
                    } else {

                        if($sms_api_details['require_country_code']==0) {
                            $url = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$phone911,$url);
                            $ph = $phone911;
                        } else if($sms_api_details['require_country_code']==1){
                            $url = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$ph,$url);

                        }

                    }
// curl_init..( new session)
                    $curl = curl_init();

                    

                    curl_setopt($curl, CURLOPT_URL,$url);

                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $strPage = curl_exec($curl);
                    //print_r($strPage."\n");
                    //print_r($url);exit;
                    $ph = trim($ph,",");
                    $ph_no_array = explode(",",$ph);
                    //print_r($ph."\n");
                    $success_label = $sms_api_details['success_label'];
                    $success_label = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$ph,$success_label);
                    // all the tracking will only start once they accept our request
                    //print_r($sms_api_details['success_label']);
                 


                 if(strpos($strPage,$success_label) !== false) {
                        $response_variable = $sms_api_details['response_variable'];
                        $response_variable = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$ph,$response_variable);
                        $difference_string = get_diff($response_variable, $strPage);
                        //print_r($difference_string."\n");
                        //function to keep in the status queue
                        if(strpos($url,'bulksmsservice.co.in') !== false) {
                            $tracking_array = explode(",",$difference_string);
                            $tracking_array = array_reverse($tracking_array);
                            $difference_string = implode(',',$tracking_array);


                        $tracking_id_array = explode(",",$difference_string);
                        //code added to print phonenumber in 2 diff rows
                        
                        echo " phone array : <br>";
                        var_dump($ph_no_array);
                        
                        foreach ($ph_no_array as $id=>$phone_no)
                        {

                            // $tracking_query = "INSERT INTO `track_sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`, `Status`, `GatewayId`,`sms_api_id`,`is_tamil`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP, '0', '".$tracking_id_array[$id]."','".$sms_api_details['id']."','".$is_tamil."')";
                            // changed
                            echo "<br>test 2 <br>";
                            $tracking_query = "INSERT INTO `sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP)";
                            echo $tracking_query;
                            mysql_query($tracking_query) or die(mysql_error().$tracking_query);
                        }

                        // print_r($tracking_query."\n");


                        //update the status to sent
                        $te2="update queue_low_priority set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";
                        $q2=mysql_query($te2,$con);

                    } 

                    else if(strpos($url,'103.247.98.91') !== false){
                            //special case for handiling prp sms
                            $response_array = explode(",",$strPage);
                            $ph_no_array = explode(",",$ph);
                            $tracking_ids = "";

                            foreach($response_array as $id=>$response_data) {
                                $response_variable = $sms_api_details['response_variable'];
                                $response_variable = str_replace("BHASH_PHONE_NUMBERS_TO_SEND",$ph_no_array[$id],$response_variable);
                                $difference_string = get_diff($response_variable, $response_data);
                                if($tracking_ids == "") {
                                    $tracking_ids .= $difference_string;
                                } else {
                                    $tracking_ids .= ','.$difference_string;
                                }

                                //update the status to sent

                            }
                            $tracking_id_array = explode(",", $tracking_ids);
                            //code added to print phonenumber in 2 diff rows
                            
                            

                            foreach ($ph_no_array as $id=>$phone_no)
                            {
                                echo "<br>test 1 <br>";
                                // $tracking_query = "INSERT INTO `track_sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`, `Status`, `GatewayId`,`sms_api_id`,`is_tamil`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP, '0', '".$tracking_id_array[$id]."','".$sms_api_details['id']."','".$is_tamil."')";

                                // changed
                                $tracking_query = "INSERT INTO `sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP)";
                                echo $tracking_query;  
                                mysql_query($tracking_query) or die('couldnt insert to the table'.mysql_error());
                            }
                            $te2="update queue_low_priority set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";
                            $q2=mysql_query($te2,$con);

                    } 
                    // myvaluefirst.com
                    // changed 
                    // else if(strpos($url,'mainadmin.dove-sms.com')!==false) {
                        else if(strpos($url,'myvaluefirst.com')!==false) {
                         //for xml data
                        $parser = xml_parser_create();
                        xml_parse_into_struct($parser, $strPage, $vals, $index);
                        xml_parser_free($parser);
                        
                        echo " phone array : <br>";
                        var_dump($ph_no_array);

                        foreach($ph_no_array as $id=>$phone_no){
                            $index_value = $index[strtoupper($success_label)][$id];
                            $tracking_id = $vals[$index_value]["value"];
                            // $tracking_query = "INSERT INTO `track_sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`, `Status`, `GatewayId`,`sms_api_id`,`is_tamil`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP, '0', '".$tracking_id."','".$sms_api_details['id']."','".$is_tamil."')";
                            //changed
                            echo "<br>test 3 <br>";
                            $tracking_query = "INSERT INTO `sms_status` (`TrackId`, `SchoolId`, `Phone`, `UserId`, `Role`, `Message`, `DateTime`) VALUES (NULL, '$schoolid', '$phone_no', '$user_id', '$role', '".mysql_escape_string($mess)."', CURRENT_TIMESTAMP)";
                            echo $tracking_query;
                            mysql_query($tracking_query) or die('couldnt insert to the table'.mysql_error());
                        }
                            $te2="update queue_low_priority set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";
                            $q2=mysql_query($te2,$con);

                        }

                        elseif (strpos($url, 'schoolcom.in')!==false|| strpos($url, 'myvaluefirst.com')!==false) {
                            $te2="update queue_low_priority set Status=1 where Status='0' and SchoolId='$schoolid' and DateTime='$datetime'";
                            $q2=mysql_query($te2,$con);
                        }
                 }
                }
                // echo "Completed Sending SMS to $no1 Phone Numbers\n".$tracking_query;
                echo "Completed Sending SMS to $no1 Phone Numbers\n";

                $te2="delete from queue_low_priority where SchoolId='$schoolid' and Status=1 and DateTime='$datetime'";
//$q2=mysql_query($te2,$con);

            }

        }
    }
    sleep(5);

}
?>