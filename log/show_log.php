<!DOCTYPE html>
<?php
  include_once "../../connection/connect_log.php"; 
?>
<html>
 <head>
   <title>App logs</title>

      <link rel="stylesheet" type="text/css" href="../../../../resources/css/bootstrap.css" />
      <link href='http://fonts.googleapis.com/css?family=Life+Savers|Margarine|Sacramento|Fenix' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" type="text/css" href="css/style.css" />
      <link rel="stylesheet" type="text/css" href="../../home/js/jquery.datetimepicker.css"/>
      <script type="text/javascript" src="../../../../resources/js/jquery-1.10.1.js"></script>
      <script type="text/javascript" src="../../../../resources/js/bootstrap.min.js"></script>
      <script src="../../home/js/jquery.datetimepicker.js"></script>
      <script type="text/javascript">
          $(document).ready(function(){
             $('#btn_from').click(function(){
                       $('#inp_from').datetimepicker('show'); 
             });

             $('#btn_to').click(function(){
                       $('#inp_to').datetimepicker('show'); 
             });
             
             $('#inp_from').datetimepicker({format:'Y-m-d H:i'});
             $('#inp_to').datetimepicker({format:'Y-m-d H:i'});
          });
          
          var fetch_logs = function(){
                    var from = $("#inp_from").val();
                    var to   = $("#inp_to").val();
                    var msg  = "fetch_logs";
               
                    console.log("fetch_logs");
                    var parameters="?from="+from+"&to="+to+"&action="+msg;
                    $(".disp_status").html("<div class='loader'><img src='img/loader.gif'><span>fetching status....</span></div>");

                    $.get("ajax/ajax.php"+parameters, {},function( data ) {
                        $(".disp_status").html(data);
                    });
          }
          </script>
 </head>

<body>
  <div class="row-fluid head_msg">
          <div class="span12">      
             <h2>App logs</h2>
             <div class='bottom_shadow'></div>
          </div>
  </div>
  <div class="row-fluid sub_header"> 

   <div class="span3 input-group">
          <input id="inp_from" type="text" placeholder=" < from date >">
                  <button id="btn_from" class="btn btn-default" type="button">
                      <span class="">
                          <i class="icon-calendar">
                          </i>
                      </span>
                  </button>
   </div>

   <div class="span3 input-group">
          <input id="inp_to" type="text" placeholder="< to date >">
                  <button id="btn_to" class="btn btn-default" type="button">
                      <span class="">
                          <i class="icon-calendar">
                          </i>
                      </span>
                  </button>           
   </div>

        <div class="span3">
            <button id="submit" class="btn btn-info" type="button" onclick="fetch_logs();">fetch logs</button>
        </div>

    </div>
  </div>


  <div class="row-fluid disp_status">

  </div>



</body>
</html>



</body>
</html>