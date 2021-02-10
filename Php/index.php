<?php
$defaultStartDate = strtotime("-1 month", time());
$defaultEndDate = strtotime("+1 day", time());
$displayEndDate = date("d.m.Y H:i:s", time());

include 'logData.php';

$pdo = createPDO();
$latestSeries = getLatestSeries($pdo)[0];
$loggers = getDataLoggers($pdo);

if (!isset($_GET)) {
    $loggerId = $latestSeries["dbIdDatalogger"];
} else {
    if (isset($_GET["datalogger"])) {
        $loggerId = $_GET["datalogger"];
        $foundLogger = false;
        foreach ($loggers as $logger) {
            if ($loggerId == $logger["dbId"]) {
                $foundLogger = true;
            }
        }
        if ($foundLogger == false) {
            $loggerId = $latestSeries["dbIdDatalogger"];;
        }
    } else {
        $loggerId = $latestSeries["dbIdDatalogger"];;
    }
}

$series = getSeriesForLogger($pdo,$loggerId);

if(!isset($_GET)){
    $dbIdSeries = $series[0]["dbId"];
} else {
    if(isset($_GET["series"])){
        $dbIdSeries = $_GET["series"];
        $foundSeries = false;
        foreach ($series as $ser) {
            if ($dbIdSeries == $ser["dbId"]) {
                $foundSeries = true;
            }
        }
        if ($foundSeries == false) {
            $dbIdSeries = $series[0]["dbId"];
        }
    } else {
        $dbIdSeries = $series[0]["dbId"];
    }
}
$lastEntry = getLastEntry($pdo,$dbIdSeries);

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
    
    <style>
    #PowerChartDiv {
            width   : 100%;
            height  : 500px;
        }
    </style>;

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/jquery.min.js"></script>

    <!-- Resources -->
    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    <!-- Chart code -->
    <!-- Chart code -->
    <script>
        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("PowerChartDiv", am4charts.XYChart);

        // Add data
        //chart.data = generateChartData();

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "power";
        series.dataFields.dateX = "date";
        series.strokeWidth = 2;
        series.minBulletDistance = 10;
        series.tooltipText = "Momentanleistung: {power}W";
        series.tooltip.pointerOrientation = "vertical";

        // Add scrollbar
        chart.scrollbarX = new am4charts.XYChartScrollbar();
        chart.scrollbarX.series.push(series);

        // Add cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.xAxis = dateAxis;
        chart.cursor.snapToSeries = series;

        $.get("<?php echo 'data.php?datalogger='.$loggerId.'&series='.$dbIdSeries; ?>", data => {
            for (var d of data) {
                d.date = new Date(d.date)
            }
		    chart.data = data;

		});

        }); // end am4core.ready()
    </script>  

    <title>myStrom Switch Logdaten</title>
</head>
<body>
<nav class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Logdaten</a>
        </div>
    </div>
</nav>

<div class="container">

    <!--- Panel Zeitraum auswählen --->
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#panelZeitraum">
            Logger und Series
        </div>
        <div class="collapse panel-body" id="panelZeitraum">
            <form method="GET">
                <div class="form-group">
                    <label for="datalogger">Datenlogger</label>
                    <select class="form-control" name="datalogger">
                        <?php
                        foreach ($loggers as $logger) {
                            if ($loggerId == $logger["dbId"]) {
                                echo '<option selected value="' . $logger["dbId"] . '">' . $logger["Name"] . '</option>';
                            } else {
                                echo '<option value="' . $logger["dbId"] . '">' . $logger["Name"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="datalogger">Messreihe</label>
                    <select class="form-control" name="series">
                        <?php
                        foreach ($series as $ser) {
                            if ($dbIdSeries == $ser["dbId"]) {
                                echo '<option selected value="' . $ser["dbId"] . '">' . $ser["Name"]. " - ". $ser["Startdatum"] . '</option>';
                            } else {
                                echo '<option value="' . $ser["dbId"] . '">' . $ser["Name"]. " - ". $ser["Startdatum"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <!--- Panel mit Charts --->
    <div class="panel panel-default">
        <div class="panel-heading" id="lastUpdate">
            Verbrauchswerte - Gesamtverbrauch: <?php echo number_format($lastEntry["Energy"], 3, '.', ','); ?> kWh
        </div>
        <div class="panel-body">
            <!-- HTML -->
            <div id="PowerChartDiv"></div>
        </div>
        <div class="panel-footer">
            &copy; 2019 David Zingg
        </div>
    </div>
</div>

</body>
</html>

