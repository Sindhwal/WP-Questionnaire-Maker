<?php
/**
 * Plugin Name: WP Questionnaire Maker
 * Description: WP Questionnaire Maker allows user to create a questionnaire form through multi selection form flow.
 * Version: 1.0
 * Author: Pawan Sindhwal
 * License: GNU 2.0+
 * 
 */

 if( !defined('ABSPATH') ){
     die("Wordpress enviornment not found!");
 }

 define("WPID_V", "1.0");
 define("WPID_FILE", __FILE__ );
 define("WPID_DIR", plugin_dir_path( WPID_FILE ) );
 define("WPID_URL", plugin_dir_url( WPID_FILE ) );


 /**
  * This is main plugin class
  */
 class wp_questionnaire_maker{

    public function __construct(){
        register_activation_hook( WPID_FILE , array($this, "wpid_activation"));
        add_action('plugins_loaded', array($this, 'include_required_files') );
        
        add_action( 'show_user_profile', array($this, 'crf_show_extra_profile_fields') );
        add_action( 'edit_user_profile', array($this, 'crf_show_extra_profile_fields') );

        add_action( 'personal_options_update', array($this, 'crf_update_profile_fields') );
        add_action( 'edit_user_profile_update', array($this, 'crf_update_profile_fields') );

    }


    /**
     * Update user fields for position information
     */
    function crf_update_profile_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        if ( isset( $_POST['wpid_position_title'] ) ) {
            update_user_meta( $user_id, 'wpid_position_title', $_POST['wpid_position_title'] );
        }
        if ( isset( $_POST['wpid_position_type'] ) ) {
            update_user_meta( $user_id, 'wpid_position_type', $_POST['wpid_position_type'] );
        }
        if ( isset( $_POST['wpid_industry'] ) ) {
            update_user_meta( $user_id, 'wpid_industry', $_POST['wpid_industry'] );
        }

    }

    public function crf_show_extra_profile_fields( $user ) {
        
        $wpid_position_title = esc_html( get_the_author_meta( 'wpid_position_title', $user->ID ) );
        $wpid_position_type = esc_html( get_the_author_meta( 'wpid_position_type', $user->ID ) );
        $wpid_industry = esc_html( get_the_author_meta( 'wpid_industry', $user->ID ) );

        ?>
        <h3><?php esc_html_e( 'Position Information', 'wpid' ); ?></h3>
        
        <table class="form-table">
        <tr>
        <th><label for="wpid_position_title"><?php esc_html_e( 'Position Title', 'crf' ); ?></label></th>
        <td><input name="wpid_position_title" id="wpid_position_title" type="text" value="<?php echo $wpid_position_title; ?>" style="width:400px;"></td>
        </tr>

        <tr>
        <th><label for="wpid_position_type"><?php esc_html_e( 'Position Type', 'crf' ); ?></label></th>
        <td><input name="wpid_position_type" id="wpid_position_type" type="text" value="<?php echo $wpid_position_type; ?>" style="width:400px;"></td>
        </tr>

        <tr>
        <th><label for="wpid_industry"><?php esc_html_e( 'Industry', 'crf' ); ?></label></th>
        <td><input name="wpid_industry" id="wpid_industry" type="text" value="<?php echo $wpid_industry; ?>" style="width:400px;"></td>
        </tr>

        </tr>
        </table>
        <?php
    }

    public function include_required_files(){

        require WPID_DIR . "/inc/cmb2/init.php";
        require WPID_DIR . "/inc/wpid-generate-cpt.php";
        require WPID_DIR . "/inc/wpid-addition-fields.php";
        require WPID_DIR . "/inc/wpid-ajax-request.php";
        require WPID_DIR . "/inc/wpid-lib.php";
        require WPID_DIR . "/inc/wpid-shortcodes.php";

    }

    /**
     * put all the initialization process here.
     */
    public function wpid_activation(){


    }

 }

 new wp_questionnaire_maker();