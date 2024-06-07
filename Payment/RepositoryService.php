<?php

namespace Payment;

use DateTime;
use Exception;
use Payment\Dto\IncomingDto;
use Payment\Dto\OutcomingDto;
use Payment\Dto\SendDto;
use Payment\Enums\PaymentTypeEnum;
use Payment\Models\Debt;
use Payment\Models\Incoming;
use Payment\Models\Outcoming;
use Payment\Models\Send;
use Payment\Models\User;
use Payment\Repositories\DebtRepositoryInterface;
use Payment\Repositories\PaymentRepositoryInterface;
use Payment\Repositories\UserRepositoryInterface;

readonly class RepositoryService
{

    public function __construct(
        private UserRepositoryInterface    $userRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private DebtRepositoryInterface    $debtRepository,
    )
    {
    }

//    /**
//     * ACTIONS
//     */
    public function credit(IncomingDto $incomingDto, User $receiver): Incoming
    {
        $receiver->balance += $incomingDto->value;
        $this->userRepository->saveUser($receiver);
        $incoming = (array)$incomingDto + [
                'type' => PaymentTypeEnum::incoming->value,
            ];
        $incoming = $this->paymentRepository->createOperation($incoming);
        return new Incoming(...$incoming);
    }

    public function debit(OutcomingDto $outcomingDto, User $sender): Outcoming
    {
        $sender->balance -= $outcomingDto->value;
        $this->userRepository->saveUser($sender);
        $outcoming = (array)$outcomingDto + [
                'type' => PaymentTypeEnum::outcoming->value
            ];
        $outcoming = $this->paymentRepository->createOperation($outcoming);
        return new Outcoming(...$outcoming);
    }

    public function send(User $sender, User $receiver, SendDto $sendDto): Send
    {
        $sender->balance -= $sendDto->value;
        $this->userRepository->saveUser($sender);
        $receiver->balance += $sendDto->value;
        $this->userRepository->saveUser($receiver);

        $send = (array)$sendDto + [
                'type' => PaymentTypeEnum::send->value,
            ];
        $send = $this->paymentRepository->createOperation($send);

        return new Send(...$send);
    }

    public function revert(Send $revert, Send $revertable): void
    {
        $revert->revert_operation_id = $revertable->id;
        $this->paymentRepository->saveOperation((array)$revert);

        $revertable->revert_operation_id = $revert->id;
        $this->paymentRepository->saveOperation((array)$revertable);

        $this->debtRepository->removeDebtByBasis($revertable->id);
    }

//    /**
//     * USERS
//     */
    /** @throws Exception */
    public function getUserByIdOrFail(int $user_id): User
    {
        $user = $this->userRepository->getUserById($user_id);
        if (!$user) throw new Exception("Пользователя с id $user_id не существует");
        return new User(...$user);
    }

//    /**
//     * OPERATIONS
//     */
    /** @throws Exception */
    public function getOperationById(int $operation_id): Incoming|Outcoming|Send
    {
        $operation = $this->paymentRepository->getOperationById($operation_id);
        return $this->operationToModel($operation);
    }

    /** @throws Exception */
    public function operationToModel(array $operation): Incoming|Outcoming|Send
    {
        return match ($operation['type']) {
            PaymentTypeEnum::incoming->value => new Incoming(...$operation),
            PaymentTypeEnum::outcoming->value => new Outcoming(...$operation),
            PaymentTypeEnum::send->value => new Send(...$operation),
            default => throw new Exception('некорректный тип операции'),
        };
    }

    /**
     * @param string $date format: RepositoryInterface::DATE_FORMAT
     * @return array<Incoming|Outcoming|Send>
     * @throws Exception
     */
    public function getOperationsByDate(string $date): array
    {
        if (!DateTime::createFromFormat($this->paymentRepository::DATE_FORMAT, $date)) {
            throw new Exception('Некорректный формат даты. Требуется формат ' . $this->paymentRepository::DATE_FORMAT);
        }

        $operations = [];
        foreach ($this->getOperations() as $operation) {
            if ($operation['date'] === $date) {
                $operations[] = $this->operationToModel($operation);
            }
        }
        return $operations;
    }

    public function getOperations(): array
    {
        return $this->paymentRepository->getOperations();
    }

//    /**
//     * DEBT
//     */
    public function checkDebtByUserId(int $user_id): bool
    {
        return (bool)$this->getDebtsByUserId($user_id);
    }

    public function checkDebtByOperationId(int $operation_id): bool
    {
        return (bool)$this->getDebtByOperationId($operation_id);
    }

    public function getDebtByOperationId(int $operation_id): ?Debt
    {
        $debts = $this->debtRepository->getDebts();
        foreach ($debts as $debt) {
            if ($debt['basis'] === $operation_id) {
                return new Debt(...$debt);
            }
        }
        return null;
    }

    /** @return Debt[] */
    public function getDebtsByUserId(int $user_id): array
    {
        $debts = [];
        foreach ($this->debtRepository->getDebts() as $debt) {
            if ($debt['debtor_id'] === $user_id) {
                $debts[] = new Debt(...$debt);
            }
        }
        return $debts;
    }

    public function firstOrCreateDebt(Send $send): Debt
    {
        return $this->getDebtByOperationId($send->id) ?? $this->createDebt($send);
    }

    public function createDebt(Send $send): Debt
    {
        $debtDto = $send->createDebtDto();
        $debt = $this->debtRepository->addDebt((array)$debtDto);
        return new Debt(...$debt);
    }
}