<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;

interface UpdateInterface
{
    public function getName(): string;

    public function getDisplayName(): string;

    public function getFields(): array;

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void;
}
