<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Logger;

use AdvancedDatabaseReplacer\Utils\Sanitizer;
use WordPressHandler\WordPressHandler;

class Logger extends \Monolog\Logger
{
    public const LOGGER_NAME = 'adr_log';

    public const LOGGER_ACTION = 'adr_log_history';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct(self::LOGGER_NAME);

        global $wpdb;

        $this->pushHandler(new WordPressHandler($wpdb, self::LOGGER_NAME, ['user', 'isDry', 'query']));

        \add_action('wp_ajax_' . self::LOGGER_ACTION, [$this, 'handleAjaxLogHistory']);
        \add_action('wp_ajax_nopriv_' . self::LOGGER_ACTION, [$this, 'handleAjaxLogHistory']);
    }

    public static function Instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function handleAjaxLogHistory(): void
    {
        $nonce = Sanitizer::sanitizeGet('nonce', FILTER_SANITIZE_STRING);

        if (false === \wp_verify_nonce($nonce, 'adr_nonce')) {
            \wp_send_json_error(\__('Invalid nonce data, please try again!', 'adr'));
        }

        $page = Sanitizer::sanitizeGet('page', FILTER_VALIDATE_INT) ?: 1;

        global $wpdb;

        $tableName = $wpdb->prefix . self::LOGGER_NAME;
        $query = $wpdb->prepare("SELECT * FROM {$tableName} ORDER BY id DESC LIMIT %d, 10", $page * 10 - 10);
        $queryCount = $wpdb->prepare("SELECT count(*) FROM {$tableName}");

        foreach ($wpdb->get_results($query) as $result) {
            $result = (array) $result;
            $user = $result['user'] ? \get_user_by('ID', $result['user'])->display_name : $result['user'];
            $result['user'] = $user ? "{$user} (ID: {$result['user']})" : $user;
            $result['isDry'] = $result['isDry'] ? \__('Yes', 'adr') : \__('No', 'adr');
            $result['level'] = self::getLevelName((int) $result['level']);

            $rows[] = $result;
        }

        \wp_send_json_success([
            'rows'    => $rows ?? [],
            'total'   => $wpdb->get_var($queryCount),
            'perPage' => 10,
        ]);
    }
}
