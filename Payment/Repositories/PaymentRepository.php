<?php

namespace Payment\Repositories;

readonly class PaymentRepository extends AbstractRepository implements PaymentRepositoryInterface
{

    private const PAYMENT_FILE = STORAGE_DIR . '/payments.json';

    protected function getFilePath(): string
    {
        return self::PAYMENT_FILE;
    }

    public function createOperation(array $operation): array
    {
        return $this->create($operation);
    }

    public function getOperationById(string $operation_id): ?array
    {
        return $this->getData($operation_id);
    }

    public function saveOperation(array $operation): void
    {
        $this->setData($operation['id'], $operation);
    }

    public function getOperations(): array
    {
        return $this->readStorage();
    }
}