<?php
/**
 * 
 * 
 * 
 */


 class wpqm_shortcodes{

    public function __construct(){

        add_shortcode("wpqm_questionnaire", array($this, "generate_main_shortcode"));

    }


    public function generate_main_shortcode( $atts, $content = null ){


        return "wow!";

    }

 }
 new wpqm_shortcodes();