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
tr.green {background:#83F57F}
tr.red {background:#FE7777}
</style>
</head>
<body>
<table>
<tr>
  <th>时间</th>
  <th>URL</th>
  <th>状态</th>
</tr>
<?php
if (isset($_GET['host']) && is_numeric($_GET['host']) && $_GET['host'] > 0)
    $other_cond = "AND host.id=".$_GET['host'];
else
    $other_cond = '';
$rs = mysql_query("SELECT host.url, host_log.status, FROM_UNIXTIME(host_log.time) as time, host_log.detail FROM host, host_log WHERE host.id = host_log.id $other_cond ORDER BY host_log.time DESC");
while ($row = mysql_fetch_array($rs)) {
	if ($row['status'] == 0)
		$background = 'green';
	else
		$background = 'red';
	$status = status2name($row['status']);
	$detail = htmlspecialchars($row['detail']);
	$time = $row['time'];
	$url = htmlspecialchars($row['url']);
	echo "<tr class=\"$background\">";
	echo "<td>$time</td>";
	echo "<td>$url</td>";
	echo "<td>$status</td>";
	echo "</tr>\n";
}
?>
</table>
</body>
</html>
