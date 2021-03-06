<?
include("common.php");
include_once("session.php");
$selected_date = fixString($_GET['date']);
if($selected_date == ""){
 $selected_date = date('Y-m-d');
}

$view = fixString($_GET['view']);
if($view == ""){
  $view = getUserSettingValue($id, 'default_view');
  if($view == ""){
  $view = 'agendaWeek';
  }
}

$slot_size = getUserSettingValue($id, 'slot_size');
if($slot_size == ""){
  //Default Slot Size
  $slot_size = 15;
}

$start_hour = getUserSettingValue($id, 'start_hour');
if($start_hour == ""){
  $start_hour = 8;
}


$sid = fixString($_GET['sid']);
$sou = fixString($_GET['sou']);
$srole = fixString($_GET['srole']);
$ou = fixString($_GET['ou']);
$role = fixString($_GET['role']);
$s = fixString($_GET['s']);

$extra_array = array(
  'sid'=>$sid,
  'sou'=>$sou,
  'srole'=>$srole,
  'ou'=>$ou,
  'role'=>$role,
  's'=>$s,
);
    $query_string = http_build_query($extra_array);

function parseDateForJS($date){
  $return = array(
    'year' => substr($date, 0, 4),
    'month' => (date('m', strtotime($date)) - 1),//Stupid javascript month format
    'day' => date('d', strtotime($date))
  );
return $return;
}
$date = parseDateForJS($selected_date);
//Set up some date limits so we don't get events for all time
if($view == 'month'){
  $start_day = date('Y-m-d', strtotime(date("Y-m-d", strtotime($selected_date)) . " -5 week") );
  $end_day = date('Y-m-d', strtotime(date("Y-m-d", strtotime($selected_date)) . " +5 week") );
}

if($view == 'agendaWeek'){
  $start_day = date('Y-m-d', strtotime(date("Y-m-d", strtotime($selected_date)) . " -1 week") );
  $end_day = date('Y-m-d', strtotime(date("Y-m-d", strtotime($selected_date)) . " +1 week") );
}

if($view == 'agendaDay'){
  $start_day = $selected_date;
  $end_day = $selected_date;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?echo $site_title;?> </title>
<?echo $common_js;?>
<link rel='stylesheet' type='text/css' href='css/jquery-ui-1.7.2.custom.css' />
<?
echo $common_css;
echo $full_calendar_css;
echo $full_calendar_links;
?>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.js'></script>
<script type='text/javascript'>
$(document).ready(function() {
<?echo $common_jquery;?>

  $.strPad = function(i,l,s) {
    var o = i.toString();
    if (!s) { s = '0'; }
      while (o.length < l) {
        o = s + o;
      }
    return o;
  };
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
$('#calendar').fullCalendar(
{
  header: {
  left: null,
  center: 'title',
  right: null
},
			editable: false,
dayClick: function(date, allDay, jsEvent, view) {
  var curr_date = date.getDate();
  var curr_month = date.getMonth();
  curr_month++;
  var curr_year = date.getFullYear();
  
  var curr_hour = date.getHours();
  var curr_min = date.getMinutes();
 
  curr_date = $.strPad(curr_date, 2);
  curr_month = $.strPad(curr_month, 2);
  var string_date = curr_year + "-" + curr_month + "-" + curr_date;
  
  var string_time = curr_hour + ":" + $.strPad(curr_min, 2);

        if (allDay) {
            window.location.href = "?date=" + string_date + "&view=agendaDay&<?echo $query_string;?>";
        }else{
            //window.location.href = "new_appt.php?date=" + string_date + "&time=" + string_time;
        }


    },
defaultView: '<?echo $view;?>',
slotMinutes: <?echo $slot_size;?>,
firstHour: <?echo $start_hour;?>,
  events: [
<?
$open_appts = getOpenAppointments($sou, $srole, $start_day, $end_day, $id, $ou, $role);

$events_string = "";
if($open_appts){
  foreach($open_appts as $item){
    $events_string .= drawBlockByBid($item['bid'], "add_participant.php?bid=".$item['bid']."&".$query_string);
  }
}
//Trim last comma
$events_string = substr($events_string, 0, -5);
echo $events_string;
?>
			]
		}

);
$('#calendar').fullCalendar('gotoDate', <?echo $date['year'];?>, <?echo $date['month'];?>, <?echo $date['day'];?>);


$("button#add").click(function() {
  $('#calendar').fullCalendar('addEventSource',source );
});

$("button#rem").click(function() {
  $('#calendar').fullCalendar('removeEvents', noms  );
});
		
	});

</script>
<style type='text/css'>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}

.main {float:left;}
#controls {
  width: 20%;
  height: 100px;
  margin: 0 auto;
  }
#calendar {
  width: 80%;
  height: 100%;
  margin: 0 auto;
  float:right;
  }
</style>
</head>
<body>
<div class="container">
<?

drawHeader($id);
drawCalControld($selected_date, $view, $extra_array);
?>
<br />
<br />
<br />
<div class="alert alert-info">

<?
echo "<a href=\"index.php?date=$selected_date\" class=\"close\">x</a>";
echo "Pick appointment time for ";
$place_name = getName($sid);
echo "<b>$place_name (@$sid) </b>";

echo "</div>";
?>
<hr />

<div id="controls" class="main sidebar">
</div>
<div class="content">
  <div id='calendar' class="main"></div>
  </div>
</div>
</body>
</html>
