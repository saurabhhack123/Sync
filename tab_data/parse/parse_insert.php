<?php
error_reporting(0);
ini_set('max_execution_time', 30000000);
include_once "../../../connection/connect_new.php";

// code to parse the query , return hash map

 function _query_insert_parse($query){
      $open_brace_first   = strpos($query,"(",0)+1;
      $close_brace_first  = strpos($query,")",0);
      $open_brace_second  = strpos($query,"(",$open_brace_first)+1;
      $close_brace_second = strpos($query,")",$open_brace_second);
      
      $cols     = substr($query,$open_brace_first,$close_brace_first-$open_brace_first);
      $cols_val = substr($query, $open_brace_second,$close_brace_second-$open_brace_second);
    
      $cols_arr     = explode(",",$cols);
      $cols_val_arr = explode(",",$cols_val);
     
      $qmap = array();
      $len  = count($cols_arr);
      for($i=0;$i<$len;$i++){
         $key = trim($cols_arr[$i]);
         $val = trim($cols_val_arr[$i],"'");
         $qmap["$key"] = $val;
      }
      return $qmap;
   }
      
      
 function _parse_insert_query($query){
      $query_map = array();
      $query_map = _query_insert_parse($query);
      
      return $query_map;
     } 
    
  function _query_insert_valid($query){
      $open_brace_first   = strpos($query,"(",0)+1;
      $close_brace_first  = strpos($query,")",0);
      $open_brace_second  = strpos($query,"(",$open_brace_first)+1;
      $close_brace_second = strpos($query,")",$open_brace_second);
      
      return substr($query,$close_brace_first,$open_brace_second-$close_brace_first);  
     
  } 

  function is_complex_qinsert($parse_map,$table){
     
     // if query is complex , update is_complex
           $q_map = $parse_map;

           $valid_tbls = array();
           $valid_tbls = get_cols_table($table);

           foreach ($q_map as $tbl_name => $tbl_val) {
             # code..
               if(!in_array($tbl_name,$valid_tbls))
                 {
                   $is_complex = 1;
                   return $is_complex;
                 }
           }

           // handle special case
           $is_contain = false;
           $str = _query_insert_valid($query);
           if(stripos($str,"values")==TRUE)
               $is_contain = true;


           $is_complex = 0;

           // validating map
           $needles = array("values","VALUES","INTO","into","INSERT","insert","select","SELECT","union","IN","(",")","OR","<",">");
           foreach ($q_map as $col => $value) { 
                 foreach($needles as $needle){
                  $pattern ='#\b'.$needle.'\b#';
                  if (preg_match($pattern,$value) || preg_match($pattern, $col)) { 
                      $is_complex=1; break; 
                      }  
                 }
           }

    if($is_contain)
      $is_complex = 1;
    
    return $is_complex ;
  }
 
?>