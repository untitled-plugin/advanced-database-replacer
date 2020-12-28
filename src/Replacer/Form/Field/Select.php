<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

class Select extends AbstractField
{
    public $values;

    public $multiple;

    public function __construct(string $name, array $settings)
    {
        parent::__construct($name, $settings);
        $this->values = $settings['values'];
        $this->multiple = $settings['multiple'];
    }
}
