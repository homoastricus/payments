<?php

namespace Payment;
use Payment\Operations\OperationInterface;

interface LogOperation
{
    public function Log(OperationInterface $operation);
}
