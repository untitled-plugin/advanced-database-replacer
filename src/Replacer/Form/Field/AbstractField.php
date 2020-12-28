<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

abstract class AbstractField
{
    public $name;

    public $type;

    public $label;

    public $conditions;

    public $default;

    public function __construct(string $name, array $settings)
    {
        $this->name = $name;
        $this->label = $settings['label'];
        $this->type = $settings['type'];
        $this->conditions = $settings['conditions'];
        $this->default = $settings['default'];
    }
}
