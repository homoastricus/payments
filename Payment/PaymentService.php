<?php

namespace Payment;

use Exception;
use Payment\Dto\IncomingDto;
use Payment\Dto\OutcomingDto;
use Payment\Dto\SendDto;
use Payment\Exceptions\AuthorizeException;
use Payment\Models\Debt;
use Payment\Models\Incoming;
use Payment\Models\Outcoming;
use Payment\Models\Send;
use Payment\Policies\PaymentPolicy;

readonly class PaymentService
{

    public function __construct(
        private RepositoryService $repositoryService,
        private PaymentPolicy     $paymentPolicy
    )
    {
    }

    /**
     * @param string $date format: RepositoryInterface::DATE_FORMAT
     * @return array<Incoming|Outcoming|Send>
     * @throws Exception
     */
    public function getOperationsByDate(string $date): array
    {
        return $this->repositoryService->getOperationsByDate($date);
    }

    /** @throws Exception */
    public function getOperationsSumByDate(?string $date = null): int
    {
        $operations = $date ? $this->getOperationsByDate($date) : $this->repositoryService->getOperations();
        return array_sum(array_column($operations, 'value'));
    }

    /** @throws AuthorizeException|Exception */
    public function incomingMoney(IncomingDto $incomingDto): Incoming
    {
        $receiver = $this->repositoryService->getUserByIdOrFail($incomingDto->receiver_id);
        $this->paymentPolicy->authorize('incoming', [$receiver, $incomingDto]);
        $incoming = $this->repositoryService->credit($incomingDto, $receiver);
        $this->debtRecovery($incoming->receiver_id);
        return $incoming;
    }

    /** @throws AuthorizeException|Exception */
    public function outcomingMoney(OutcomingDto $outcomingDto): Outcoming
    {
        $sender = $this->repositoryService->getUserByIdOrFail($outcomingDto->sender_id);
        $this->paymentPolicy->authorize('outcoming', [$sender, $outcomingDto]);
        return $this->repositoryService->debit($outcomingDto, $sender);
    }

    /** @throws AuthorizeException|Exception */
    public function sendMoney(SendDto $sendDto): Send
    {
        $sender = $this->repositoryService->getUserByIdOrFail($sendDto->sender_id);
        $receiver = $this->repositoryService->getUserByIdOrFail($sendDto->receiver_id);
        $this->paymentPolicy->authorize('send', [$sender, $receiver, $sendDto]);
        $send = $this->repositoryService->send($sender, $receiver, $sendDto);
        $this->debtRecovery($send->receiver_id);
        return $send;
    }

    /** @throws AuthorizeException|Exception */
    public function revert(string $operation_id): Send|Debt
    {
        /** @var Send $send */
        $send = $this->repositoryService->getOperationById($operation_id);
        $this->paymentPolicy->authorize('revert', [$send]);
        try {
            $revert = $this->sendMoney($send->createRevertDto());
        } catch (AuthorizeException $e) {
            return $this->repositoryService->firstOrCreateDebt($send);
        }
        $this->repositoryService->revert($revert, $send);
        return $revert;
    }

    /** @throws AuthorizeException|Exception */
    private function debtRecovery(string $user_id): void
    {
        $debts = $this->repositoryService->getDebtsByUserId($user_id);
        foreach ($debts as $debt) {
            if ($this->revert($debt->basis) instanceof Debt) {
                break;
            }
        }
    }
}