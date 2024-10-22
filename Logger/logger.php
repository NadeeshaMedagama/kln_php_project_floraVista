<?php 

function logger($level, $message) {

    $file = __DIR__ . "/../logs/logs.log";
    $date = date("Y-m-d H:i:s");

    $log_msg = "[$date]  [$level] - $message" . PHP_EOL;

    file_put_contents($file, $log_msg, FILE_APPEND);
}

?>
