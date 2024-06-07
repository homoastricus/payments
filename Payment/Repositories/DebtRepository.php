<?php

namespace Payment\Repositories;

use DateTime;

readonly class DebtRepository extends AbstractRepository implements DebtRepositoryInterface
{

    private const DEBT_FILE = STORAGE_DIR . "/debts.json";

    public function __construct()
    {
        parent::__construct(self::DEBT_FILE);
    }

    public function addDebt(array $debt): array
    {
        $debt['id'] = $this->newId();
        $debt['date'] = (new DateTime())->format(self::DATE_FORMAT);
        $this->setData($debt['id'], $debt);
        return $debt;
    }

    public function removeDebtByBasis(int $basis): void
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