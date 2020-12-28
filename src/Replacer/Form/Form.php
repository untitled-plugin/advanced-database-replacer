<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form;

use AdvancedDatabaseReplacer\Replacer\Form\Target\Post\PostTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Target\Taxonomy\TaxonomyTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Target\User\UserTarget;

class Form extends AbstractForm
{
    private static $instance = null;

    public function __construct()
    {
        $this->setTarget(new PostTarget($this));
        $this->setTarget(new TaxonomyTarget($this));
        $this->setTarget(new UserTarget($this));
    }

    public static function Instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
