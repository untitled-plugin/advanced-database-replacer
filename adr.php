<?php

/**
 * Plugin Name: Advanced Database Replacer
 * Description: The most advanced WordPress database replacer plugin. A user-friendly and powerful tool to fast modify WordPress posts, taxonomies, and meta.
 * Version: 1.1.0
 * Author: Untitled Plugin
 * Author URI: https://untitledplugin.com
 * WC tested up to: 5.7.1
 * WC requires at least: 5.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

namespace AdvancedDatabaseReplacer;

\defined('ABSPATH') || exit('File cannot be opened directly!');

if (\function_exists('AdvancedDatabaseReplacer\adr_fs')) {
    adr_fs()->set_basename(true, __FILE__);

    return;
}

class Plugin
{
    public static function initialize(): void
    {
        include_once 'vendor/autoload.php';

        \define('AdvancedDatabaseReplacer\PLUGIN_DIR', \plugin_dir_path(__FILE__));
        \define('AdvancedDatabaseReplacer\PLUGIN_URL', \plugin_dir_url(__FILE__));

        adr_fs();

        \add_action('init', [Dashboard\Dashboard::class, 'Instance']);
        \add_action('admin_init', [Replacer\Replacer::class, 'Instance']);
    }
}

Plugin::initialize();
