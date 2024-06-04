<?php

include("./init.php");

global $operations;

$date = $argv[1] ?? null;

$result = $operations->getOperationsSumByDate($date);

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;