<?php

declare(strict_types=1);

namespace DWenzel\T3events\Event;

final class EntityNotFoundErrorWasTriggered
{
    public function __construct(private array $parameters)
    {
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
