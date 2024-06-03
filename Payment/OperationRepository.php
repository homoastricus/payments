<?php
namespace Payment;

use AllowDynamicProperties;
use Payment\Operations\CashOutOperation;
use Payment\Operations\FillUpOperation;
use Payment\Operations\OperationInterface;
use Payment\Operations\OperationsFactory;
use Payment\Operations\OperationTypes;
use Payment\Operations\RevertOperation;
use Payment\Operations\SendOperation;
use User\UserAccount;

#[AllowDynamicProperties] class OperationRepository extends Repository implements LogOperation
{
    private UserMoneyRepository $userMoneyRepository;
    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        parent::__construct($file);
    }

    public function setUserMoneyRepository(UserMoneyRepository $moneyRepository)
    {
        $this->userMoneyRepository = $moneyRepository;
    }

    /**
     * @param array $operation
     * @return void
     */
    public function log(OperationInterface $operation): void
    {
        $operation = $operation->toArray();
        $id = $this->newLogId();
        $operation['id'] = $id;

        $this->addData($operation);
    }

    private function newLogId(): int{
        $storage = $this->readStorage();
        $current_count = count($storage);
        return $current_count+1;
    }

    public function revert(int $operationId): void
    {
        $parentOperation = $this->getOperationById($operationId);
        $operation = new RevertOperation($parentOperation);

        $operation->execute();

        $userAccounts = $operation->getUserAccounts();
        foreach ($userAccounts as $account) {
            $this->userMoneyRepository->saveUserAccount($account);
        }

        $this->log($operation);
    }

    private function getOperationById(int $operationId): OperationInterface
    {
        $operationData = $this->getOperationDataById($operationId);
        $userAccounts = $this->getUserAccounts($operationData);
        return $this->buildOperation($userAccounts, $operationData);
    }

    private function getOperationDataById(int $operationId): array
    {
        $logData = $this->readStorage();

        /** @var \stdClass $data */
        foreach ($logData as $data) {
            if ($data->id == $operationId) {
                return (array)$data;
            }
        }
        return [];
    }

    private function getUserAccounts(array $operationData): array
    {
        $userAccountKeys = [
            'from',
            'to',
            'userAccount'
        ];

        $users = [];
        foreach ($operationData as $key=>$value) {
            if (in_array($key, $userAccountKeys)) {
                $users[$key] = $this->userMoneyRepository->getUserById($value);
            }
        }
        return $users;
    }

    private function buildOperation(array $userAccounts, array $operationData): OperationInterface
    {
        $operationType = $operationData['type'];

        switch ($operationType) {
            case OperationTypes::SEND:
                $operation = new SendOperation($userAccounts['from'],$userAccounts['to'], $operationData['sum']);
                $operation->setStatus($operationData['status']);
                $operation->setId($operationData['id']);
                break;

            case OperationTypes::INCOMING:
                $operation = new FillUpOperation($userAccounts['userAccount'], $operationData['value']);
                $operation->setStatus($operationData['status']);
                $operation->setId($operationData['id']);
                break;

            case OperationTypes::OUTCOMING:
                $operation = new CashOutOperation($userAccounts['userAccount'], $operationData['value']);
                $operation->setStatus($operationData['status']);
                $operation->setId($operationData['id']);
                break;

            case OperationTypes::REVERT:
                $operation = RevertOperation($this->getOperationById($operationData['parentOperationId']));
                $operation->setStatus($operationData['status']);
                $operation->setId($operationData['id']);
                break;
        }

        return $operation;
    }
}