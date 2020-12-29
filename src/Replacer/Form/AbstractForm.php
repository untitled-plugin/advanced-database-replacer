<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form;

use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetGroupFieldsInterface;
use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetInterface;

abstract class AbstractForm
{
    private $targets = [];

    public function setTarget(TargetInterface ...$targets): self
    {
        foreach ($targets as $target) {
            $this->targets[$target->getName()] = $target;
        }

        return $this;
    }

    public function getFields(): array
    {
        return \array_merge($this->getTargetsFields(), $this->getConditionsFields(), $this->getUpdatesFields());
    }

    public function getFieldsArray(): array
    {
        $fields = \json_decode(\json_encode($this->getFields()), true);

        $parsedFields = $this->recursiveParse($fields);

        \usort($parsedFields, function ($a, $b): int {
            return $a['id'] <=> $b['id'];
        });

        return $parsedFields;
    }

    public function getTargets(): array
    {
        return (array) \apply_filters('adr\targets', $this->targets);
    }

    public function getTarget(string $name): ?TargetInterface
    {
        return $this->getTargets()[$name] ?? null;
    }

    private function recursiveParse(array $fields, ?int $parentId = null): array
    {
        static $counter = 0;
        $parsedFields = [];

        foreach ($fields as $field) {
            $field['parent'] = $parentId;
            $field['id'] = $counter++;

            if (false === \array_key_exists('fields', $field)) {
                $parsedFields[] = $field;
                continue;
            }

            $parsedFields = \array_merge($parsedFields, $this->recursiveParse($field['fields'], $field['id']));
            unset($field['fields']);
            $parsedFields[] = $field;
        }

        return $parsedFields;
    }

    private function getTargetsFields(): array
    {
        $targetsDefaultField = [
            'target_type' => FieldFactory::addSelect(
                'target_type',
                [
                    'label'  => \__('The type of content', 'adr'),
                    'values' => function (): array {
                        foreach ($this->getTargets() as $target) {
                            $values[] = [
                                'value' => $target->getName(),
                                'label' => $target->getDisplayName(),
                                'isPro' => $target->isPro(),
                            ];
                        }

                        return $values ?? [];
                    },
                ]
            ),
        ];

        foreach ($this->getTargets() as $target) {
            if (false === $target instanceof TargetGroupFieldsInterface) {
                false;
            }

            $fields = \array_merge($fields ?? [], $target->getTargetGroupFields());
        }

        return [
            'target' => FieldFactory::addGroup(
                'target',
                [
                    'label' => \__('In the first step of the replacement, you have to decide that type of database
                        data will be replaced. The form will guide how to select only that content that you really want
                        to update. And please remember, everything that will happen at the end of this form can make
                        irreversible changes in the database, so before we will start, please make sure that you
                        backup your database.', 'adr'),
                    'fields' => \array_merge($targetsDefaultField, $fields ?? []),
                ]
            ),
        ];
    }

    private function getConditionsFields(): array
    {
        foreach ($this->getTargets() as $target) {
            $conditionsFields = \array_merge($conditionsFields ?? [], $target->getConditionsFields());
        }

        return [
            'conditions' => FieldFactory::addGroup(
                'conditions',
                [
                    'label' => \__('In the second step of the replacement, you can specify some additional conditions
                        that will be included in the update query. This step allows you to filter more specific
                        groups of content, for example, you can select posts only from one category.', 'adr'),
                    'fields' => \array_merge(
                        [
                            'relation' => FieldFactory::addSelect('relation', [
                                'label'   => \__('Relation between conditions', 'adr'),
                                'default' => 'and',
                                'values'  => [
                                    ['value' => 'and', 'label' => 'AND'],
                                    ['value' => 'or', 'label' => 'OR'],
                                ],
                            ]),
                        ],
                        $conditionsFields ?? []
                    ),
                ]
            ),
        ];
    }

    private function getUpdatesFields(): array
    {
        foreach ($this->getTargets() as $target) {
            $updatesFields = \array_merge($updatesFields ?? [], $target->getUpdatesFields());
        }

        return [
            'update' => FieldFactory::addGroup(
                'update',
                [
                    'label' => \__('And finally, in the last step of the replacement, let\'s define which exactly data
                        and how will be replaced, for example, change all tags description to uppercase.', 'adr'),
                    'fields' => $updatesFields ?? [],
                ]
            ),
        ];
    }
}
