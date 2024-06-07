<?php

namespace Payment\Policies;

use Payment\Dto\IncomingDto;
use Payment\Dto\OutcomingDto;
use Payment\Dto\SendDto;
use Payment\Models\Send;
use Payment\Models\User;
use Payment\RepositoryService;

readonly class PaymentPolicy extends AbstractPolicy
{

    public function __construct(private RepositoryService $repositoryService)
    {
    }

    protected function incoming(User $receiver, IncomingDto $incomingDto): bool
    {
        return $incomingDto->value > 0;
    }

    protected function outcoming(User $sender, OutcomingDto $outcomingDto): bool
    {
        return $outcomingDto->value > 0
            && $sender->balance >= $outcomingDto->value
            && !$this->repositoryService->checkDebtByUserId($sender->id);
    }

    protected function send(User $sender, User $receiver, SendDto $sendDto): bool
    {
        return $sendDto->value > 0
            && $sender->balance >= $sendDto->value;
    }

    protected function revert(mixed $operation): bool
    {
        return $operation instanceof Send
            && !$operation->revert_operation_id;
    }
}