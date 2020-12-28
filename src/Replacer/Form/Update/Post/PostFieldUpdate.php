<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Update\AbstractUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\StringUpdateTrait;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class PostFieldUpdate extends AbstractUpdate
{
    use StringUpdateTrait;

    public function __construct(AbstractTarget $target)
    {
        parent::__construct('update_post', \__('Custom post type field', 'adr'), false, $target);
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
                    'label'  => \__('Custom post type field', 'adr'),
                    'values' => [
                        ['label' => \__('Title', 'adr'), 'value' => 'post_title'],
                        ['label' => \__('Content', 'adr'), 'value' => 'post_content'],
                        ['label' => \__('Excerpt', 'adr'), 'value' => 'post_excerpt'],
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
            ['post_title', 'post_content', 'post_excerpt']
        );

        if (false === $field) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $setValue = $this->getReplaceValue($this->getName(), "{$wpdb->posts}.{$field}", $parentKey);

        if (null === $setValue) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setSet("{$wpdb->posts}.{$field}", $setValue);
    }
}
