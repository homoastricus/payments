<?php

namespace Payment\Operations;

class RevertOperation extends AbstractOperation
{
    public function __construct(private OperationInterface $operation)
    {
    }
    public function toArray(): array
    {
        $info = [
            'parentOperationId' => $this->operation->getId(),
            'type' => OperationTypes::REVERT,
        ];

        return array_merge(parent::toArray(), $info);
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