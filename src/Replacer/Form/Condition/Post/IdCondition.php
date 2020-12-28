<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class IdCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('posts_ids', \__('Custom posts', 'adr'), false, $target);

        \add_action("wp_ajax_{$this->getName()}_ids", [$this, 'handleAsyncIdsSelect']);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_ids" => FieldFactory::addAsyncSelect(
                "{$this->getName()}_ids",
                [
                    'conditions'             => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'                  => \__('Custom posts', 'adr'),
                    'multiple'               => true,
                    'values_callback'        => "{$this->getName()}_ids",
                    'values_callback_params' => ["{$this->target->getName()}_cpt"],
                ]
            ),
        ];
    }

    public function handleAsyncIdsSelect(): void
    {
        $nonce = \filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        if (false === \wp_verify_nonce($nonce, 'adr_nonce')) {
            \wp_send_json_error(\__('Invalid nonce data, please try again!', 'adr'));
        }

        $search = \filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);
        $postTypes = \filter_input(
            INPUT_POST,
            "{$this->target->getName()}_cpt",
            FILTER_SANITIZE_STRING
        );

        $args = [
            'post_type'      => \explode(',', $postTypes),
            's'              => $search,
            'posts_per_page' => 10,
        ];

        foreach (\get_posts($args) as $post) {
            $data[] = [
                'label' => $post->post_title,
                'value' => $post->ID,
            ];
        }

        \wp_send_json_success($data ?? []);
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $postsIds = Sanitizer::sanitizePost(
            "{$parentKey}.{$this->getName()}_ids",
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (false === $postsIds) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $postsIds = \implode(',', \array_map(function ($post) {
            return (int) $post['key'];
        }, $postsIds));

        if (empty($postsIds)) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setWhere("{$wpdb->posts}.id", 'IN', "({$postsIds})");
    }
}
