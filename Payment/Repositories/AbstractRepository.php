<?php

namespace Payment\Repositories;

abstract readonly class AbstractRepository
{

    public function __construct(private string $file)
    {
    }

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

    protected function newId(): int
    {
        return array_key_last($this->readStorage()) + 1;
    }
}