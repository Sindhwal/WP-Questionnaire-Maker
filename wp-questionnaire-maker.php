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