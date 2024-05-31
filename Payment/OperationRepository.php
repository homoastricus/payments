<?php
namespace Payment;

use AllowDynamicProperties;

#[AllowDynamicProperties] class OperationRepository extends Repository implements LogOperation
{
    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        parent::__construct($file);
    }

    /**
     * @param array $operation
     * @return void
     */
    public function Log(array $operation): void
    {
        $id = $this->newLogId();
        $operation['id'] = $id;
        $this->addData($operation);
    }

    private function newLogId(): int{
        $storage = $this->readStorage();
        $current_count = count($storage);
        return $current_count+1;
    }
}