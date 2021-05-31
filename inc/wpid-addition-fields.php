<?php
/**
 *
 *  This PHP file/script is responsible for creating custom fields or setting page through CMB2
 *  It is a part of a WordPress plugin and does not
 *
 */

class wpid_addition_fields
{

    public function __construct()
    {

        add_action('cmb2_admin_init', array($this, 'init_settings_page'));

        add_action('cmb2_admin_init', array($this, 'init_extra_fields_cpt'));

        add_action('admin_footer', array($this, "custom_admin_css"));

    }

    public function init_extra_fields_cpt()
    {

        $cmb = new_cmb2_box(array(
            'id' => 'wpid_submissions_options',
            'title' => __('Extra Fields', 'cmb2'),
            'object_types' => array('wpid_submissions'), // Post type
            'context' => 'side',
            'priority' => 'high',
            'show_names' => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ));

        // Regular text field
        $cmb->add_field(array(
            'name' => __('Interview Dive Type', 'cmb2'),
            'id' => 'wpid_submissions_type',
            'desc' => 'Select the type of interview dive.',
            'type' => 'select',
            'options' => array(
                'public' => 'Public',
                'private' => 'Private',
            ),
        ));

    }

    public function init_settings_page()
    {

        $option_page = new_cmb2_box(array(
            'id' => 'wpid_options',
            'title' => esc_html__('General Options', 'myprefix'),
            'object_types' => array('options-page'),
            /*
             * The following parameters are specific to the options-page box
             * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
             */

            'option_key' => 'wpid_general', // The option key and admin menu page slug.
            'parent_slug' => 'edit.php?post_type=wpid_questions',
        ));

        $option_page->add_field(array(
            'name' => __('Enable Redirect ?', 'cmb2'),
            'id' => 'wpid_redirect_enable',
            'type' => 'checkbox',
        ));

        $option_page->add_field(array(
            'name' => __('Redirect URL:', 'cmb2'),
            'id' => 'wpid_redirect_url',
            'desc' => 'Enter the url where you want to redirect after form submission.<br/>This setting will only works if previous \'Enable Redirect ?\' setting is also set to enable',
            'type' => 'text_url',
        ));

        $option_page->add_field(array(
            'name' => __('Redirect Button Label', 'cmb2'),
            'id' => 'wpid_redirect_btn_label',
            'type' => 'text',
        ));

        $group_fields = array("introductory_question", "transitional_and_verification_question", "technical_question", "closing_question");

        foreach ($group_fields as $field_name) {
            $option_page->add_field(
                array(
                    'id' => 'title_for_' . $field_name,
                    'type' => 'title',
                    'name' => str_replace("_", " ", $field_name) . "S",
                )
            );

            $group_field_id = $option_page->add_field(array(
                'id' => 'wpid_all_' . $field_name . 's',
                'type' => 'group',
                'options' => array(
                    'group_title' => __(ucwords(str_replace("_", " ", $field_name)) . ' {#}', 'cmb2'),
                    'add_button' => __('Add Another Question', 'cmb2'),
                    'remove_button' => __('Remove Question', 'cmb2'),
                    'sortable' => true,
                    'closed' => true,
                    'remove_confirm' => esc_html__('Are you sure you want to remove?', 'cmb2'),
                ),
            ));

            $option_page->add_group_field($group_field_id, array(
                "id" => "wpid_" . $field_name,
                "type" => "textarea_small",
                "name" => "Question",
            ));
        }

    }

    public function custom_admin_css()
    {

        ?>
<style>
#wpid_redirect_enable {
    position: absolute;
    left: -9999px;
}

#wpid_redirect_enable+label {
    display: inline-block;
    position: relative;
    font-size: 1.2rem;
    padding-top: 40px;
    cursor: pointer;
}

#wpid_redirect_enable+label::before,
#wpid_redirect_enable+label::after {
    position: absolute;
    transition: all .5s;
    content: '';
}

#wpid_redirect_enable+label::before {
    top: 0;
    left: 0;
    width: 70px;
    height: 25px;
    background-color: #ebebeb;
    border-radius: 8px;
}

#wpid_redirect_enable:checked+label::before {
    background-color: #e73e41;
}

#wpid_redirect_enable+label::after {
    top: 0px;
    left: -5px;
    width: 25px;
    height: 25px;
    background-color: #fff;
    border-radius: 50%;
    border: 1px solid gray;
    box-shadow: 1px 1px 1px;
}

#wpid_redirect_enable:checked+label::after {
    left: 50px;
}

#wpid_redirect_enable+label span {
    position: absolute;
    bottom: 0;
    left: 0;
}

#wpid_redirect_enable+label span::before {
    content: 'Disabled';
}

#wpid_redirect_enable:checked+label span::before {
    content: 'Enabled';
}
</style>
<?php
}

}
new wpid_addition_fields();