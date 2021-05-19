<?php
/**
 *
 * This php script handel all AJAX request in the plugin
 *
 *
 */

class wpid_ajax
{

    public function __construct()
    {

        add_action("wp_ajax_wpid_request_competencies_qa", array($this, "wpid_request_competencies_qa"));

        add_action("wp_ajax_wpid_update_user_meta", array($this, "wpid_update_user_meta"));

        add_action("wp_ajax_wpid_generate_questionnaire_files", array($this, "wpid_generate_questionnaire_files"));

    }

    public function wpid_generate_questionnaire_files()
    {

        $data = isset($_POST['data']) && !empty($_POST['data']) ? $_POST['data'] : null;

        if ($data == null) {
            die(json_encode(array("response" => 400, "message" => "No data has been received to process")));
        }

        require WPID_DIR . "/inc/fpdf.php";


        $pdf = new FPDF();

        $data = json_decode( stripslashes( $data ) );

        foreach( $data as $title=>$content ){

         if( empty( $title ) ){
            continue;
         }
         
         $pdf->AddPage();

            $pdf->Cell(0,10,'',0,1);
            $pdf->SetFont('Arial','B',18);
            $pdf->Write( 8, $title );

            $ex_title = null;
            $ex_cat = null;
            foreach( $content as $single_row ){


               if( $title == "Core Competencies" ){
                  
                  $terms = get_the_terms( $single_row, "core-competencies" ) ;
                  $qa_title = get_the_title( $single_row );
                  foreach( $terms as $term ){
                     $cat_title = $term->name;
                     break;
                  }
                  
                  if( $ex_cat != $cat_title ){
                     $pdf->Cell(0,20,'',0,1);
                     $pdf->SetFont('Arial','B',16);
                     $pdf->Write( 8, $cat_title );
                     $ex_cat = $cat_title;
                  }
                  $pdf->Cell(0,10,'',0,1);
                  $pdf->SetFont('Arial','B',14);
                  $pdf->Write( 8, $qa_title );
                  $pdf->Cell(0,10,'',0,1);
                  $pdf->Cell(0,40,'',1,1);

               }else{
                  $pdf->Cell(0,20,'',0,1);
                  $pdf->SetFont('Arial','B',14);
                  $pdf->Write( 8, $single_row );
                  $pdf->Cell(0,10,'',0,1);
                  $pdf->Cell(0,40,'',1,1);
               }
            }
        

        }

        $userdata = wp_get_current_user();
        $username = $userdata->user_login;
        $upload_dir = wp_upload_dir(); 
        $upload_url = $upload_dir['url'];
        $file_name = $username . '-' . time() . '.pdf';
        $pdf->Output("F", $upload_dir['path'] . "/" . $file_name );die();

        die( json_encode( array("response"=>200, 'filename'=>$upload_url . '/'. $file_name )) );

        //$pdf->AddPage();
        //$pdf->SetFont('Arial','B',16);



    }

    public function wpid_update_user_meta()
    {

        $user_id = get_current_user_id();

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['wpid_position_title'])) {
            update_user_meta($user_id, 'wpid_position_title', $_POST['wpid_position_title']);
        }
        if (isset($_POST['wpid_position_type'])) {
            update_user_meta($user_id, 'wpid_position_type', $_POST['wpid_position_type']);
        }
        if (isset($_POST['wpid_industry'])) {
            update_user_meta($user_id, 'wpid_industry', $_POST['wpid_industry']);
        }

    }

    public function wpid_request_competencies_qa()
    {

        $nonce = (isset($_POST['wp_nonce']) && !empty($_POST['wp_nonce'])) ? $_POST['wp_nonce'] : null;
        $tax = $_POST['core_selections'];
        $args = array(
            "post_type" => "wpid_questions",
            "posts_per_page" => -1,
            "tax_query" => array(
                array(
                    "taxonomy" => "core-competencies",
                    "field" => "slug",
                    "terms" => $tax,
                ),
            ),
        );

        $competencies_qa = new WP_Query($args);

        $all_qa = "<div>";
        $current_tax = null;
        $all_qa .= "<h2 class='section-title'>Core Competencies</h2>";
        if ($competencies_qa->have_posts()) {
            while ($competencies_qa->have_posts()) {
                $competencies_qa->the_post();
                $ID = get_the_ID();
                $title = get_the_title($ID);
                $current_cat = get_the_terms($ID, "core-competencies");

                if ($current_tax != null && $current_tax != $current_cat[0]->slug) {
                    $all_qa .= "</ul>";
                    $all_qa .= "<ul class='wpid-main-container'><h3>" . $current_cat[0]->name . "</h3>";
                    $x = 1;
                } elseif ($current_tax != $current_cat[0]->slug && $current_tax == null) {
                    $all_qa .= "<ul class='wpid-main-container'><h3>" . $current_cat[0]->name . "</h3>";
                    $x = 1;
                }

                $current_tax = $current_cat[0]->slug;

                $all_qa .= "<li>";

                $all_qa .= wpid_lib::display_checkbox_options("wpid_" . $current_tax, $ID, "wpid_" . $current_tax . "_" . $x, $title);

                $all_qa .= "</li>";

                $x++;
            }
        }

        $all_qa .= "</div>";

        wp_reset_postdata();

        if ($nonce != null && wp_verify_nonce($nonce, "wpid_request_competencies_qa")) {
            die(json_encode(array("code" => 200, "requestEd_data" => $_POST['core_selections'], "response" => $all_qa)));
        } else {
            die(json_encode(array("code" => 400)));
        }

    }

}
new wpid_ajax();
