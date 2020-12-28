<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Update\User;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Update\AbstractUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\StringUpdateTrait;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class UserFieldUpdate extends AbstractUpdate
{
    use StringUpdateTrait;

    public function __construct(AbstractTarget $target)
    {
        parent::__construct('update_user', \__('User field', 'adr'), false, $target);
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
                    'label'  => \__('User field', 'adr'),
                    'values' => [
                        ['label' => \__('Nice name', 'adr'), 'value' => 'user_nicename'],
                        ['label' => \__('Email address', 'adr'), 'value' => 'user_email'],
                        ['label' => \__('Display name', 'adr'), 'value' => 'display_name'],
                        ['label' => \__('Website URL', 'adr'), 'value' => 'user_url'],
                        ['label' => \__('Biography', 'adr'), 'value' => 'biography'],
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
            ['user_nicename', 'user_email', 'display_name', 'user_url', 'biography']
        );

        if (false === $field) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $setValue = $this->getReplaceValue($this->getName(), "{$wpdb->users}.{$field}", $parentKey);

        if (null === $setValue) {
            $queryProcessor->lastError = \sprintf(
                \__('%s update contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setSet("{$wpdb->users}.{$field}", $setValue);
    }
}
