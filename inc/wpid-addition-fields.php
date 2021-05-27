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

        add_action('cmb2_admin_init', array($this, 'init_extra_fields_cpt'));

    }


    public function init_extra_fields_cpt(){

        $cmb = new_cmb2_box( array(
            'id'            => 'wpid_submissions_options',
            'title'         => __( 'Extra Fields', 'cmb2' ),
            'object_types'  => array( 'wpid_submissions', ), // Post type
            'context'       => 'side',
            'priority'      => 'high',
            'show_names'    => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name'       => __( 'Interview Dive Type', 'cmb2' ),
            'id'         => 'wpid_submissions_type',
            'desc'       => 'Select the type of interview dive.',
            'type'       => 'select',
            'options'   => array(
                'public'=>'Public',
                'private'=>'Private',
            )
        ) );

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
            'parent_slug'     => 'edit.php?post_type=wpid_questions', 
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
                'options'     => array(
                    'group_title'       => __( ucwords( str_replace( "_", " ", $field_name ) ) . ' {#}', 'cmb2' ), 
                    'add_button'        => __( 'Add Another Question', 'cmb2' ),
                    'remove_button'     => __( 'Remove Question', 'cmb2' ),
                    'sortable'          => true,
                    'closed'         => true,
                    'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), 
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