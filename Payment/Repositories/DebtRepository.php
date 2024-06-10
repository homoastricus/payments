<?php

namespace Payment\Repositories;

readonly class DebtRepository extends AbstractRepository implements DebtRepositoryInterface
{

    private const DEBT_FILE = STORAGE_DIR . "/debts.json";

    protected function getFilePath(): string
    {
        return self::DEBT_FILE;
    }

    public function createDebt(array $debt): array
    {
        return $this->create($debt);
    }

    public function removeDebtByBasis(string $basis): void
    {
        $debts = $this->readStorage();
        $bases = array_column($debts, 'id', 'basis');
        if (isset($bases[$basis])) {
            unset($debts[$bases[$basis]]);
            $this->save($debts);
        }
    }

    public function getDebts(): array
    {
        return $this->readStorage();
    }
}