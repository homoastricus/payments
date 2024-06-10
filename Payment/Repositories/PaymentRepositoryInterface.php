<?php

namespace Payment\Repositories;

interface PaymentRepositoryInterface extends RepositoryInterface
{

    public function createOperation(array $operation): array;

    public function getOperations(): array;

    public function saveOperation(array $operation): void;

    public function getOperationById(string $operation_id): ?array;
}