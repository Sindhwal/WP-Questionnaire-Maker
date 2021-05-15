<?php
/**
 * 
 *  This file contains the frontend function including shortcode rendering 
 * 
 */

 class wpqm_shortcodes{

    public function __construct(){

        add_shortcode("wpqm_questionnaire", array($this, "generate_main_shortcode"));

        add_action("wp_enqueue_scripts", array($this, "wp_enqueue_styles") );

    }


    /**
     * Main shortcode for the frontend layout
     */
    public function generate_main_shortcode( $atts, $content = null ){

        ob_start();

        echo "<div id='wpqm-questionnaire-slider' class='carousel slide' data-wrap='false'>";
        echo "<div class='carousel-inner'>";

        echo "<div class='carousel-item active'>";
            echo $this->create_section_panel( "Introductory Questions", "introductory_question", "wpqm-intros" );
        echo "</div>";

        echo "<div class='carousel-item'>";
            echo $this->create_section_panel( "Transitional & Verification Questions", "transitional_and_verification_question", "wpqm-transitional" );
        echo "</div>";

        echo "<div class='carousel-item'>";
            echo $this->create_section_panel( "Technical Questions", "technical_question", "wpqm-technicals" );
        echo "</div>";

        echo "</div>";

        echo "<button class='btn btn-primary wpqm-conitue-btn'>Continue</button>";

        echo "</div>";
        
        return ob_get_clean();

    }


    /**
     * 
     * This function create a section HTML for frontend
     */
    function create_section_panel( $title, $option_name, $class_name ){

        $all_options = wpqm_lib::general_options_array( "wpqm_all_" . $option_name . "s" ) ;

        $no = 1;
        
        if( is_array( $all_options ) && count( $all_options ) > 0 ){

            echo "<div><h2>". $title . "</h2>";

            echo "<ul class='wpqm-main-container ". $option_name."_list'>";
            foreach( $all_options as $quest ){
                
                echo "<li>";
                    echo $this->display_checkbox_options( $class_name , $quest['wpqm_'. $option_name ], $class_name. '-' .$no );
                echo "</li>";

                $no++;
            }
            echo "</ul>";

            echo "</div>";

        }

    }

    /**
     * 
     * Create single checkbox element
     */
    function display_checkbox_options( $class, $text, $id ){

        $id_html = ( isset($id) && !empty($id) ) ? "id='".$id."'" : "";
        $name_html = ( isset($id) && !empty($id) ) ? "name='".$id."'" : "";

        $class_html = ( isset($class) && !empty($class) ) ? "class='wpqm-checkbox ".$class."'" : "";

        return "<input value='". $text ."' type='checkbox' ".$class_html." ".$name_html ." ". $id_html." /><label class='wpqm-label' for='".$id."'>". $text ."</label>";

    }


    function wp_enqueue_styles(){
        
        wp_enqueue_style( "wpqm-bootstrap", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css", null, WPQM_V );
        wp_enqueue_style("wpqm-general-style", WPQM_URL . "/assets/css/style.css", null, WPQM_V );

        wp_enqueue_script( "wpqm-bootstra-script", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js", array('jquery'), WPQM_V, true);
        
        wp_enqueue_script( "wpqm-main-script", WPQM_URL . "/assets/js/script.js", array('jquery'), WPQM_V, true);

    }

 }
 new wpqm_shortcodes();