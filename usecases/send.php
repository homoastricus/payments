<?php
include("./init.php");

global $user_money;

$sender = $argv[1];
$receiver = $argv[2];
$value = $argv[3];

$user_money->sendMoney($sender, $receiver, $value);