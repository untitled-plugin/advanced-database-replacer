<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\User;

use function  AdvancedDatabaseReplacer\adr_fs ;
use  AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor ;
use  AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition ;
use  AdvancedDatabaseReplacer\Replacer\Form\Condition\StringConditionTrait ;
use  AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory ;
use  AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget ;
use  AdvancedDatabaseReplacer\Utils\Sanitizer ;
class UserMetaCondition extends AbstractCondition
{
    use  StringConditionTrait ;
    public function __construct( AbstractTarget $target )
    {
        parent::__construct(
            'user_meta',
            \__( 'User meta', 'adr' ),
            true,
            $target
        );
    }
    
    public function getFields() : array
    {
        return \array_merge( $extraFields ?? [], $this->getStringConditionFields( $this->getName(), $this->target->getName() ) );
    }
    
    public function executeQuery( QueryProcessor $queryProcessor, string $parentKey = '' ) : void
    {
        global  $wpdb ;
    }
    
    private function getUserMetaKeys() : array
    {
        global  $wpdb ;
        return (array) $wpdb->get_col( "\n\t\t\tSELECT meta_key\n\t\t\tFROM {$wpdb->usermeta}\n\t\t\tGROUP BY meta_key\n\t\t\tORDER BY meta_key" );
    }

}