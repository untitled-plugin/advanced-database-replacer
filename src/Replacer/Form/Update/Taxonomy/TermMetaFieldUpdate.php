<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update\Taxonomy;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Update\AbstractUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\StringUpdateTrait;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class TermMetaFieldUpdate extends AbstractUpdate
{
    use StringUpdateTrait;

    public function __construct(AbstractTarget $target)
    {
        parent::__construct('update_term_meta', \__('Custom term field', 'adr'), false, $target);
    }

    public function getFields(): array
    {
        return \array_merge(
            [
                "{$this->getName()}_field" => FieldFactory::addSelect(
                    "{$this->getName()}_field",
                    [
                        'conditions' => [
                            ["{$this->target->getName()}_update_type", $this->getName()],
                            ['target_type', $this->target->getName()],
                        ],
                        'label'  => \__('Custom meta field', 'adr'),
                        'values' => function (): array {
                            foreach ($this->getTermMetaKeys() as $key) {
                                $values[] = ['label' => $key, 'value' => $key];
                            }

                            return $values ?? [];
                        },
                    ]
                ),
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
            $this->getTermMetaKeys()
        );

        if (false === $field) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $setValue = $this->getReplaceValue($this->getName(), "{$wpdb->usermeta}.meta_value", $parentKey);

        if (null === $setValue) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables($wpdb->termmeta, "{$wpdb->termmeta}.term_id", "{$wpdb->terms}.term_id");
        $queryProcessor->setMustWhere("{$wpdb->termmeta}.meta_key", '=', "'{$field}'");
        $queryProcessor->setSet("{$wpdb->termmeta}.meta_value", $setValue);
    }

    private function getTermMetaKeys(): array
    {
        global $wpdb;

        return $wpdb->get_col(
            "SELECT meta_key
			FROM {$wpdb->termmeta}
			GROUP BY meta_key
			ORDER BY meta_key"
        );
    }
}
