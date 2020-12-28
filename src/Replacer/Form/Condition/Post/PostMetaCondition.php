<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer\Replacer\Form\Condition\Post;

use function  AdvancedDatabaseReplacer\adr_fs ;
use  AdvancedDatabaseReplacer\Replacer\Builder\QueryProcessor ;
use  AdvancedDatabaseReplacer\Replacer\Form\Condition\AbstractCondition ;
use  AdvancedDatabaseReplacer\Replacer\Form\Condition\StringConditionTrait ;
use  AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory ;
use  AdvancedDatabaseReplacer\Replacer\Form\Target\AbstractTarget ;
use  AdvancedDatabaseReplacer\Utils\Sanitizer ;
class PostMetaCondition extends AbstractCondition
{
    use  StringConditionTrait ;
    public function __construct( AbstractTarget $target )
    {
        parent::__construct(
            'post_meta',
            \__( 'Post meta', 'adr' ),
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

}