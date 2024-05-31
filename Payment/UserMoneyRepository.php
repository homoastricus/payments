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
        $from_money = $this->getMoneyValue($sender);
        if ($from_money < $value) {
            return false;
        }
        $money_left = $from_money - $value;
        $this->setMoneyValue($sender, $money_left);
        $this->setMoneyValue($receiver, $value);
        $this->operations->Log(['from' => $sender, 'to' => $receiver, 'sum' => $value]);
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