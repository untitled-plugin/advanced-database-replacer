<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update\Taxonomy;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Update\AbstractUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\StringUpdateTrait;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class TermFieldUpdate extends AbstractUpdate
{
    use StringUpdateTrait;

    public function __construct(AbstractTarget $target)
    {
        parent::__construct('update_term', \__('Taxonomy term field', 'adr'), false, $target);
    }

    public function getFields(): array
    {
        return \array_merge(
            [
                "{$this->getName()}_field" => FieldFactory::addSelect("{$this->getName()}_field", [
                    'conditions' => [
                        ["{$this->target->getName()}_update_type", $this->getName()],
                        ['target_type', $this->target->getName()],
                    ],
                    'label'  => \__('Taxonomy term field', 'adr'),
                    'values' => [
                        ['label' => \__('Name', 'adr'), 'value' => 'name'],
                        ['label' => \__('Description', 'adr'), 'value' => 'description'],
                    ],
                ]),
            ],
            $this->getStringReplaceFields($this->getName(), $this->target->getName())
        );
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $field = Sanitizer::sanitizePost(
            "{$parentKey}.{$this->getName()}_field",
            FILTER_SANITIZE_STRING,
            null,
            ['name', 'description']
        );

        if (false === $field) {
            return;
        }

        switch ($field) {
            case 'name':
                $fieldName = "{$wpdb->terms}.name";
                break;
            case 'description':
                $fieldName = "{$wpdb->term_taxonomy}.description";
                break;
            default:
                $fieldName = null;
        }

        if (null === $fieldName) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $setValue = $this->getReplaceValue($this->getName(), $fieldName, $parentKey);

        if (null === $setValue) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setSet($fieldName, $setValue);
    }
}
