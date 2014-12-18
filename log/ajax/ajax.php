<?
 include_once "../../../connection/connect_log.php";
 include_once "../models/log.php";
  

 $action = $_REQUEST["action"];

 if($action == "fetch_logs"){

  $from = $_REQUEST["from"];
  $to   = $_REQUEST["to"];	
  
  $log_entry =  new Log($from,$to);
  $logs = $log_entry->logs;
  
  $html = "";

  $html .= "<div class='row-fluid span12'>"; 

  foreach ($logs as $log) {
    $html .= "<div class='row-fluid span12 container'>";    
    $start = strpos($log["LOGCAT"],"D/id");
    $tab_id = substr($log['LOGCAT'], $start+18,17);
    
    $stack_trace = $log["STACK_TRACE"];
    $logcat = $log["LOGCAT"];
    $user_crash_date = $log["USER_CRASH_DATE"];
     
    $html.= "<div class='row-fluid span12 top_head'>";
    $html.= "<div class='span6 tab'>$tab_id</div>";
    $html.= "<div class='span6 crash_date'>$user_crash_date</div>";
    $html.= "</div>";
    
    $stack_text = "stack_trace :~: &gt;&gt; ";

    $html.= "<div class='stack_trace'>"."<span>".$stack_text."</span>".$stack_trace."</div>";
    
    $log_text = "logcat :~: &gt;&gt;";
        
    $html.= "<div class='logcat'>"."<span>".$log_text."</span>".$logcat."</div>";
    $html.= "</div>";
    $html.="</div>";
  }
  $html.= "</div>";
  echo $html;
 }  


?>