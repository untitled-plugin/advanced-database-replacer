<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class OwnerCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('owner_user', \__('Post owner', 'adr'), false, $target);

        \add_action("wp_ajax_{$this->getName()}_ids", [$this, 'handleAsyncIdsSelect']);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_ids" => FieldFactory::addAsyncSelect(
                "{$this->getName()}_ids",
                [
                    'conditions'             => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'                  => \__('Post owner', 'adr'),
                    'multiple'               => false,
                    'values_callback'        => "{$this->getName()}_ids",
                    'values_callback_params' => [],
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

        $args = [
            'search'         => $search,
            'search_columns' => ['ID', 'user_login', 'user_email', 'user_url', 'user_nicename', 'display_name'],
            'number'         => 10,
        ];

        foreach (\get_users($args) as $user) {
            /** @var \WP_User $user */
            $data[] = [
                'label' => $user->display_name,
                'value' => $user->ID,
            ];
        }

        \wp_send_json_success($data ?? []);
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $userId = Sanitizer::sanitizePost("{$parentKey}.{$this->getName()}_ids.key", FILTER_VALIDATE_INT);

        if (false === $userId) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables($wpdb->users, "{$wpdb->posts}.post_author", "{$wpdb->users}.ID");
        $queryProcessor->setWhere("{$wpdb->users}.ID", '=', (int) $userId);
    }
}
