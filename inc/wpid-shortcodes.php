<?php
/**
 * 
 *  This file contains the frontend function including shortcode rendering 
 * 
 */

 class wpid_shortcodes{

    public function __construct(){

        add_shortcode("wpid-questionnaire", array($this, "generate_main_shortcode"));

        add_action("wp_enqueue_scripts", array($this, "wp_enqueue_styles") );

    }


    /**
     * Main shortcode for the frontend layout
     */
    public function generate_main_shortcode( $atts, $content = null ){

        $user_id = get_current_user_id();
        $post_title = get_the_author_meta("wpid_position_title", $user_id );
        $post_type = get_the_author_meta("wpid_position_type", $user_id );
        $post_industry = get_the_author_meta("wpid_industry", $user_id );
        
        $enable_userinfo = false;

        if( $post_title == null && $post_type == null && $post_industry == null ){
            $enable_userinfo = true;
        }

        ob_start();

        echo "<div id='wpid-questionnaire-container'>";


        if( $post_title == null && $post_type == null && $post_industry == null ){
            echo "<div class='form-card first-card active wpid-userinfo-section'>";
                echo $this->create_position_info();
            echo "</div>";
            
            echo "<div class='form-card'>";
                echo $this->create_section_panel( "Introductory Questions", "introductory_question", "wpid-intros" );
            echo "</div>";
        }else{

        echo "<div class='form-card first-card active'>";
            echo $this->create_section_panel( "Introductory Questions", "introductory_question", "wpid-intros" );
        echo "</div>";

        }

        echo "<div class='form-card'>";
            echo $this->create_section_panel( "Transitional & Verification Questions", "transitional_and_verification_question", "wpid-transitional" );
        echo "</div>";

        echo "<div class='form-card'>";
            echo $this->create_section_panel( "Technical Questions", "technical_question", "wpid-technicals" );
        echo "</div>";

        echo "<div class='form-card'>";
            echo $this->create_core_competencies( "Position Core Competencies" );
        echo "</div>";

        echo "<div class='form-card request-competencies'>";
            /** KEEP THIS EMPTY | THIS DIV IS POPULATED BY DYNAMIC CONTENT ON FRONTEND **/
        echo "</div>";

        echo "<div class='form-card last-card'>";
            echo $this->create_section_panel( "Closing Questions", "closing_question", "wpid-closing" );
        echo "</div>";

        echo "<div class='form-card last-card'>";
            echo $this->create_final_section();
        echo "</div>";

        echo "<div id='wpid-questionnaire-controller'>";
            echo "<button class='btn btn-primary wpid-back-btn' disabled='disabled'>Back</button>";
            echo "<button class='btn btn-primary wpid-conitue-btn'>Continue</button>";
            echo "<button style='display:none;' class='btn btn-primary submit-selected-qa'>Submit</button>";
        echo "</div>";  // end of button container
      
        echo "</div>";
        
        return ob_get_clean();

    }


    /**
     * This function creates the final confirmation section for the form
     */
    function create_final_section(){

    }

    /**
     * This will create a position info for the maker
     */
    function create_position_info(){

        ob_start();
        ?>
        <div>
        <h2 class="section-title">Position Info</h2>
        <p>This information is only collected once and will be able to be managed in your profile after you complete your first assessment</p>
            <div class="wpid-info-section">
            <h3 class="section-ask">What Position Title are you hiring for?</h3>
            <input type="text" id="wpid-position-title" class="form-control wpid-userinfo">
            <h3 class="section-ask">Position Type</h3>
            <input type="text" id="wpid-position-type" class="form-control wpid-userinfo">
            <h3 class="section-ask">Industry</h3>
            <input type="text" id="wpid-industry-name" class="form-control wpid-userinfo">
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * create core competencies panel
     */
    function create_core_competencies( $title ){

        $competencies = wpid_lib::gather_all_core_competencies();
        echo "<div><h2 class='section-title'>". $title . "</h2>";

        echo "<ul class='wpid-main-container core_competencies_list'>";

        foreach( $competencies as $comp ){

            echo "<li>";

            echo wpid_lib::display_checkbox_options($comp['slug'],$comp['name'],$comp['slug'] );

            echo "</li>";

        }
        
        echo "</ul>";


        echo "</div>";

    }


    /**
     * 
     * This function create a section HTML for frontend
     */
    function create_section_panel( $title, $option_name, $class_name ){

        $all_options = wpid_lib::general_options_array( "wpid_all_" . $option_name . "s" ) ;

        $no = 1;
        
        
        echo "<div><h2 class='section-title'>". $title . "</h2>";
        
        echo "<ul class='wpid-main-container ". $option_name."_list'>";
        
 
            if( is_array( $all_options ) && count( $all_options ) > 0 ){
                foreach( $all_options as $quest ){
                    
                    echo "<li>";
                        echo wpid_lib::display_checkbox_options( $class_name , $quest['wpid_'. $option_name ], $class_name. '-' .$no );
                    echo "</li>";

                    $no++;
                }
            }else{
                echo "Does not found any entry in this section.";
            }
            echo "</ul>";

            echo "</div>";

    }


    function wp_enqueue_styles(){
        
        wp_enqueue_style( "wpid-bootstrap", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css", null, WPID_V );
        wp_enqueue_style("wpid-general-style", WPID_URL . "assets/css/style.css", null, WPID_V );

        wp_enqueue_script( "wpid-bootstra-script", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js", array('jquery'), WPID_V, true);
               
        wp_enqueue_script( "wpid-main-script", WPID_URL . "assets/js/script.js", array('jquery'), WPID_V, true);

        wp_localize_script( "wpid-main-script" , "wpid_data", array(
            
            "ajaxurl"=> admin_url( "admin-ajax.php" ),
            "wpid_nonce"=> wp_create_nonce( "wpid_request_competencies_qa" )

        ) );

    }

 }
 new wpid_shortcodes();