<?php

namespace Payment\Operations;

class RevertOperation extends AbstractOperation
{
    public function __construct(private OperationInterface $operation)
    {
    }
    public function toArray(): array
    {
        return [
            'parentOperationId' => $this->operation->getId(),
            'type' => OperationTypes::REVERT,
            'status' => $this->status
        ];
    }

    public function run(): bool
    {
        echo 'Try revert operation: ' . $this->operation->getId() . PHP_EOL;
        return $this->operation->revert();
    }

    public function revert(): bool
    {
        if ($this->operation->getStatus() != OperationStatuses::COMPLETED) {
            return false;
        }
        return $this->operation->execute();
    }


    public function getUserAccounts(): array
    {
        return $this->operation->getUserAccounts();
    }
}