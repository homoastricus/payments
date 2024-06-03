<?php

namespace Payment;

use AllowDynamicProperties;
use \Payment\OperationRepository;

#[AllowDynamicProperties] class UserMoneyRepository extends Repository implements Payment
{
    private OperationRepository $operations;

    /**
     * @param $file
     * @param OperationRepository $operations
     */
    public function __construct($file, OperationRepository $operations)
    {
        $this->file = $file;
        $this->operations = $operations;
        parent::__construct($file);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getMoneyValue($user): mixed
    {
        return $this->getData($user);
    }

    /**
     * @param int $sender
     * @param int $receiver
     * @param int $value
     * @return bool
     */
    public function sendMoney(int $sender, int $receiver, int $value): bool
    {
        $senderMoney = $this->getMoneyValue($sender);
        if ($senderMoney < $value) {
            return false;
        }
        $senderMoneyResult = $senderMoney - $value;
        $this->setMoneyValue($sender, $senderMoneyResult);

        $receiverMoneyResult = $this->getMoneyValue($receiver) + $value;
        $this->setMoneyValue($receiver, $receiverMoneyResult);

        $this->operations->Log(['from' => $sender, 'to' => $receiver, 'sum' => $value, 'type' => 'send']);
        return true;
    }

    /**
     * @param int $user
     * @param int $value
     * @return bool
     */
    public function fillUpMoney(int $user, int $value): bool
    {
        $userMoney = $this->getMoneyValue($user);
        $this->setMoneyValue($user, $userMoney + $value);
        $this->operations->Log(['user' => $user, 'value' => $value, 'type' => 'incoming']);

        return true;
    }

    /**
     * @param int $user
     * @param int $value
     * @return bool
     */
    public function cashOutMoney(int $user, int $value): bool
    {
        $userMoney = $this->getMoneyValue($user);
        if ($userMoney < $value) {
            return false;
        }

        $userMoneyResult = $userMoney - $value;
        $this->setMoneyValue($user, $userMoneyResult);

        $this->operations->Log(['user' => $user, 'value' => $value, 'type' => 'outcoming']);
        return true;
    }

    /**
     * @param $user
     * @param $value
     * @return void
     */
    public function setMoneyValue($user, $value): void
    {
        $this->saveData($user, $value);
    }
}