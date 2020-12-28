<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

class Input extends AbstractField
{
    public $inputType;

    public function __construct(string $name, array $settings)
    {
        parent::__construct($name, $settings);
        $this->inputType = $settings['input_type'];
    }
}
