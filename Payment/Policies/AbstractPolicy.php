<?php

namespace Payment\Policies;

use Payment\Exceptions\AuthorizeException;

abstract readonly class AbstractPolicy
{

    /** @throws AuthorizeException */
    public function authorize(string $ability, array $arguments = []): void
    {
        if (!$this->check($ability, $arguments)) {
            throw new AuthorizeException();
        }
    }

    public function check(string $ability, array $arguments = []): bool
    {
        return method_exists($this, $ability) ? $this->$ability(...$arguments) : false;
    }
}