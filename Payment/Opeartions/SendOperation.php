<?php

namespace Payment\Operations;

use User\UserAccount;

class SendOperation implements OperationInterface
{

    public function __construct(
        private UserAccount $sender,
        private UserAccount $receiver,
        private int $value,
    )
    {
    }

    public function execute(): bool
    {
        $senderMoney = $this->sender->getMoneyValue();
        if ($senderMoney < $this->value) {
            return false;
        }
        $senderMoneyResult = $senderMoney - $this->value;
        $this->sender->setMoneyValue($senderMoneyResult);

        $receiverMoneyResult = $this->receiver->getMoneyValue() + $this->value;
        $this->receiver->setMoneyValue($receiverMoneyResult);
        return true;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->sender->getId(),
            'to' => $this->receiver->getId(),
            'sum' => $this->value,
            'type' => OperationTypes::SEND
        ];
    }

}