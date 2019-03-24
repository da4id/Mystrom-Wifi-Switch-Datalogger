<?php

include_once 'config.php';

function createPDO()
{
    $config = readConfig();
    $servername = $config["server"];
    $username = $config["user"];
    $password = $config["passwd"];
    $dbname = $config["database"];

    return new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
}

function getDataLoggers($pdo)
{
    $sql = "SELECT dbId, Name FROM Datalogger";
    $result = $pdo->prepare($sql);
    $result->execute();

    return $result->fetchAll();
}

function getSeriesForLogger($pdo, $dbIdDatalogger)
{
    $sql = "SELECT dbId, dbIdDatalogger, Name, Startdatum FROM Series WHERE dbIdDatalogger = " . $dbIdDatalogger;
    $result = $pdo->prepare($sql);
    $result->execute();

    return $result->fetchAll();
}

function getLastEntry($pdo, $dbIdSeries)
{
    $sql = "SELECT Energy, Timestamp FROM `Measurements` where dbIdSeries = ".$dbIdSeries." order by Timestamp DESC";
    $result = $pdo->prepare($sql);
    $result->execute();

    return $result->fetch();
}

function getData($pdo, $dbIdSeries)
{
    $sql = "SELECT CurrentPower, Energy, Timestamp FROM Measurements WHERE dbIdSeries = ".$dbIdSeries." ORDER BY Timestamp";
    $result = $pdo->prepare($sql);
    $result->execute();

    return $result->fetchAll();
}

function printData($data)
{
    echo "[";
    foreach ($data as $row) {
        echo '{date : new Date("' . str_replace("-", "/", $row['Timestamp']) . '")';
        echo ', power:'.$row["CurrentPower"]."},\r\n";
    }
    echo "]";
}

?>