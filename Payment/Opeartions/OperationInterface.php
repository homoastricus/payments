<?php

namespace Payment\Operations;

interface OperationInterface
{
    public function getId(): ?int;
    public function setId(int $id);

    public function getStatus(): string;
    public function setStatus(string $status);
    public function toArray(): array;

    public function execute(): bool;

    public function revert(): bool;

    public function getUserAccounts():array;
}