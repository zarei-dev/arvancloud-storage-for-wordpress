<?php
use WP_Arvan\OBS\Helper;
use WP_Arvan\OBS\Admin\Partials;
?>

<div class="wrap">
    <?php
    if (isset( $_GET['system-info'] ) && $_GET['system-info'] == true) {
        Partials::system_info();
        return;
    } else if ( isset( $_GET['bulk_upload'] ) && $_GET['bulk_upload'] == true ) {
        ?>

        <div class="ar-heading">
            <H1><?php _e( 'Move files to the bucket', 'arvancloud-object-storage' ) ?></H1>
        </div>
        <hr>
        <?php
        Partials::move_files();
        return;
    } else {

        $config_type     = false;
        $snippet_defined = false;
        $db_defined      = false;
        $bucket_selected = false;
        $acs_settings    = false;
    
        if( $acs_settings_option = Helper::get_storage_settings() ) {
            $config_type         = $acs_settings_option['config-type'];
            $snippet_defined     = defined( 'ARVANCLOUD_STORAGE_SETTINGS' );
            $db_defined          = $config_type == 'db' && ! empty( $acs_settings_option['access-key'] ) && ! empty( $acs_settings_option['secret-key'] ) && ! empty( $acs_settings_option['endpoint-url'] ) ? true : false;
            $bucket_selected     = Helper::get_bucket_name();
            $acs_settings	     = get_option( 'acs_settings' );
    
        }

        if ( isset($_GET['notice']) && sanitize_text_field( $_GET['notice'] ) == 'bucket-created' ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Bucket created successfully', 'arvancloud-object-storage' ) . '</p></div>';
        } else if ( isset($_GET['notice']) && sanitize_text_field($_GET['notice']) == 'bucket-exists') {
            echo '<div class="notice notice-error is-dismissible">
                <p>'. esc_html__( "Bucket with provided information already exists.", 'arvancloud-object-storage' ) .'</p>
            </div>';
        } else if ( isset($_GET['notice']) && sanitize_text_field($_GET['notice']) == 'bucket-create-failed' ) {
            echo '<div class="notice notice-error is-dismissible">
                <p>'. esc_html__( "Something wrong. Try again.", 'arvancloud-object-storage' ) .'</p>
            </div>';
        } else if ( isset($_GET['notice']) && sanitize_text_field($_GET['notice']) == 'bucket-name-too-short' ) {
            echo '<div class="notice notice-error is-dismissible">
                <p>'. esc_html__( "The bucket name should not be less than 3", 'arvancloud-object-storage' ) .'</p>
            </div>';
        }

        $bulk_upload_url = esc_url( add_query_arg(array(
            'page' => ACS_SLUG,
            'bulk_upload' => true,
        ), admin_url()) );

        ?>
            <div class="ar-heading">
                <h1><?php echo __( 'Settings', 'arvancloud-object-storage' ) ?></h1>
                <a href="<?php echo $bulk_upload_url; ?>" type="button" class="button media-button select-mode-toggle-button"><?php _e( 'Move files to the bucket', 'arvancloud-object-storage' ) ?></a>
            </div>
            <hr>
        <?php
    }

    if( ( ! $db_defined && ! $snippet_defined ) || ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'change-access-option' ) ) {

        Partials::set_api_key();

    } elseif( ! $bucket_selected || ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'change-bucket' ) ) {

        // change bucket
        Partials::change_bucket();

    } else if (isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'create-bucket') {

        // create bucket
        Partials::create_bucket();

    } else {
        // Bucket List
        if( isset( $_GET['notice'] ) && $_GET['notice'] == 'selected-bucket-saved' ) {
            echo '<div class="notice notice-success is-dismissible">
                <p>'. esc_html__( "Selected bucket saved.", 'arvancloud-object-storage' ) .'</p>
            </div>';
        }
        Partials::bucket_list();

    }

    require_once( ACS_PLUGIN_ROOT . 'admin/partials/components/footer.php' );
    ?>
</div>