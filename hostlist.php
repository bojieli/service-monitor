<?php
include "db.php";
include "testurl.php";
?>
<html>
<head>
<meta charset="utf8" />
<style>
body {font-family: "Segoe UI", Ubuntu, Tahoma, sans-serif;}
td {padding:1px 5px 1px 5px}
tr.green td {background:#83F57F; color:#000}
tr.red td {background:#FE7777; color:#000}
</style>
</head>
<body>
<table id="sort">
<thead>
<tr>
  <th>ID</th>
  <th>URL</th>
  <th>Status</th>
  <th>Last Probe</th>
  <th>Response Time (ms)</th>
  <th>Last Status Change</th>
</tr>
</thead>
</tbody>
<?php
$rs = mysql_query("SELECT FROM_UNIXTIME(lastprobe) AS lastprobe, id, url, status, response_time, FROM_UNIXTIME(last_status_change) AS lastchange FROM host");
while ($row = mysql_fetch_array($rs)) {
	if ($row['status'] == 0)
		$background = 'green';
	else
		$background = 'red';
	$status = status2name($row['status']);
    $id = $row['id'];
	$url = htmlspecialchars($row['url']);
    $lastprobe = $row['lastprobe'];
    $response_time = $row['response_time'];
	echo "<tr class=\"$background\">";
    echo "<td>$id</td>";
	echo "<td>$url</td>";
	echo "<td>$status</td>";
    echo "<td>$lastprobe</td>";
    echo "<td align=\"right\">$response_time</td>";
    echo "<td>".$row['lastchange']."</td>";
	echo "</tr>\n";
}
?>
</tbody>
</table>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.13.3/jquery.tablesorter.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.13.3/css/theme.default.css" />
<script>
$(document).ready(function(){ 
        $("#sort").tablesorter(); 
}); 
</script>
</body>
</html>
