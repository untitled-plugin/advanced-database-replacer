<?php

declare ( strict_types = 1 );
namespace AdvancedDatabaseReplacer\Dashboard;

use function  AdvancedDatabaseReplacer\adr_fs ;
use  AdvancedDatabaseReplacer\Replacer\Builder\Builder ;
use  AdvancedDatabaseReplacer\Replacer\Logger\Logger ;
use  AdvancedDatabaseReplacer\Replacer\Replacer ;
use  AdvancedDatabaseReplacer\Utils\Asset ;
class Dashboard
{
    private static  $instance = null ;
    private  $isAdrPage ;
    private function __construct()
    {
        $this->isAdrPage = isset( $_GET['page'] ) && 'advanced-database-replacer' === $_GET['page'];
        \add_action( 'admin_menu', [ $this, 'registerMenus' ] );
        \add_action( 'admin_print_scripts', [ $this, 'enqueueCss' ] );
        $this->isAdrPage && \add_action( 'admin_print_scripts', [ $this, 'enqueueApplicationJs' ] );
        $this->isAdrPage && \add_action( 'admin_print_styles', [ $this, 'enqueueApplicationCss' ] );
    }
    
    public static function Instance() : self
    {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function registerMenus() : void
    {
        \add_menu_page(
            \__( 'Advanced Database Replacer', 'adr' ),
            \__( 'Database Replacer', 'adr' ),
            'manage_options',
            'advanced-database-replacer',
            [ $this, 'initializeMenuPage' ],
            Asset::imageUrl( 'icon.svg' ),
            70
        );
    }
    
    public function initializeMenuPage() : void
    {
        echo  '<noscript>You need to enable JavaScript to run this app.</noscript>' ;
        echo  "<div id='adr-application-wrapper'></div>" ;
    }
    
    public function enqueueCss() : void
    {
        if ( false === \file_exists( Asset::cssDir( 'global.min.css' ) ) ) {
            return;
        }
        \wp_enqueue_style(
            'advanced-database-replacer',
            Asset::cssUrl( 'global.min.css' ),
            [],
            \md5_file( Asset::cssDir( 'global.min.css' ) )
        );
    }
    
    public function enqueueApplicationJs() : void
    {
        foreach ( \glob( Asset::applicationDir( 'static/js/*.js' ) ) as $index => $file ) {
            if ( 'js' !== \pathinfo( $file, PATHINFO_EXTENSION ) ) {
                continue;
            }
            \wp_enqueue_script(
                "advanced-database-replacer-react-{$index}",
                Asset::applicationUrl( 'static/js/' . \basename( $file ) ),
                [],
                \md5_file( $file ),
                true
            );
        }
        if ( false === isset( $index ) ) {
            return;
        }
        $reactData = [
            'translation' => Translation::getTranslations(),
            'form'        => Replacer::Instance()->getForm()->getFieldsArray(),
            'ajax'        => [
            'url'           => \admin_url( 'admin-ajax.php' ),
            'nonce'         => \wp_create_nonce( 'adr_nonce' ),
            'executeAction' => Builder::EXECUTE_ACTION,
            'historyAction' => Logger::LOGGER_ACTION,
        ],
            'checkoutUrl' => 'https://checkout.freemius.com/mode/dialog/plugin/7443/plan/12250/',
        ];
        \wp_localize_script( "advanced-database-replacer-react-{$index}", 'adr', $reactData );
    }
    
    public function enqueueApplicationCss() : void
    {
        foreach ( \glob( Asset::applicationDir( 'static/css/*.css' ) ) as $index => $file ) {
            if ( 'css' !== \pathinfo( $file, PATHINFO_EXTENSION ) ) {
                continue;
            }
            \wp_enqueue_style(
                "advanced-database-replacer-react-{$index}",
                Asset::applicationUrl( 'static/css/' . \basename( $file ) ),
                [],
                \md5_file( $file )
            );
        }
    }

}