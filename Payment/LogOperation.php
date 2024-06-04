<?php

namespace Payment;
use Payment\Operations\OperationInterface;

interface LogOperation
{
    public function Log(OperationInterface $operation);
    public function revert(int $operationId);
    public function getOperationsByDate(string $date): array;
    public function getOperationsSumByDate(?string $date): int;
}
