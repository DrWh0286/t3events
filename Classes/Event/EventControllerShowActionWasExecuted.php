<?php

declare(strict_types=1);

namespace DWenzel\T3events\Event;

final class EventControllerShowActionWasExecuted
{
    public function __construct(private array $templateVariables)
    {
    }

    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    public function setTemplateVariables(array $templateVariables): void
    {
        $this->templateVariables = $templateVariables;
    }
}
