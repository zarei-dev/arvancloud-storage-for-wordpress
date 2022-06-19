<?php
namespace WP_Arvan\OBS;

use WP_Encryption\Encryption;
/**
 * The file that defines the plugin helper functions
 *
 * A class definition that includes functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       khorshidlab.com
 * @since      1.0.0
 *
 * @package    Wp_Arvancloud_Storage
 * @subpackage Wp_Arvancloud_Storage/includes
 */
class Helper {
    
    public static function get_storage_settings() {
        $credentials         = false;
        $acs_settings_option = get_option( 'arvan-cloud-storage-settings' );

        if( !empty( $acs_settings_option ) ) {    
            $acs_settings_option = json_decode( (new Encryption)->decrypt( $acs_settings_option ), true );

            if( $acs_settings_option['config-type'] == 'db' ) {
                $credentials = $acs_settings_option;
            } else {
                if( defined( 'ARVANCLOUD_STORAGE_SETTINGS' ) ) {
                    $settings = json_decode( \ARVANCLOUD_STORAGE_SETTINGS, true );
                    $settings['config-type'] = $acs_settings_option['config-type'];
                    
                    $credentials = $settings;
                }
            }
        }

        return $credentials;

    }

    public static function get_bucket_name() {

        $bucket_name = esc_attr( get_option( 'arvan-cloud-storage-bucket-name', false ) );

        return $bucket_name;

    }

    public static function get_storage_url() {

        $credentials  = self::get_storage_settings();
        $bucket_name  = self::get_bucket_name();
        $endpoint_url = $credentials['endpoint-url'] . "/";

        return esc_url( substr_replace( $endpoint_url, $bucket_name . ".", 8, 0 ) );
        
    }

    /**
     * Recursive sanitation for an array
     * 
     * @param $array
     *
     * @return mixed
     */
    public static function acs_recursive_sanitize( $array ) {
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = self::acs_recursive_sanitize( $value );
            } else {
                $value = \sanitize_text_field( $value );
            }
        }

        return $array;
    }
}