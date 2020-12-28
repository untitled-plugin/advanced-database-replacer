<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Target;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;

interface TargetInterface
{
    public function getName(): string;

    public function getDisplayName(): string;

    public function isPro(): bool;

    public function getConditionsFields(): array;

    public function getUpdatesFields(): array;

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void;
}
