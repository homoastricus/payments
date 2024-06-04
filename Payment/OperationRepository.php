<?php
namespace Payment;

use AllowDynamicProperties;
use Payment\Operations\CashOutOperation;
use Payment\Operations\FillUpOperation;
use Payment\Operations\OperationInterface;
use Payment\Operations\OperationStatuses;
use Payment\Operations\OperationTypes;
use Payment\Operations\RevertOperation;
use Payment\Operations\SendOperation;
use stdClass;

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
            $this->userMoneyRepository->setPendingOperations($account);
        }

        $this->log($operation);

        foreach ($userAccounts as $account) {
            $this->userMoneyRepository->tryRunPendingOperations($account);
        }
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
                $operation = new RevertOperation($this->getOperationById($operationData['parentOperationId']));
                $operation->setStatus($operationData['status']);
                $operation->setId($operationData['id']);
                break;
        }

        return $operation;
    }

    /**
     * @param int $userId
     * @return OperationInterface[]
     */
    public function getPendingOperationsByUserId(int $userId): array
    {

        echo 'Search Pending operations for user: '. $userId . PHP_EOL;
        $logData = $this->readStorage();

        $userOperationsData = [];

        /** @var stdClass $logRecord */
        foreach ($logData as $logRecord) {
            if (
                $this->isOperationRelatedToUser($userId, (array)$logRecord)
                && $this->isPendingOperation($logRecord->id, $logData)
            ) {
                $userOperationsData[] = (array)$logRecord;
            }
        }

        $pendingOperations = [];
        $operationIds = '';
        foreach ($userOperationsData as $operationData) {
            $userAccounts = $this->getUserAccounts($operationData);
            $pendingOperations[] = new RevertOperation($this->buildOperation($userAccounts, $operationData));
            $operationIds .= ', ' . $operationData['id'];
        }

        echo "user $userId has " . count($pendingOperations)
            . ' revert pending operations for ids:' . $operationIds .  PHP_EOL;

        return $pendingOperations;
    }

    private function isOperationRelatedToUser(int $userId, array $operationData): bool
    {
        return
            (key_exists('to', $operationData) && $operationData['to'] == $userId)
            || (key_exists('userAccount', $operationData) && $operationData['userAccount'] == $userId);
    }

    private function isPendingOperation(int $operationId, array $logData): bool
    {
        $isPending = false;

        foreach ($logData as $logRecord) {
            if (
                $logRecord->type == OperationTypes::REVERT
                && $logRecord->parentOperationId == $operationId
                && $logRecord->status == OperationStatuses::FAILED) {
                $isPending = true;
            }

            if (
                $logRecord->type == OperationTypes::REVERT
                && $logRecord->parentOperationId == $operationId
                && $logRecord->status == OperationStatuses::COMPLETED) {
                $isPending = false;
            }
        }

        return $isPending;
    }
}