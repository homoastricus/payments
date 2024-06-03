<?php
include("./init.php");

global $operations;

$operationId = $argv[1];

$operations->revert($operationId);