<?php
include("./init.php");

global $user_money;

$userId = $argv[1];
$value = $argv[2];

$user_money->cashOutMoney($userId, $value);