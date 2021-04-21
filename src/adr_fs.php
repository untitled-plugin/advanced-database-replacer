<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer;

function adr_fs() : \Freemius
{
    static  $freemius = null ;
    require_once PLUGIN_DIR . '/vendor/freemius/wordpress-sdk/start.php';
    if ( null === $freemius ) {
        try {
            $freemius = \fs_dynamic_init( [
                'id'             => '7443',
                'slug'           => 'advanced-database-replacer',
                'premium_slug'   => 'advanced-database-replacer-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_5eb7c9cbcd753114763f0578d8583',
                'is_premium'     => false,
                'premium_suffix' => 'PRO',
                'menu'           => [
                'slug'    => 'advanced-database-replacer',
                'contact' => true,
                'support' => false,
            ],
                'is_live'        => true,
            ] );
            $freemius->add_action( 'plugin_icon', static function () : string {
                return PLUGIN_DIR . '/assets/images/icon.svg';
            } );
        } catch ( \Freemius_Exception $e ) {
            exit( 'Freemius extension not loaded property!' );
        }
    }
    return $freemius;
}
