<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer;

use AdvancedDatabaseReplacer\Replacer\Builder\Builder;
use AdvancedDatabaseReplacer\Replacer\Form\AbstractForm;
use AdvancedDatabaseReplacer\Replacer\Form\Form;
use AdvancedDatabaseReplacer\Replacer\Logger\Logger;

class Replacer
{
    private static $instance = null;

    private $form;

    private $builder;

    private $logger;

    private function __construct()
    {
        if (\wp_doing_ajax()) {
            $this->getForm()->getFieldsArray();
            $this->getBuilder();
            $this->getLogger();
        }
    }

    public static function Instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getForm(): AbstractForm
    {
        if (null === $this->form) {
            $this->form = Form::Instance();
        }

        return $this->form;
    }

    public function getBuilder(): Builder
    {
        if (null === $this->builder) {
            $this->builder = Builder::Instance();
        }

        return $this->builder;
    }

    public function getLogger(): Logger
    {
        if (null === $this->logger) {
            $this->logger = Logger::Instance();
        }

        return $this->logger;
    }
}
