<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

class RelationsRepeater extends AbstractField
{
    public $fields;

    public function __construct(string $name, array $settings)
    {
        parent::__construct($name, $settings);
        $this->fields = $settings['fields'];
    }
}
