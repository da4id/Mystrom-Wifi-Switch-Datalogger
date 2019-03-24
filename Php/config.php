<?php

function readConfig(){
    $string = file_get_contents("config.json");
    return json_decode($string, true);
}

?>