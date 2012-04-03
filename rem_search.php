<?
include("common.php");
include_once("session.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?echo $site_title;?> </title>
<?
echo $common_js;
?>
<script type='text/javascript'>
$(document).ready(function() {
<?echo $common_jquery;?>

  $("#form").submit(function(){
    $("#spinner").show();
    $("#ajax").html("");
    $("#error").hide();
    var q = $("input[name=search]").val();
    $.getJSON(
      "rem_d.php?q=" + q,
      {},
      function(data){
        //debugger;
        $("#spinner").hide();
        for ( var i = 0; i < data.count; i++){
          $("#ajax").append("<a href='" + data[i].uid[0] + "'>" + data[i].displayname[0] + ' (@' + data[i].uid[0] + ')</a><br />');
        }

       if(i >= 15){
         $("#error").show();
         $("#error").html('<div class="alert alert-error">There may be more matches, try refining your search.</div>');
       }
       if(i == 0){
         $("#error").show();
         $("#error").html('<div class="alert alert-message ">No matches</div>');

       }

      }
    );

    return false;
  });
});
</script>
<?
echo $common_css;
?>
</head>
<body>
<div class="container">
<div class="content main">
<?
drawHeader($id);
?>
  <div class="row">

    <div class="span3">
      <div class="well">
        Helpful info
      </div>
    </div>


    <div class="span5 well">
      <form id="form">
      <input type="text" name="search" id="search" class="search-query" value="McPheron" />
      <br />
      <br />
      <input type="submit" class="btn" value="Search">
      </form>

      <div id="error" class="span4"></div>
      <div class="span5" id="spinner" style="display:none;">
      <img src="images/ajax-loader.gif">
      </div>
      <div class="span5" id="ajax">
      </div>

    </div>
  </div>

</div>
</div>
</body>
</html>
