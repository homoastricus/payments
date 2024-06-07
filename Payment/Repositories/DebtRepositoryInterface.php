<?php

namespace Payment\Repositories;

interface DebtRepositoryInterface extends RepositoryInterface
{

    public function addDebt(array $debt): array;

    public function getDebts(): array;

    public function removeDebtByBasis(int $basis): void;
}