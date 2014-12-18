<?

error_reporting(0);
ini_set('max_execution_time', 30000000);
include_once "../../../connection/connect.php";

  function get_cols_table($table){     
      $sql  = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='tabletproduction' AND `TABLE_NAME`='$table'";
      $res  =  mysql_query($sql) or die("Error in fetching from schema".mysql_error());
      $cols = array();
      while($row=mysql_fetch_array($res)){
        array_push($cols,$row['COLUMN_NAME']);
      }
      return $cols;
  }

 function _qupdate_clause_helper($query_exp)
  {
   $qmap = array();
   if(strpos($query_exp,"and")){
         $qarr = explode("and",$query_exp);
         foreach ($qarr as $q) {
             $qexp         = explode("=",$q);
             $key          = $qexp[0];
             $qmap["$key"] = $qexp[1]; 
         }
      }
      else{
             $qexp         = explode("=",$query_exp);
             $key          = $qexp[0];
             $qmap["$key"] = $qexp[1]; 
      } 
     return $qmap;   
  }

  function _update_params_helper($query_exp)
  {
   $qmap = array();

   if(strpos($query_exp,",")){
         $qarr = explode(",",$query_exp);
         foreach ($qarr as $q) {
             $qexp         = explode("=",$q);
             $key          = $qexp[0];
             $qmap["$key"] = $qexp[1]; 
         }
      }
      else{
             $qexp         = explode("=",$query_exp);
             $key          = $qexp[0];
             $qmap["$key"] = $qexp[1]; 
      } 
     return $qmap;   
  }
  
   function _map_update_params($params){

       if(strpos($params,"SET"))
         $set = strpos($params,"SET")+4;
       else
         $set = strpos($params,"set")+4;

       $set_sub = substr($params,$set);
    
       $map = array();
       $map = _update_params_helper($set_sub);
       return $map; 
   }


  function _query_update_parse($query){
      
      if(strpos($query,"WHERE"))
        $query_exp   = explode("WHERE",$query);
      else
         $query_exp     = explode("where",$query);

      $params        = $query_exp[0];
      $where_clause  = $query_exp[1];
      $qmap = array();

      $set_params    = array();
      $set_params    = _map_update_params($params);
      $qmap["params"]= $set_params;

      $where_map     = array();
      $where_map     = _qupdate_clause_helper($where_clause);
      $qmap["where"] = $where_map;

      return $qmap;
   }

  function _parse_update_query($query){
      $query_map = array();
      $query_map = _query_update_parse($query);
      
      return $query_map;
   }


function is_complex_qupdate($parse_map,$table){
     
     // if query is complex , update is_complex
           $q_map = $parse_map;
           
           $is_complex = 0;

           // validating map

           $valid_tbls = array();
           
           $valid_tbls = get_cols_table($table);
           $needles = array("values","VALUES","insert","INSERT","select","union","UNION","(",")","IN","OR","<",">","where","from","delete","or","FROM");
           
           foreach ($q_map["params"] as $param_key => $param_val) {
                 

                 if(!in_array($param_key,$valid_tbls))
                 {
                      $is_complex = 1;
                      return $is_complex;
                 }

                 if(strpos($param_key,")")!==false || strpos($param_key,".")!==false)
                   { $is_complex = 1; break; }

                 if(strpos($param_key,")")!==false || strpos($param_key,",")!==false)
                   { $is_complex = 1; break; }

                 if(is_numeric ($param_key))
                     {$is_complex=1; break; }
                  foreach($needles as $needle){
                   $pattern ='#\b'.$needle.'\b#';
                     if (preg_match($pattern,$param_key) || preg_match($pattern, $param_val)) { 

                         $is_complex=1; 
                         break;

                      } 
                 }
           }
           foreach ($q_map["where"] as $where_key => $where_val) {
                 
                 if(!in_array($where_key,$valid_tbls))
                 {
                      $is_complex = 1;
                      return $is_complex;
                 }

                 if(is_numeric ($where_key))
                     {$is_complex=1; break; }

                 foreach($needles as $needle){
                  $pattern ='#\b'.$needle.'\b#';
                  if (preg_match($pattern,$where_key) || preg_match($pattern, $where_val)) { 
                      $is_complex=1; break; 
                      } 
                 }
           }               
     // else
    return $is_complex;
 }
  

 ?>