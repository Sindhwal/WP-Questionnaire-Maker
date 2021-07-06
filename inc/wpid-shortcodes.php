<?php
/**
 *
 *  This file contains the frontend function including shortcode rendering
 *
 */

class wpid_shortcodes
{

    public function __construct()
    {

        add_shortcode("wpid-questionnaire", array($this, "generate_main_shortcode"));
        add_shortcode("wpid-interview-dive", array($this, "display_interview_dive"));

        add_action("wp_enqueue_scripts", array($this, "wp_enqueue_styles"));

    }

    /**
     * Main shortcode for the frontend layout
     */
    public function generate_main_shortcode($atts, $content = null)
    {

        $user_id = get_current_user_id();

        if ($user_id == 0) {
            return "<strong><a href='" . wp_login_url(site_url($_SERVER['REQUEST_URI'])) . "'>Login</a> to view this form.</strong>";
        }

        $post_title = get_the_author_meta("wpid_position_title", $user_id);
        $post_type = get_the_author_meta("wpid_position_type", $user_id);
        $post_industry = get_the_author_meta("wpid_industry", $user_id);

        $enable_userinfo = false;

        if ($post_title == null && $post_type == null && $post_industry == null) {
            $enable_userinfo = true;
        }

        ob_start();

        $enable_redirect = wpid_lib::general_options_array("wpid_redirect_enable");
        $redirect_url = wpid_lib::general_options_array("wpid_redirect_url");
        $redirect_btn_label = wpid_lib::general_options_array("wpid_redirect_btn_label");

        $redirect_btn_label = $redirect_btn_label == "" ? "Continue" : $redirect_btn_label;

        echo "<div id='wpid-questionnaire-container' data-redirect-enable='" . $enable_redirect . "' data-redirect-url='" . $redirect_url . "' data-redirect-label='" . $redirect_btn_label . "'>";

        if ($post_title == null && $post_type == null && $post_industry == null) {
            echo "<div class='form-card first-card active wpid-userinfo-section wpid-ajax-ignore'>";
            echo $this->create_position_info();
            echo "</div>";

            echo "<div class='form-card'>";
            echo $this->create_section_panel("Introductory Questions", "introductory_question", "wpid-intros");
            echo "</div>";
        } else {

            echo "<div class='form-card first-card active wpid-userinfo-section wpid-ajax-ignore'>";
            echo $this->create_position_info($post_title,$post_type,$post_industry);
            echo "</div>";

            echo "<div class='form-card'>";
            echo $this->create_section_panel("Introductory Questions", "introductory_question", "wpid-intros");
            echo "</div>";

/*             echo "<div class='form-card first-card active'>";
            echo $this->create_section_panel("Introductory Questions", "introductory_question", "wpid-intros");
            echo "</div>";  */

        }

        echo "<div class='form-card'>";
        echo $this->create_section_panel("Transitional & Verification Questions", "transitional_and_verification_question", "wpid-transitional");
        echo "</div>";

        echo "<div class='form-card'>";
        echo $this->create_section_panel("Technical Questions", "technical_question", "wpid-technicals");
        echo "</div>";

        echo "<div class='form-card wpid-ajax-ignore'>";
        echo $this->create_core_competencies("Position Core Competencies");
        echo "</div>";

        echo "<div class='form-card request-competencies'>";
        /** KEEP THIS EMPTY | THIS DIV IS POPULATED BY DYNAMIC CONTENT ON FRONTEND USING JAVASCRIPT **/
        echo "</div>";

        echo "<div class='form-card'>";
        echo $this->create_section_panel("Closing Questions", "closing_question", "wpid-closing");
        echo "</div>";

        echo "<div class='form-card wpid-ajax-ignore last-card'>";
        echo $this->create_final_section();
        echo "</div>";

        echo "<div class='form-card wpid-ajax-ignore wpid-display-form'>";
        echo "<div><h2 class='section-title'>Congratulations! You've completed Your Questionnaire</h2>";
        echo "<h4 class='section-subtitle'>How Would You Like To View Your Dive</h4>";
        echo "<div id='wpid-controller-container'></div>";
        echo "</div>";
        echo "</div>";

        echo "<div id='wpid-questionnaire-controller'>";
        echo "<button class='btn btn-primary wpid-back-btn' disabled='disabled'>BACK</button>";
        echo "<button class='btn btn-primary wpid-conitue-btn'>CONTINUE</button>";

        $userdata = wp_get_current_user();
        $username = $userdata->user_login;
        $upload_dir = wp_upload_dir();
        $upload_url = $upload_dir['url'];
        $file_name = $username . '-' . time() . '.pdf';
        $fileURL = $upload_url . '/' . $file_name;

        echo "<button style='display:none;' class='btn btn-primary submit-selected-qa' data-filename='" . $file_name . "' data-fileurl='" . $fileURL . "'>Save & Finish</button>";

        echo "</div>"; // end of button container

        echo "</div>";

        return ob_get_clean();

    }

    public function display_interview_dive($atts, $content = null)
    {

        if (is_admin()) {
            //must not render anything on admin-side
            return false;
        }

        $atts = shortcode_atts(array(
            'type' => 'personal',
            'per_page' => '10',
        ), $atts);

        $content = "";
        $type = (isset($atts['type']) && !empty($atts['type'])) ? $atts['type'] : 'personal';
        $max = (isset($atts['per_page']) && !empty($atts['per_page'])) ? $atts['per_page'] : '10';
        $paged = (isset($_GET['paged']) && !empty($_GET['paged'])) ? $_GET['paged'] : 1;

        $paged = get_query_var('paged', $paged);

        $args = array();

        if ($type == "public") {

            $args1 = array(
                "post_type" => "wpid_submissions",
                "post_status" => "publish",
                "meta_query" => array(
                    array(
                        'key' => 'wpid_submissions_type',
                        'value' => 'public',
                        'compare' => '=',
                    ),
                ),
            );

            $args2 = array(
                "post_type" => "wpid_submissions",
                "post_status" => "publish",
                'author' => get_current_user_id(),
            );

            $query_1 = new WP_Query($args1);
            $query_2 = new WP_Query($args2);

            $all_posts = array_merge($query_1->posts, $query_2->posts);
            $args3 = array_unique(wp_list_pluck($all_posts, 'ID'));
            $args3 = array_values($args3);
            $arg = array(
                'post_type' => 'wpid_submissions',
                'post__in' => $args3,
                'posts_per_page' => $max,
                'paged' => $paged,
                'order' => 'DESC',
                'orderby' => 'date',
            );
            $posts = new WP_Query($arg);

        } else {

            $args = array(
                "post_type" => "wpid_submissions",
                "post_status" => "publish",
                "post_per_page" => $max,
                'paged' => $paged,
                'author' => get_current_user_id(),
            );

            $posts = new WP_Query($args);

        }

        ob_start();

        $content .= "<div class='wpid-submissions-list wpid-submissions-" . $type . "'>";

        if ($posts->have_posts()) {

            while ($posts->have_posts()) {
                $posts->the_post();
                $title = get_the_title();
                $id = get_the_ID();
                $content .= "<div class='single-post-title'>";
                $content .= "<a href='" . get_permalink($id) . "'><h3>" . get_the_title($id) . "</h3></a>";
                $content .= "</div>";
            }

        }

        $content .= '<div class="pagination__wrapper">';

        $total_pages = $posts->max_num_pages;
        if ($total_pages > 1) {
            $current_page = max(1, $paged);
            $content .= str_replace('page-numbers', 'pagination', paginate_links(array(
                'base' => str_replace('%_%', 1 == $paged ? '' : "?paged=%#%", "?paged=%#%"),
                'format' => '?page=%#%',
                'current' => $current_page,
                'total' => $total_pages,
                'prev_text' => __('&#10094;'),
                'next_text' => __('&#10095;'),
                'type' => 'list',
            )));
        }

        $content .= "</div>";

        $content .= "</div>"; // end of container;

        wp_reset_postdata();
        echo $content;
        ob_end_flush();

    }

    /**
     * This function creates the final confirmation section for the form
     */
    public function create_final_section()
    {
        ?>
<div>
    <h2 class='section-title'>Would you be willing to share your interview Dive in our Community Library?</h2>
    <ul class='wpid-main-container'>
        <li>
            <input data-slug="wpid-public-drive" value="public" type="radio"
                class="wpid-checkbox wpid-public-drive wpid-drive-ask" name="wpid-drive-ask" id="wpid-public-ask"
                selected />
            <label class="wpid-label" for="wpid-public-ask">Yes! I'd be happy to share it in the Interview Drive Public
                Library</label>
        </li>

        <li>
            <input data-slug="wpid-private-drive" value="private" type="radio"
                class="wpid-checkbox wpid-private-drive wpid-drive-ask" name="wpid-drive-ask" id="wpid-private-ask" />
            <label class="wpid-label" for="wpid-private-ask">No. This should just be available in my personal Drive
                Library</label>
        </li>
    </ul>
</div>
<?php
}

    /**
     * This will create a position info for the maker
     */
    public function create_position_info( $post_title = null ,$post_type = null ,$post_industry = null )
    {

        ob_start();
        ?>
<div>
    <h2 class="section-title">Position Info</h2>
    <p>This information is only collected once and will be able to be managed in your profile after you complete your
        first assessment</p>
    <div class="wpid-info-section">
        <h3 class="section-ask">What Position Title are you hiring for?</h3>
        <select id="wpid-position-title" class="form-control wpid-userinfo">
        <?php
        $job_title_ar = array(
            "Board",
            "Consultant",
            "Customer Service",
            "Director",
            "Entry Level Associate",
            "Executive",
            "First Line Manager",
            "Freelancer",
            "High Level Individual Contributor",
            "Laborer",
            "Lead",
            "Manager",
            "Mid Level Individual Contributor",
            "Sales",
            "Volunteer"
        );
        foreach($job_title_ar as $title){
            $title_selection = $post_title == $title ? "selected" : "";
             echo "<option value='". $title ."' ". $title_selection .">". $title ."</option>";
        }
        ?>
        </select>
        <h3 class="section-ask">Position Type</h3>
        <input type="text" id="wpid-position-type" class="form-control wpid-userinfo" value=<?php echo $post_type; ?> >
        <h3 class="section-ask">Industry</h3>
        <select id="wpid-industry-name" class="form-control wpid-userinfo">
        <?php

        $industry_ar = array(
            "Architecture/Engineering",
            "Arts, Media, Recreation",
            "Construction",
            "Consulting",
            "Education",
            "Facility Services/Pest Control/Lawn Service",
            "Financial Services & Banking",
            "Government",
            "Hospitality",
            "Information Technology",
            "Insurance",
            "Legal Services",
            "Manufacturing",
            "Marketing/Advertising",
            "Medical or Dental Services",
            "Nonprofit",
            "Professional & Business Services",
            "Religious Organizations",
            "Retail",
            "Telecommunications",
            "Transportation & Logistics",
            "Utilities/Energy",
            "Warehousing & Distribution",
            "Web, Design, or Writing Services",
            "Other"
        );
        foreach($industry_ar as $title){
            $title_selection = $post_industry == $title ? "selected" : "";
             echo "<option value='". $title ."' ". $title_selection .">". $title ."</option>";
        }
        ?>
        </select>
    </div>
</div>
<?php

        return ob_get_clean();
    }

    /**
     * create core competencies panel
     */
    public function create_core_competencies($title)
    {

        $competencies = wpid_lib::gather_all_core_competencies();
        echo "<div><h2 class='section-title'>" . $title . "</h2>";

        echo "<ul class='wpid-main-container core_competencies_list'>";

        foreach ($competencies as $comp) {

            echo "<li>";

            echo wpid_lib::display_checkbox_options($comp['slug'], $comp['name'], $comp['slug']);

            echo "</li>";

        }

        echo "</ul>";

        echo "</div>";

    }

    /**
     *
     * This function create a section HTML for frontend
     */
    public function create_section_panel($title, $option_name, $class_name)
    {

        $all_options = wpid_lib::general_options_array("wpid_all_" . $option_name . "s");

        $no = 1;

        echo "<div><h2 class='section-title'>" . $title . "</h2>";

        echo "<ul class='wpid-main-container " . $option_name . "_list'>";

        if (is_array($all_options) && count($all_options) > 0) {
            foreach ($all_options as $quest) {

                echo "<li>";
                echo wpid_lib::display_checkbox_options($class_name, $quest['wpid_' . $option_name], $class_name . '-' . $no);
                echo "</li>";

                $no++;
            }
        } else {
            echo "Does not found any entry in this section.";
        }
        echo "</ul>";

        echo "</div>";

    }

    public function wp_enqueue_styles()
    {

        wp_enqueue_style("wpid-bootstrap", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css", null, WPID_V);
        wp_enqueue_style("wpid-general-style", WPID_URL . "assets/css/style.css", null, WPID_V);

        wp_enqueue_script("wpid-bootstra-script", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js", array('jquery'), WPID_V, true);

        wp_enqueue_script("wpid-main-script", WPID_URL . "assets/js/script.js", array('jquery'), WPID_V, true);

        wp_localize_script("wpid-main-script", "wpid_data", array(

            "ajaxurl" => admin_url("admin-ajax.php"),
            "wpid_nonce" => wp_create_nonce("wpid_request_competencies_qa"),

        ));

    }

}
new wpid_shortcodes();