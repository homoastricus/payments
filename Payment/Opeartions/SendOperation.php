<?php

namespace Payment\Operations;

use User\UserAccount;

class SendOperation extends AbstractOperation
{
    private ?int $id = null;

    public function __construct(
        private UserAccount $sender,
        private UserAccount $receiver,
        private int $value,
    )
    {
    }

    public function run(): bool
    {
        return $this->send($this->sender, $this->receiver);
    }

    public function revert(): bool
    {
        return $this->send($this->receiver, $this->sender);
    }

    private function send(UserAccount $userA, UserAccount $userB): bool
    {

        echo 'Start ' . OperationTypes::SEND . ' operation.'
            . ' FROM: ' . $this->sender->getId()
            . ' TO: ' . $this->receiver->getId()
            . ' Value: ' .$this->value . PHP_EOL;

        $senderMoney = $userA->getMoneyValue();
        if ($senderMoney < $this->value) {
            echo 'Not enough money on account' . PHP_EOL;
            return false;
        }
        $senderMoneyResult = $senderMoney - $this->value;
        $userA->setMoneyValue($senderMoneyResult);

        $receiverMoneyResult = $userB->getMoneyValue() + $this->value;
        $userB->setMoneyValue($receiverMoneyResult);

        echo 'Finish ' . OperationTypes::INCOMING . ' operation.' . PHP_EOL;
        return true;
    }

    public function toArray(): array
    {
        $info = [
            'from' => $this->sender->getId(),
            'to' => $this->receiver->getId(),
            'sum' => $this->value,
            'type' => OperationTypes::SEND,
        ];

        return array_merge(parent::toArray(), $info);
    }

    public function getUserAccounts(): array
    {
        return [
            $this->sender,
            $this->receiver
        ];
    }
}