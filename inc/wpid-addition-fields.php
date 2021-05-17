<?php
/**
 * 
 *  This PHP file/script is responsible for creating custom fields or setting page through CMB2
 *  It is a part of a WordPress plugin and does not 
 * 
 */

 class wpid_addition_fields{


    public function __construct(){

        add_action('cmb2_admin_init', array($this, 'init_settings_page'));

    }


    public function init_settings_page(){

        $option_page = new_cmb2_box( array(
            'id'           => 'wpid_options',
            'title'        => esc_html__( 'General Options', 'myprefix' ),
            'object_types' => array( 'options-page' ),
            /*
             * The following parameters are specific to the options-page box
             * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
             */
    
            'option_key'      => 'wpid_general', // The option key and admin menu page slug.
            // 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
            // 'menu_title'      => esc_html__( 'Options', 'myprefix' ), // Falls back to 'title' (above).
             'parent_slug'     => 'edit.php?post_type=wpid_questions', // Make options page a submenu item of the themes menu.
            // 'capability'      => 'manage_options', // Cap required to view options-page.
            // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
            // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
            // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
            // 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.
        ) );


        $group_fields = array( "introductory_question", "transitional_and_verification_question", "technical_question","closing_question" );

        foreach( $group_fields as $field_name ){
            $option_page->add_field(
                array(
                'id'=>'title_for_' . $field_name,
                'type'=>'title',
                'name'=> str_replace( "_"," ", $field_name ) . "S",
                )
            );

            $group_field_id = $option_page->add_field( array(
                'id'          => 'wpid_all_' . $field_name . 's',
                'type'        => 'group',
                // 'repeatable'  => false, // use false if you want non-repeatable group
                'options'     => array(
                    'group_title'       => __( ucwords( str_replace( "_", " ", $field_name ) ) . ' {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'        => __( 'Add Another Question', 'cmb2' ),
                    'remove_button'     => __( 'Remove Question', 'cmb2' ),
                    'sortable'          => true,
                    'closed'         => true, // true to have the groups closed by default
                    'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
                ),
            ) );

            $option_page->add_group_field( $group_field_id, array(
                "id"=>"wpid_" . $field_name,
                "type"=>"textarea_small",
                "name"=>"Question"
            ));
        }

    }


 }
 new wpid_addition_fields();