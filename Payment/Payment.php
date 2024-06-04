<?php
namespace Payment;

interface Payment
{
    public function sendMoney(int $sender, int $receiver, int $value): bool;
    public function fillUpMoney(int $user, int $value): bool;
    public function cashOutMoney(int $user, int $value): bool;
}