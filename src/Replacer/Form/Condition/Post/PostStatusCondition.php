<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class PostStatusCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('post_status', \__('Post status', 'adr'), false, $target);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_status" => FieldFactory::addSelect(
                "{$this->getName()}_status",
                [
                    'conditions' => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'      => \__('Post status', 'adr'),
                    'values'     => static function (): array {
                        foreach (\get_post_statuses() as $postStatus => $postStatusName) {
                            $values[] = ['value' => $postStatus, 'label' => $postStatusName];
                        }

                        return $values ?? [];
                    },
                ]
            ),
        ];
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $postStatus = Sanitizer::sanitizePost("{$parentKey}.{$this->getName()}_status", FILTER_SANITIZE_STRING);

        if (false === $postStatus) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setWhere("{$wpdb->posts}.post_status", '=', "'{$postStatus}'");
    }
}
