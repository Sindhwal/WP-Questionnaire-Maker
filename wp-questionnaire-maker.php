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

 define("WPQM_V", "1.0");
 define("WPQM_FILE", __FILE__ );
 define("WPQM_DIR", plugin_dir_path( WPQM_FILE ) );
 define("WPQM_URL", plugin_dir_url( WPQM_FILE ) );


 /**
  * This is main plugin class
  */
 class wp_questionnaire_maker{

    public function __construct(){
        register_activation_hook( WPQM_FILE , array($this, "wpqm_activation"));
        add_action('plugins_loaded', array($this, 'include_required_files') );
        add_action("admin_menu", array($this, "create_option_menus"));

    }


    public function include_required_files(){

        require WPQM_DIR . "/inc/cmb2/init.php";
        require WPQM_DIR . "/inc/wpqm-generate-cpt.php";
        require WPQM_DIR . "/inc/wpqm-addition-fields.php";
        require WPQM_DIR . "/inc/wpqm-shortcodes.php";

    }

    public function create_option_menus(){

    }

    /**
     * put all the initialization process here.
     */
    public function wpqm_activation(){


    }

 }

 new wp_questionnaire_maker();