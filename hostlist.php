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
  <th>ID</th>
  <th>URL</th>
  <th>Status</th>
  <th>Last Probe</th>
</tr>
<?php
$rs = mysql_query("SELECT FROM_UNIXTIME(lastprobe) AS lastprobe, id, url, status FROM host");
while ($row = mysql_fetch_array($rs)) {
	if ($row['status'] == 0)
		$background = 'green';
	else
		$background = 'red';
	$status = status2name($row['status']);
    $id = $row['id'];
	$url = htmlspecialchars($row['url']);
    $lastprobe = $row['lastprobe'];
	echo "<tr class=\"$background\">";
    echo "<td>$id</td>";
	echo "<td>$url</td>";
	echo "<td>$status</td>";
    echo "<td>$lastprobe</td>";
	echo "</tr>\n";
}
?>
</table>
</body>
</html>
