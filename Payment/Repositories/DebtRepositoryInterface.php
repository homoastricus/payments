<?php

namespace Payment\Repositories;

interface DebtRepositoryInterface extends RepositoryInterface
{

    public function createDebt(array $debt): array;

    public function getDebts(): array;

    public function removeDebtByBasis(string $basis): void;
}