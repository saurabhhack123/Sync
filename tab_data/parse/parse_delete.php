<?php
error_reporting(0);
ini_set('max_execution_time', 30000000);
include_once "../../../connection/connect.php";


  //*************************** query parser for delete , return hash map
  
  function _qdelete_clause_helper($query_exp)
  {
   $qmap = array();
   if(strpos($query_exp,"and")){
         $qarr = explode("and",$query_exp);
         foreach ($qarr as $q) {
             //$q = str_replace("!=", "=", $q);//To overcome != condition
             $qexp         = explode("=",$q);
             $key          = trim($qexp[0]);
             $qmap["$key"] = trim($qexp[1]); 
         }
      }
      else{
             //$query_exp = str_replace("!=", "=", $query_exp);//To overcome != condition
             $qexp         = explode("=",$query_exp);
             $key          = trim($qexp[0]);
             $qmap["$key"] = trim($qexp[1]); 
      } 
     return $qmap;   
  }

  function _query_delete_parse($query){
      $where_index = strpos($query,"where",0)+6;
      $query_exp   = substr($query,$where_index);
      $qmap = array();
      $qmap = _qdelete_clause_helper($query_exp);        
      return $qmap;
   }

  function _parse_delete_query($query){
      $query_map = array();
      $query_map = _query_delete_parse($query);
      
      return $query_map;
   } 

 function is_complex_qdelete($parse_map){
     
     // if query is complex , update is_complex
           $q_map = $parse_map;

           $is_complex = 0;

     // validating map
           $needles = array("values","insert","select","union","IN","(",")","OR","<",">","where","from","delete");

           foreach ($q_map as $col => $value) {
                 foreach($needles as $needle){
                    if(stripos($value, $needle) !== FALSE)
                       { $is_complex=1; break; }
                 }
           }
     // else
    return $is_complex;
 }

 /** query paser code ends */

?>