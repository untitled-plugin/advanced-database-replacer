<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Target\Post;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\AbstractForm;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Post\IdCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Post\OwnerCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Post\PostMetaCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Post\PostStatusCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Post\TermCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetGroupFieldsInterface;
use AdvancedDatabaseReplacer\Replacer\Form\Update\Post\PostFieldUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\Post\PostMetaFieldUpdate;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class PostTarget extends AbstractTarget implements TargetGroupFieldsInterface
{
    public function __construct(AbstractForm $form)
    {
        parent::__construct('post', \__('Custom post types', 'adr'), false, $form);

        $this->setCondition(new IdCondition($this));
        $this->setCondition(new PostStatusCondition($this));
        $this->setCondition(new OwnerCondition($this));
        $this->setCondition(new PostMetaCondition($this));
        $this->setCondition(new TermCondition($this));

        $this->setUpdate(new PostFieldUpdate($this));
        $this->setUpdate(new PostMetaFieldUpdate($this));
    }

    public function getTargetGroupFields(): array
    {
        return [
            "{$this->getName()}_cpt" => FieldFactory::addSelect(
                "{$this->getName()}_cpt",
                [
                    'label'      => \__('Custom post types', 'adr'),
                    'multiple'   => true,
                    'conditions' => [['target_type', $this->getName()]],
                    'values'     => static function (): array {
                        foreach (\get_post_types([], 'objects') as $postType) {
                            $values[] = ['value' => $postType->name, 'label' => $postType->label];
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

        $postTypes = Sanitizer::sanitizePost(
            "target.{$this->getName()}_cpt",
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY,
            \get_post_types()
        );

        if (false === $postTypes) {
            $queryProcessor->lastError = \sprintf(
                \__('%s target contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables($wpdb->posts);
        $postTypes = \is_array($postTypes) ? \implode('\', \'', $postTypes) : $postTypes;
        $queryProcessor->setMustWhere("{$wpdb->posts}.post_type", 'IN', "('{$postTypes}')");

        $this->executeConditionsQuery($queryProcessor);
        $this->executeUpdateQuery($queryProcessor);
    }
}
