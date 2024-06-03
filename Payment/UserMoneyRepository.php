<?php

namespace Payment;

use AllowDynamicProperties;
use Payment\Operations\CashOutOperation;
use Payment\Operations\FillUpOperation;
use Payment\Operations\SendOperation;
use \Payment\OperationRepository;
use User\UserAccount;

#[AllowDynamicProperties] class UserMoneyRepository extends Repository implements Payment
{
    private OperationRepository $operations;

    /**
     * @param $file
     * @param OperationRepository $operations
     */
    public function __construct($file)
    {
        $this->file = $file;
        parent::__construct($file);
    }

    public function setOperationRepository(OperationRepository $operations)
    {
        $this->operations = $operations;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getMoneyValue($user): mixed
    {
        return $this->getData($user);
    }

    public function getUserById(int $userId): ?UserAccount
    {
        $moneyValue = $this->getMoneyValue($userId);

        if ($moneyValue === null) {
            return null;
        }

        return new UserAccount($userId, $moneyValue);
    }

    /**
     * @param int $sender
     * @param int $receiver
     * @param int $value
     * @return bool
     */
    public function sendMoney(int $sender, int $receiver, int $value): bool
    {
        $sender = $this->getUserById($sender);
        $receiver = $this->getUserById($receiver);

        if (empty($sender) || empty($receiver)) {
            return false;
        }
        $operation = new SendOperation($sender,$receiver,$value);

        if (!$operation->execute()) {
            return false;
        }
        $this->saveUserAccount($sender);
        $this->saveUserAccount($receiver);
        $this->operations->Log($operation);
        return true;

    }

    /**
     * @param int $user
     * @param int $value
     * @return bool
     */
    public function fillUpMoney(int $user, int $value): bool
    {
        $userAccount = $this->getUserById($user);

        if (is_null($userAccount)) {
            return false;
        }
        $operation = new FillUpOperation($userAccount,$value);
        $operation->execute();

        $this->saveUserAccount($userAccount);
        $this->operations->Log($operation);

        return true;
    }

    /**
     * @param int $user
     * @param int $value
     * @return bool
     */
    public function cashOutMoney(int $user, int $value): bool
    {
        $userAccount = $this->getUserById($user);
        if (is_null($userAccount))
        {
            return false;
        }
        $operation = new CashOutOperation($userAccount,$value);

        if (!$operation->execute()) {
            return false;
        }
        $this->saveUserAccount($userAccount);
        $this->operations->Log($operation);

        return true;
    }

    /**
     * @param UserAccount $userAccount
     * @return void
     */
    public function saveUserAccount(UserAccount $userAccount): void
    {
        $this->saveData($userAccount->getId(), $userAccount->getMoneyValue());
    }
}