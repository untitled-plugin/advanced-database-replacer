<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

class AsyncSelect extends AbstractField
{
    public $valuesCallback;

    public $valuesCallbackParams;

    public $multiple;

    public function __construct(string $name, array $settings)
    {
        parent::__construct($name, $settings);
        $this->multiple = $settings['multiple'];
        $this->valuesCallback = $settings['values_callback'];
        $this->valuesCallbackParams = $settings['values_callback_params'];
    }
}
