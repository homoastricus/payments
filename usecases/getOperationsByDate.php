<?php
include("./init.php");

global $operations;

$date = $argv[1];

$result = $operations->getOperationsByDate($date);

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;