<?php
namespace WP_Arvan\OBS\Admin;

class Partials {
    protected static function load( $template_name ) {
        $template_path = \ACS_PLUGIN_ROOT . 'admin/partials/partial-' . $template_name . '.php';
        if ( file_exists( $template_path ) ) {
            require_once $template_path;
            return true;
        }

        return false;
    }

    public static function __callStatic( $name, $arguments ) {
        $template_name = str_replace( '_', '-', $name );
        return self::load( $template_name );
    }

    private function __construct() {}

}