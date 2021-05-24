<?php
/**
 * 
 * 
 * 
 */

 class wpid_generate_cpt{

    public $custom_post_types = array( "wpid_questions" );

    public function __construct(){

        add_action("init", array( $this, "register_questions_cpt"));
        add_action("init", array( $this, "generate_cpt_taxonomy"),100);
        add_action("init", array( $this, "register_form_submission_cpt"));

    }

    public function register_questions_cpt(){

        foreach ($this->custom_post_types as $current_post) {

            $label = ucwords( str_replace("wpid_","", $current_post ) );
            
            $singular_label = substr( $label, 0 , -1);

            $labels = array(
                'name' => _x($label , 'Post Type General Name', '_sps_'),
                'singular_name' => _x( $singular_label, 'Post Type Singular Name', '_sps_'),
                'menu_name' => __($label, '_sps_'),
                'name_admin_bar' => __($label, '_sps_'),
                'archives' => __($label . ' Archives', '_sps_'),
                'attributes' => __($label . ' Attributes', '_sps_'),
                'parent_item_colon' => __('Parent Item:', '_sps_'),
                'all_items' => __('All ' . $label , '_sps_'),
                'add_new_item' => __('Add New ' . $singular_label, '_sps_'),
                'add_new' => __('Add New ' . $singular_label, '_sps_'),
                'new_item' => __('New ' . $singular_label, '_sps_'),
                'edit_item' => __('Edit '.$singular_label, '_sps_'),
                'update_item' => __('Update '.$singular_label, '_sps_'),
                'view_item' => __('View '.$singular_label, '_sps_'),
                'view_items' => __('View '.$singular_label, '_sps_'),
                'search_items' => __('Search '.$singular_label, '_sps_'),
                'not_found' => __($singular_label . 'Not found', '_sps_'),
                'not_found_in_trash' => __('Not found in Trash', '_sps_'),
                'featured_image' => __('Featured Image', '_sps_'),
                'set_featured_image' => __('Set featured image', '_sps_'),
                'remove_featured_image' => __('Remove featured image', '_sps_'),
                'use_featured_image' => __('Use as featured image', '_sps_'),
                'insert_into_item' => __('Insert into item', '_sps_'),
                'uploaded_to_this_item' => __('Uploaded to this item', '_sps_'),
                'items_list' => __('Items list', '_sps_'),
                'items_list_navigation' => __('Items list navigation', '_sps_'),
                'filter_items_list' => __('Filter items list', '_sps_'),
            );

            $support = array('title');

            $args = array(
                'label' => __($label, '_sps_'),
                'description' => __('Post Type Description', '_sps_'),
                'labels' => $labels,
                'supports' => $support,
                'taxonomies' => array("core-competencies"),
                'hierarchical' => true,
                'public' => true, // it's not public, it shouldn't have it's own permalink, and so on
                'show_ui' => true,
                'menu_position' => 5,
                'show_in_admin_bar' => false,
                'show_in_nav_menus' => false,
                'show_in_menu' => true,
                'can_export' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => $current_post, 'with_front' => false),
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                // 'menu_icon' => 'dashicons-chart-area',
                'capability_type' => 'post'
            );
            register_post_type($current_post, $args);
        }

    }

      /**
     * Create a taxonomy 'tag' for 'debate'
     */
    public function generate_cpt_taxonomy()
    {

        $taxonomy = "core competencies";
        $label = ucwords( $taxonomy );
        $taxonomy_s = ucwords( "core competency" );


        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = array(
            'name' => _x( $label , 'taxonomy general name'),
            'singular_name' => _x( $label , 'taxonomy singular name'),
            'search_items' => __('Search ' . $label),
            'popular_items' => __('Popular '.$label),
            'all_items' => __('All '.$label),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit '.$label),
            'update_item' => __('Update '.$label),
            'add_new_item' => __('Add New '.$label),
            'new_item_name' => __('New '.$label.' Name'),
            'add_or_remove_items' => __('Add or remove '.$label),
            'choose_from_most_used' => __('Choose from the most used '.$label),
            'menu_name' => __($label),
        );

        register_taxonomy( str_replace(" ","-", $taxonomy), 'wpid_questions', array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
        ));

    }


    public function register_form_submission_cpt(){

        $label = "Form Submissions";
        $current_post = "wpid_submissions";
        $singular_label = "Form Submission";

            $labels = array(
                'name' => _x($label , 'Post Type General Name', '_sps_'),
                'singular_name' => _x( $singular_label, 'Post Type Singular Name', '_sps_'),
                'menu_name' => __($label, '_sps_'),
                'name_admin_bar' => __($label, '_sps_'),
                'archives' => __($label . ' Archives', '_sps_'),
                'attributes' => __($label . ' Attributes', '_sps_'),
                'parent_item_colon' => __('Parent Item:', '_sps_'),
                'all_items' => __('All ' . $label , '_sps_'),
                'add_new_item' => __('Add New ' . $singular_label, '_sps_'),
                'add_new' => __('Add New ' . $singular_label, '_sps_'),
                'new_item' => __('New ' . $singular_label, '_sps_'),
                'edit_item' => __('Edit '.$singular_label, '_sps_'),
                'update_item' => __('Update '.$singular_label, '_sps_'),
                'view_item' => __('View '.$singular_label, '_sps_'),
                'view_items' => __('View '.$singular_label, '_sps_'),
                'search_items' => __('Search '.$singular_label, '_sps_'),
                'not_found' => __($singular_label . 'Not found', '_sps_'),
                'not_found_in_trash' => __('Not found in Trash', '_sps_'),
                'featured_image' => __('Featured Image', '_sps_'),
                'set_featured_image' => __('Set featured image', '_sps_'),
                'remove_featured_image' => __('Remove featured image', '_sps_'),
                'use_featured_image' => __('Use as featured image', '_sps_'),
                'insert_into_item' => __('Insert into item', '_sps_'),
                'uploaded_to_this_item' => __('Uploaded to this item', '_sps_'),
                'items_list' => __('Items list', '_sps_'),
                'items_list_navigation' => __('Items list navigation', '_sps_'),
                'filter_items_list' => __('Filter items list', '_sps_'),
            );

            $support = array('title','editor');

            $args = array(
                'label' => __($label, '_sps_'),
                'description' => __('Post Type Description', '_sps_'),
                'labels' => $labels,
                'supports' => $support,
                //'taxonomies' => array("core-competencies"),
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'menu_position' => 5,
                'show_in_admin_bar' => false,
                'show_in_nav_menus' => false,
                'show_in_menu' => true,
                'can_export' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => $current_post, 'with_front' => false),
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                // 'menu_icon' => 'dashicons-chart-area',
                'capability_type' => 'post'
            );
            register_post_type($current_post, $args);
    }

 }
 new wpid_generate_cpt();