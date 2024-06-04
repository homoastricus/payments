<?php

namespace User;

use Payment\Operations\OperationInterface;

class UserAccount
{
    /** @var OperationInterface[] $pendingOperations  */
    private array $pendingOperations = [];
    public function __construct(private int $id, private int $moneyValue)
    {}
    public function getId(): int
    {
        return $this->id;
    }
    public function getMoneyValue(): int
    {
        return $this->moneyValue;
    }

    public function setMoneyValue(int $moneyValue): void
    {
        $this->moneyValue = $moneyValue;
    }

    public function addPendingOperation(OperationInterface $operation)
    {
        $this->pendingOperations[] = $operation;
    }

    /**
     * @return OperationInterface[]
     */
    public function getPendingOperations(): array
    {
        return $this->pendingOperations;
    }
}