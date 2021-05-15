<?php
/**
 * 
 * This is a library file contains all common functions for the plugin.
 */

 class wpqm_lib{

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

 }