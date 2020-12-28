<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;

interface ConditionInterface
{
    public function getName(): string;

    public function getDisplayName(): string;

    public function getFields(): array;

    public function isPro(): bool;

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void;
}
