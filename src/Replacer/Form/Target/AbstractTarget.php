<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Target;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\AbstractForm;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\ConditionInterface;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Update\UpdateInterface;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

abstract class AbstractTarget implements TargetInterface
{
    protected $conditions = [];

    protected $updates = [];

    protected $form;

    protected $name;

    protected $displayName;

    protected $isPro;

    public function __construct(string $name, string $displayName, bool $isPro, AbstractForm $form)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->isPro = $isPro;
        $this->form = $form;
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

    public function setCondition(ConditionInterface ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $this->conditions[$condition->getName()] = $condition;
        }

        return $this;
    }

    public function setUpdate(UpdateInterface ...$updates): self
    {
        foreach ($updates as $update) {
            $this->updates[$update->getName()] = $update;
        }

        return $this;
    }

    public function getConditionsFields(): array
    {
        $conditionDefaultField = [
            "{$this->getName()}_condition_type" => FieldFactory::addSelect(
                "{$this->getName()}_condition_type",
                [
                    'label'  => \__('Condition type', 'adr'),
                    'values' => function (): array {
                        foreach ($this->getConditions() as $condition) {
                            $values[] = [
                                'value' => $condition->getName(),
                                'label' => $condition->getDisplayName(),
                                'isPro' => $condition->isPro(),
                            ];
                        }

                        return $values ?? [];
                    },
                ]
            ),
        ];

        foreach ($this->getConditions() as $condition) {
            $fields = \array_merge($fields ?? [], $condition->getFields());
        }

        return [
            "{$this->getName()}_conditions" => FieldFactory::addRelationsRepeater(
                "{$this->getName()}_conditions",
                [
                    'conditions' => [['target_type', $this->getName()]],
                    'fields'     => \array_merge($conditionDefaultField, $fields ?? []),
                ]
            ),
        ];
    }

    public function getUpdatesFields(): array
    {
        $updatesDefaultField = [
            "{$this->getName()}_update_type" => FieldFactory::addSelect(
                "{$this->getName()}_update_type",
                [
                    'label'      => \__('Update type', 'adr'),
                    'conditions' => [['target_type', $this->getName()]],
                    'values'     => function (): array {
                        foreach ($this->getUpdates() as $update) {
                            $values[] = ['value' => $update->getName(), 'label' => $update->getDisplayName()];
                        }

                        return $values ?? [];
                    },
                ]
            ),
        ];

        foreach ($this->getUpdates() as $update) {
            $fields = \array_merge($fields ?? [], $update->getFields());
        }

        return \array_merge($updatesDefaultField, $fields ?? []);
    }

    public function getConditions(): array
    {
        return (array) \apply_filters('adr\conditions', $this->conditions, $this);
    }

    public function getCondition(string $name): ?ConditionInterface
    {
        return $this->getConditions()[$name] ?? null;
    }

    public function getUpdates(): array
    {
        return (array) \apply_filters('adr\updates', $this->updates, $this);
    }

    public function getUpdate(string $name): ?UpdateInterface
    {
        return $this->getUpdates()[$name] ?? null;
    }

    protected function executeConditionsQuery(QueryProcessor $queryProcessor): void
    {
        $conditionsKey = "conditions.{$this->getName()}_conditions";
        $conditions = Sanitizer::sanitizePost($conditionsKey, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (false === $conditions) {
            return;
        }

        foreach ((array) $conditions as $index => $condition) {
            $conditionType = Sanitizer::sanitizePost(
                "{$conditionsKey}.{$index}.{$this->getName()}_condition_type",
                FILTER_SANITIZE_STRING,
                null,
                \array_keys($this->getConditions())
            );

            $condition = $this->getCondition((string) $conditionType);

            if (null === $condition) {
                continue;
            }

            $condition->executeQuery($queryProcessor, "{$conditionsKey}.{$index}");
        }
    }

    protected function executeUpdateQuery(QueryProcessor $queryProcessor): void
    {
        $updateTypeKey = "update.{$this->getName()}_update_type";
        $updateType = Sanitizer::sanitizePost(
            $updateTypeKey,
            FILTER_SANITIZE_STRING,
            null,
            \array_keys($this->getUpdates())
        );

        $update = $this->getUpdate((string) $updateType);

        if (null === $update) {
            $queryProcessor->lastError = \sprintf(
                \__('%s target contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $update->executeQuery($queryProcessor, 'update');
    }
}
