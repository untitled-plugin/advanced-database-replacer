<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\User;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class IdCondition extends AbstractCondition
{
    public function __construct(AbstractTarget $target)
    {
        parent::__construct('users_ids', \__('Custom users', 'adr'), false, $target);

        \add_action("wp_ajax_{$this->getName()}_ids", [$this, 'handleAsyncIdsSelect']);
    }

    public function getFields(): array
    {
        return [
            "{$this->getName()}_ids" => FieldFactory::addAsyncSelect(
                "{$this->getName()}_ids",
                [
                    'conditions'             => [["{$this->target->getName()}_condition_type", $this->getName()]],
                    'label'                  => \__('Custom users', 'adr'),
                    'multiple'               => true,
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

        $usersIds = Sanitizer::sanitizePost(
            "{$parentKey}.{$this->getName()}_ids",
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (false === $usersIds) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $usersIds = \implode(',', \array_map(function ($user) {
            return (int) $user['key'];
        }, $usersIds));

        if (empty($usersIds)) {
            $queryProcessor->lastError = \sprintf(
                \__('%s condition contain empty value!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setWhere("{$wpdb->users}.ID", 'IN', "({$usersIds})");
    }
}
