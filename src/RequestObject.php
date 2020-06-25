<?php

declare(strict_types=1);

namespace Fesor\RequestObject;

use Symfony\Component\Validator\Constraint;

abstract class RequestObject
{
    private array $payload = [];

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function get(string $name, $default = null)
    {
        return $this->has($name) ? $this->payload[$name] : $default;
    }

    public function has($name): bool
    {
        return array_key_exists($name, $this->payload);
    }

    public function all(): array
    {
        return $this->payload;
    }

    public function validationGroup(array $payload): array
    {
        return [];
    }

    /**
     * @return Constraint|Constraint[]|null
     */
    abstract public function rules();
}
