<?php

namespace Payment\Operations;

use User\UserAccount;

abstract class AbstractOperation implements OperationInterface
{
    private ?int $id = null;
    protected string $status = OperationStatuses::NEW;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        return $this->status = $status;
    }

    abstract public function toArray(): array;

    public function execute(): bool
    {
        if ($this->run()) {
            $this->status = OperationStatuses::COMPLETED;
            return true;
        }
        $this->status = OperationStatuses::FAILED;
        return false;
    }

    abstract public function revert(): bool;

    abstract protected function run();
}