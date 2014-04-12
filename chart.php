<?php
include "db.php";
include "testurl.php";
?>
<html>
<head>
<meta charset="utf8" />
<script src="static/jquery-1.11.0.min.js"></script>
<script src="static/highcharts.js"></script>
<script src="static/exporting.js"></script>
</head>
<body>
<?php
if (!isset($_GET['time'])) {
    $start_time = time() - 24 * 3600;
} else {
    $start_time = time() - intval($_GET['time']) * 3600;
}

if (!isset($_GET['id'])) {
    $rs = mysql_query("SELECT id FROM host ORDER BY id");
    while ($row = mysql_fetch_array($rs)) {
        plot($row['id'], $start_time);
    }
} else {
    $ids = explode(' ', $_GET['id']);
    foreach ($ids as $id) {
        $id = intval($id);
        if ($id > 0) {
            plot($id, $start_time);
        }
    }
}

function plot($id, $start_time) {
    $rs = mysql_query("SELECT time, response_time FROM full_log WHERE id = $id AND time > $start_time ORDER BY time");
    $url = mysql_result(mysql_query("SELECT url FROM host WHERE id = $id"), 0);


$start_time = 0;
$last_time = 0;
$INTERVAL = 60;
$data = [];
while ($row = mysql_fetch_array($rs)) {
    if (!$start_time) {
        $start_time = $row['time'];
        $data[] = $row['response_time'];
        $last_time = $start_time;
    }
    else {
        $time = $row['time'];
        if ($last_time < $time) {
            // add zero data points if missing
            while ($last_time + 2*$INTERVAL <= $time) {
                $data[] = 0;
                $last_time = $last_time + $INTERVAL;
            }

            $data[] = $row['response_time'];
            $last_time = $last_time + $INTERVAL;
        }
        // otherwise, the duplicate data point is dropped silently
    }
}
?>
<div id="container<?=$id?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script>
$(function () {
        $('#container<?=$id?>').highcharts({
            chart: {
                zoomType: 'x',
                spacingRight: 20
            },
            title: {
                    text: 'HTTP Response Time of <?=addslashes($url)?>'
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                    'Click and drag in the plot area to zoom in' :
                    'Pinch the chart to zoom in'
            },
            xAxis: {
                type: 'datetime',
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'HTTP Response Time (ms)'
                }
            },
            tooltip: {
                shared: true
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
    
            series: [{
                type: 'area',
                name: 'Time',
                pointInterval: <?=$INTERVAL?> * 1000,
                pointStart: (<?=$start_time?> + 8 * 3600) * 1000,
                data: [ <?=implode(',',$data)?> ]
            }]
        });
});
</script>
<hr />
<?php
} // end function plot
?>
</body>
</html>
