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

        return $this->operation->revert();
    }

    public function revert(): bool
    {
        return $this->operation->execute();
    }


    public function getUserAccounts(): array
    {
        return $this->operation->getUserAccounts();
    }
}