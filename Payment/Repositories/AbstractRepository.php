<?php

namespace Payment\Repositories;

use DateTime;

abstract readonly class AbstractRepository implements RepositoryInterface
{

    private string $file;

    public function __construct()
    {
        $this->file = $this->getFilePath();
    }

    abstract protected function getFilePath(): string;

    protected function readStorage(): array
    {
        $file = file_get_contents($this->file);
        return (array)json_decode($file, true);
    }

    protected function getData(string $key, mixed $default = null): mixed
    {
        return $this->readStorage()[$key] ?? $default;
    }

    protected function setData(string $key, mixed $value): void
    {
        $data = $this->readStorage();
        $data[$key] = $value;
        $this->save($data);
    }

    protected function addData(mixed $value): void
    {
        $data = $this->readStorage();
        $data[] = $value;
        $this->save($data);
    }

    protected function save(mixed $data): void
    {
        file_put_contents($this->file, json_encode($data));
    }

    protected function newId(): string
    {
        return uniqid('', true);
    }

    protected function create(array $entity): array
    {
        $entity['date'] = (new DateTime())->format(self::DATE_FORMAT);
        $entity['id'] = $this->newId();
        $this->setData($entity['id'], $entity);
        return $entity;
    }
}