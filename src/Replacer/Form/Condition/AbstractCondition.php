<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition;

use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;

abstract class AbstractCondition implements ConditionInterface
{
    protected $name;

    protected $displayName;

    protected $target;

    protected $isPro;

    public function __construct(string $name, string $displayName, bool $isPro, AbstractTarget $target)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->isPro = $isPro;
        $this->target = $target;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function isPro(): bool
    {
        return $this->isPro;
    }
}
