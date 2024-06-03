<?php

namespace Payment\Operations;

interface OperationInterface
{
    public function toArray(): array;

    public function execute(): bool;
}