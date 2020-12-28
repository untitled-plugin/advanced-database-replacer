<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Field;

class FieldFactory
{
    public const INPUT = 'input';
    public const SELECT = 'select';
    public const ASYNC_SELECT = 'async-select';
    public const RELATIONS_REPEATER = 'condition-repeater';
    public const GROUP = 'group';

    public static function addInput(string $name, array $field): Input
    {
        $defaultSettings = [
            'type'        => self::INPUT,
            'label'       => \__('Input field label', 'adr'),
            'conditions'  => null,
            'default'     => null,
            'input_type'  => 'text',
        ];

        $settings = \array_replace_recursive($defaultSettings, $field);

        return new Input($name, $settings);
    }

    public static function addSelect(string $name, array $field): Select
    {
        $defaultSettings = [
            'type'        => self::SELECT,
            'label'       => \__('Select field label', 'adr'),
            'conditions'  => null,
            'multiple'    => false,
            'values'      => [],
            'default'     => null,
        ];

        $settings = \array_replace_recursive($defaultSettings, $field);

        if (\is_callable($settings['values'])) {
            $settings['values'] = \call_user_func($settings['values']);
        }

        return new Select($name, $settings);
    }

    public static function addAsyncSelect(string $name, array $field): AsyncSelect
    {
        $defaultSettings = [
            'type'                   => self::ASYNC_SELECT,
            'label'                  => \__('Async select label', 'adr'),
            'conditions'             => null,
            'multiple'               => false,
            'values_callback'        => null,
            'values_callback_params' => [],
            'default'                => null,
        ];

        $settings = \array_replace_recursive($defaultSettings, $field);

        return new AsyncSelect($name, $settings);
    }

    public static function addRelationsRepeater(string $name, array $field): RelationsRepeater
    {
        $defaultSettings = [
            'type'        => self::RELATIONS_REPEATER,
            'label'       => \__('Relations repeater label', 'adr'),
            'conditions'  => null,
            'fields'      => [],
            'default'     => null,
        ];

        $settings = \array_replace_recursive($defaultSettings, $field);

        return new RelationsRepeater($name, $settings);
    }

    public static function addGroup(string $name, array $field): ConditionGroup
    {
        $defaultSettings = [
            'type'       => self::GROUP,
            'label'      => \__('Group label', 'adr'),
            'conditions' => null,
            'fields'     => [],
            'default'    => null,
        ];

        $settings = \array_replace_recursive($defaultSettings, $field);

        return new ConditionGroup($name, $settings);
    }
}
