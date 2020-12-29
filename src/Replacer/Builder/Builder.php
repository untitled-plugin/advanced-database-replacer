<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Builder;

use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetInterface;
use AdvancedDatabaseReplacer\Replacer\Replacer;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class Builder
{
    public const EXECUTE_ACTION = 'adr_execute';

    private static $instance = null;

    public function __construct()
    {
        \add_action('wp_ajax_' . self::EXECUTE_ACTION, [$this, 'handleExecuteRequest']);
    }

    public static function Instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function handleExecuteRequest(): void
    {
        $nonce = Sanitizer::sanitizePost('nonce', FILTER_SANITIZE_STRING);

        if (false === \wp_verify_nonce($nonce, 'adr_nonce')) {
            \wp_send_json_error(\__('Invalid nonce data, please try again!', 'adr'));
        }

        $queryProcessor = new QueryProcessor(
            (bool) Sanitizer::sanitizePost('dry', FILTER_VALIDATE_BOOL),
            (string) Sanitizer::sanitizePost('conditions.relation', FILTER_SANITIZE_STRING, null, ['AND', 'OR'])
        );

        $form = Replacer::Instance()->getForm();
        $targetName = (string) Sanitizer::sanitizePost(
            'target.target_type',
            FILTER_SANITIZE_STRING,
            [],
            \array_keys($form->getTargets())
        );
        $target = $form->getTarget($targetName);

        if (null === $target) {
            \wp_send_json_error(\__('Form target contain empty value!', 'adr'));
        }

        $target->executeQuery($queryProcessor);

        \wp_send_json_success($queryProcessor->execute());
    }
}
