<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Form\Target\User;

use AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor;
use AdvancedDatabaseReplacer\Replacer\Form\AbstractForm;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\User\IdCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Condition\User\UserMetaCondition;
use AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget;
use AdvancedDatabaseReplacer\Replacer\Form\Target\TargetGroupFieldsInterface;
use AdvancedDatabaseReplacer\Replacer\Form\Update\User\UserFieldUpdate;
use AdvancedDatabaseReplacer\Replacer\Form\Update\User\UserMetaFieldUpdate;

class UserTarget extends AbstractTarget implements TargetGroupFieldsInterface
{
    public function __construct(AbstractForm $form)
    {
        parent::__construct('user', \__('Users', 'adr'), false, $form);

        $this->setCondition(new IdCondition($this));
        $this->setCondition(new UserMetaCondition($this));

        $this->setUpdate(new UserFieldUpdate($this));
        $this->setUpdate(new UserMetaFieldUpdate($this));
    }

    public function getTargetGroupFields(): array
    {
        return [];
    }

    public function executeQuery(QueryProcessor $queryProcessor, string $parentKey = ''): void
    {
        global $wpdb;

        $queryProcessor->setTables($wpdb->users);

        $this->executeConditionsQuery($queryProcessor);
        $this->executeUpdateQuery($queryProcessor);
    }
}
