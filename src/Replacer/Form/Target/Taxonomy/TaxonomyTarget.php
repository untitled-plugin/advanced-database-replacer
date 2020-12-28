<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Target\Taxonomy;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\AbstractForm;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Taxonomy\IdCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\Taxonomy\TermMetaCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetGroupFieldsInterface;
use AdvancedDatabaseReplacer\Replacer\Form\Update\Taxonomy\TermFieldUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\Taxonomy\TermMetaFieldUpdate;
use AdvancedDatabaseReplacer\Utils\Sanitizer;

class TaxonomyTarget extends AbstractTarget implements TargetGroupFieldsInterface
{
    public function __construct(AbstractForm $form)
    {
        parent::__construct('taxonomy', \__('Taxonomies', 'adr'), false, $form);

        $this->setCondition(new IdCondition($this));
        $this->setCondition(new TermMetaCondition($this));

        $this->setUpdate(new TermFieldUpdate($this));
        $this->setUpdate(new TermMetaFieldUpdate($this));
    }

    public function getTargetGroupFields(): array
    {
        return [
            "{$this->getName()}_tax" => FieldFactory::addSelect(
                "{$this->getName()}_tax",
                [
                    'label'      => \__('Taxonomies', 'adr'),
                    'multiple'   => true,
                    'conditions' => [['target_type', $this->getName()]],
                    'values'     => static function (): array {
                        foreach (\get_taxonomies([], 'objects') as $taxonomy) {
                            $values[$taxonomy->name] = [
                                'value' => $taxonomy->name,
                                'label' => $taxonomy->label,
                            ];
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

        $taxonomies = Sanitizer::sanitizePost(
            "target.{$this->getName()}_tax",
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY,
            \get_taxonomies()
        );

        if (false === $taxonomies) {
            $queryProcessor->lastError = \sprintf(
                \__('%s target contain not allowed values!', 'adr'),
                $this->getDisplayName()
            );

            return;
        }

        $queryProcessor->setTables($wpdb->terms);

        $queryProcessor->setTables(
            $wpdb->term_taxonomy,
            "{$wpdb->term_taxonomy}.term_id",
            "{$wpdb->terms}.term_id"
        );

        $taxonomies = \is_array($taxonomies) ? \implode('\', \'', $taxonomies) : $taxonomies;
        $queryProcessor->setMustWhere("{$wpdb->term_taxonomy}.taxonomy", 'IN', "('{$taxonomies}')");

        $this->executeConditionsQuery($queryProcessor);
        $this->executeUpdateQuery($queryProcessor);
    }
}
