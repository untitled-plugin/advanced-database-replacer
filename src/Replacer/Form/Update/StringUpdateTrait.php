<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer\Replacer\Form\Update;

use function  AdvancedDatabaseReplacer\adr_fs ;
use  AdvancedDatabaseReplacer\Replacer\Form\Field\FieldFactory ;
use  AdvancedDatabaseReplacer\Utils\Sanitizer ;
trait StringUpdateTrait
{
    private function getStringReplaceFields( string $name, string $targetName ) : array
    {
        return \array_merge( [
            "{$name}_type"          => FieldFactory::addSelect( "{$name}_type", [
            'conditions' => [ [ "{$name}_field!", null ], [ "{$targetName}_update_type", $name ], [ 'target_type', $targetName ] ],
            'label'      => \__( 'New value set type', 'adr' ),
            'values'     => [
            [
            'label' => \__( 'Replace', 'adr' ),
            'value' => 'replace',
        ],
            [
            'label' => \__( 'Uppercase', 'adr' ),
            'value' => 'uppercase',
        ],
            [
            'label' => \__( 'Lowercase', 'adr' ),
            'value' => 'lowercase',
        ],
            [
            'label' => \__( 'Increase / decrease', 'adr' ),
            'value' => 'increase',
            'isPro' => true,
        ],
            [
            'label' => \__( 'Add at the begin', 'adr' ),
            'value' => 'add_start',
            'isPro' => true,
        ],
            [
            'label' => \__( 'Add at the end', 'adr' ),
            'value' => 'add_end',
            'isPro' => true,
        ]
        ],
        ] ),
            "{$name}_value_replace" => FieldFactory::addInput( "{$name}_value_replace", [
            'conditions' => [ [ "{$name}_type", 'replace' ], [ "{$targetName}_update_type", $name ], [ 'target_type', $targetName ] ],
            'label'      => \__( 'Replace new value', 'adr' ),
        ] ),
        ], $extraFields ?? [] );
    }
    
    private function getReplaceValue( string $name, string $fieldName, string $parentKey = '' ) : ?string
    {
        $replaceName = Sanitizer::sanitizePost( "{$parentKey}.{$name}_type", FILTER_SANITIZE_STRING );
        switch ( $replaceName ) {
            case 'replace':
                $replace = Sanitizer::sanitizePost( "{$parentKey}.{$name}_value_replace", FILTER_SANITIZE_STRING );
                if ( false === $replace ) {
                    return null;
                }
                return "'{$replace}'";
            case 'uppercase':
                return "UPPER({$fieldName})";
            case 'lowercase':
                return "LOWER({$fieldName})";
        }
        return null;
    }

}