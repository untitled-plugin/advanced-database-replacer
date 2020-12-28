<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer\Replacer\Form\Condition;

use function  AdvancedDatabaseReplacer\adr_fs ;
use  AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory ;
use  AdvancedDatabaseReplacer\Utils\Sanitizer ;
trait StringConditionTrait
{
    private function getStringConditionFields( string $name, string $targetName ) : array
    {
        return [];
    }
    
    private function getConditionValue( string $name, string $fieldName, string $parentKey = '' ) : ?array
    {
        $replaceName = Sanitizer::sanitizePost( "{$parentKey}.{$name}_type", FILTER_SANITIZE_STRING );
        if ( false === $replaceName ) {
            return null;
        }
        return null;
    }

}