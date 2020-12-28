<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class TermCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('terms', \__('Taxonomy term', 'adr'), false, $target);

        \add_action("wp_ajax_{$this->getName()}_term", [$this, 'handleAsyncTermSelect']);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_taxonomy" => FieldFactory::addSelect(
                "{$this->getName()}_taxonomy",
                [
                    'conditions' => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'      => \__('Taxonomy', 'adr'),
                    'default'    => 'category',
                    'values'     => static function (): array {
                        foreach (\get_taxonomies([], 'object') as $taxonomy) {
                            /** @var \WP_Taxonomy $taxonomy */
                            $values[] = [
                                'value' => $taxonomy->name,
                                'label' => $taxonomy->label,
                                'isPro' => false === \in_array($taxonomy->name, ['category', 'tag']),
                            ];
                        }

                        return $values ?? [];
                    },
                ]
            ),
            "{$this->getName()}_term" => FieldFactory::addAsyncSelect(
                "{$this->getName()}_term",
                [
                    'conditions' => [
                        ["{$this->target->getName()}_condition_type", $this->getName()],
                        ["{$this->getName()}_taxonomy!", null],
                    ],
                    'label'                  => \__('Term', 'adr'),
                    'values_callback'        => "{$this->getName()}_term",
                    'values_callback_params' => ["{$this->getName()}_taxonomy"],
                ]
            ),
        ];
    }

    public function handleAsyncTermSelect(): void
    {
        $nonce = Sanitizer::sanitizePost('nonce', FILTER_SANITIZE_STRING);

        if (false === \wp_verify_nonce($nonce, 'adr_nonce')) {
            \wp_send_json_error(\__('Invalid nonce data, please try again!', 'adr'));
        }

        $search = Sanitizer::sanitizePost('value', FILTER_SANITIZE_STRING);
        $taxonomy = Sanitizer::sanitizePost('terms_taxonomy', FILTER_SANITIZE_STRING, null, \get_taxonomies());

        $args = [
            'taxonomy'   => $taxonomy,
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

        $termId = Sanitizer::sanitizePost("{$parentKey}.{$this->getName()}_term.key", FILTER_VALIDATE_INT);

        if (false === $termId) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables(
            $wpdb->term_relationships,
            "{$wpdb->posts}.id",
            "{$wpdb->term_relationships}.object_id"
        );

        $queryProcessor->setTables(
            $wpdb->terms,
            "{$wpdb->terms}.term_id",
            "{$wpdb->term_relationships}.term_taxonomy_id"
        );

        $queryProcessor->setWhere("{$wpdb->terms}.term_id", '=', (int) $termId);
    }
}
