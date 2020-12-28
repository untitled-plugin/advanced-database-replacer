<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Utils;

use const AdvancedDatabaseReplacer\PLUGIN_DIR;
use const AdvancedDatabaseReplacer\PLUGIN_URL;

class Asset
{
    public static function imageUrl(string $fileName): string
    {
        return PLUGIN_URL . "assets/images/{$fileName}";
    }

    public static function applicationUrl(string $fileName): string
    {
        return PLUGIN_URL . "assets/application/build/{$fileName}";
    }

    public static function applicationDir(string $fileName): string
    {
        return PLUGIN_DIR . "assets/application/build/{$fileName}";
    }

    public static function cssUrl(string $fileName): string
    {
        return PLUGIN_URL . "assets/stylesheets/{$fileName}";
    }

    public static function cssDir(string $fileName): string
    {
        return PLUGIN_DIR . "assets/stylesheets/{$fileName}";
    }
}
