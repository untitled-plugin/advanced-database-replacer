<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Update\AbstractUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\StringUpdateTrait;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class PostMetaFieldUpdate extends AbstractUpdate
{
    use StringUpdateTrait;

    public function __construct(AbstractTarget $target)
    {
        parent::__construct('update_meta', \__('Custom meta field', 'adr'), false, $target);
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
                        'values' => static function (): array {
                            foreach (\get_meta_keys() as $key) {
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
            \get_meta_keys()
        );

        if (false === $field) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $setValue = $this->getReplaceValue($this->getName(), "{$wpdb->postmeta}.meta_value", $parentKey);

        if (null === $setValue) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables($wpdb->postmeta, "{$wpdb->postmeta}.post_id", "{$wpdb->posts}.ID");
        $queryProcessor->setMustWhere("{$wpdb->postmeta}.meta_key", '=', "'{$field}'");
        $queryProcessor->setSet("{$wpdb->postmeta}.meta_value", $setValue);
    }
}
