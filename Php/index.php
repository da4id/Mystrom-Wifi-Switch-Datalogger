<?php
$defaultStartDate = strtotime("-1 month", time());
$defaultEndDate = strtotime("+1 day", time());
$displayEndDate = date("d.m.Y H:i:s", time());

include 'logData.php';

$pdo = createPDO();
$loggers = getDataLoggers($pdo);

if (!isset($_GET)) {
    $loggerId = $loggers[0]["dbId"];
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
            $loggerId = $loggers[0]["dbId"];
        }
    } else {
        $loggerId = $loggers[0]["dbId"];
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

$data = getData($pdo,$dbIdSeries);
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

    <!-- Resources -->
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all"/>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

    <!-- Chart code -->
    <script type="text/javascript">
        var chartData = 
        <?php
            printData($data);
        ?>;

        var powerChart = AmCharts.makeChart("PowerChartDiv", {
            "type": "serial",
            "theme": "light",
            "marginRight": 80,
            "dataProvider": chartData,
            "valueAxes": [{
                "position": "left",
                "title": "power"
            }],
            "graphs": [{
                "id": "g1",
                "fillAlphas": 0.4,
                "valueField": "power",
                "balloonText": "<div style='margin:5px; font-size:19px;'>Momentanleistung:<b>[[value]]W</b></div>"
            }],
            "chartScrollbar": {
                "graph": "g1",
                "scrollbarHeight": 80,
                "backgroundAlpha": 0,
                "selectedBackgroundAlpha": 0.1,
                "selectedBackgroundColor": "#888888",
                "graphFillAlpha": 0,
                "graphLineAlpha": 0.5,
                "selectedGraphFillAlpha": 0,
                "selectedGraphLineAlpha": 1,
                "autoGridCount": true,
                "color": "#AAAAAA"
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                "cursorPosition": "mouse"
            },
            "categoryField": "date",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true
            },
            "export": {
                "enabled": true,
                "dateFormat": "YYYY-MM-DD HH:NN:SS"
            }
        });

        powerChart.addListener("dataUpdated", zoomPowerChart);
        // when we apply theme, the dataUpdated event is fired even before we add listener, so
        // we need to call zoomChart here
        zoomPowerChart();

        // this method is called when chart is first inited as we listen for "dataUpdated" event
        function zoomPowerChart() {
            // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            powerChart.zoomToIndexes(0, chartData.length - 1);
        }
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
            Verbrauchswerte - Gesamtverbrauch: <?php echo number_format($lastEntry["Energy"]/1000, 3, '.', ','); ?> kWh
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

