<?php
/**
 * 
 * This is a library file contains all common functions for the plugin.
 */

 class wpqm_lib{


    /**
     * gather all questions from general options
     */
    public static function general_options_array( $key = '', $default = false ) {
        if ( function_exists( 'cmb2_get_option' ) ) {
            // Use cmb2_get_option as it passes through some key filters.
            
            return cmb2_get_option( 'wpqm_general', $key, $default );
        }
    
        // Fallback to get_option if CMB2 is not loaded yet.
        $opts = get_option( 'wpqm_general', $default );
    
        $val = $default;
    
        if ( 'all' == $key ) {
            $val = $opts;
        } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
            $val = $opts[ $key ];
        }
    
        return $val;
    }


    /**
     * Gather all core competencies taxonomy and return usefull array
     */
    public static function gather_all_core_competencies(){

        $terms = get_terms( array(
            'taxonomy' => 'core-competencies',
            'orderby'=>'name',
            'order'=>'ASC',
            'hide_empty' => true,
        ) );

        $competencies = array();
        foreach( $terms as $index=>$term ){

            $competencies[] = array( 'name'=>$term->name,'slug'=>$term->slug );

        }

        return $competencies;

    }

 }