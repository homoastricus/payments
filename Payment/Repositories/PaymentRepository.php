<?php

namespace Payment\Repositories;

use DateTime;

readonly class PaymentRepository extends AbstractRepository implements PaymentRepositoryInterface
{

    private const PAYMENT_FILE = STORAGE_DIR . '/payments.json';

    public function __construct()
    {
        parent::__construct(self::PAYMENT_FILE);
    }

    public function createOperation(array $operation): array
    {
        $operation['id'] = $this->newId();
        $operation['date'] = (new DateTime())->format(self::DATE_FORMAT);
        $this->setData($operation['id'], $operation);
        return $operation;
    }

    public function getOperationById(int $operation_id): ?array
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