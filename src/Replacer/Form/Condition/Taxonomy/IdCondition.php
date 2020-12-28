<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Taxonomy;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class IdCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('terms_ids', \__('Custom terms', 'adr'), false, $target);

        \add_action("wp_ajax_{$this->getName()}_ids", [$this, 'handleAsyncIdsSelect']);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_ids" => FieldFactory::addAsyncSelect(
                "{$this->getName()}_ids",
                [
                    'conditions'             => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'                  => \__('Custom terms', 'adr'),
                    'multiple'               => true,
                    'values_callback'        => "{$this->getName()}_ids",
                    'values_callback_params' => ["{$this->target->getName()}_tax"],
                ]
            ),
        ];
    }

    public function handleAsyncIdsSelect(): void
    {
        $nonce = Sanitizer::sanitizePost('nonce', FILTER_SANITIZE_STRING);

        if (false === \wp_verify_nonce($nonce, 'adr_nonce')) {
            \wp_send_json_error(\__('Invalid nonce data, please try again!', 'adr'));
        }

        $search = Sanitizer::sanitizePost('value', FILTER_SANITIZE_STRING);
        $taxonomies = Sanitizer::sanitizePost("{$this->target->getName()}_tax", FILTER_SANITIZE_STRING);

        $args = [
            'taxonomy'   => \explode(',', $taxonomies),
            'hide_empty' => false,
            'search'     => $search,
        ];

        foreach (\get_terms($args) as $term) {
            $data[] = [
                'label' => $term->name,
                'value' => $term->term_id,
            ];
        }

        \wp_send_json_success($data ?? []);
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $termsIds = Sanitizer::sanitizePost(
            "{$parentKey}.{$this->getName()}_ids",
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (false === $termsIds) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $termsIds = \implode(',', \array_map(function ($term) {
            return (int) $term['key'];
        }, $termsIds));

        if (empty($termsIds)) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setWhere("{$wpdb->terms}.term_id", 'IN', "({$termsIds})");
    }
}
