<?php

include 'logData.php';

$pdo = createPDO();
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

$data = getData($pdo,$dbIdSeries);
header("Content-type:application/json");
$out = array();

foreach ($data as &$row) {
    array_push($out,(object)array('date' => $row['Timestamp'],'power' => floatval($row['CurrentPower'])));
}

echo json_encode($out);

?>
