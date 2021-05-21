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

        add_action("wp_ajax_wpid_send_email", array($this, "wpid_send_email"));

        add_action("wp_ajax_wpid_display_created_form", array($this, "wpid_display_created_form"));

        add_action("wp_ajax_wpid_generate_questionnaire_files", array($this, "wpid_generate_questionnaire_files"));

    }

        /**
     * 
     * Display final form on frontend
     */
    function wpid_display_created_form(){

        $args = array(
            'post_type' => 'wpid_submissions',
            'posts_per_page' => 1,
            'author'=>get_current_user_id(),
            'post_status'=>'publish',
            'order'=>'DESC',
            'orderby'=>'date'
        );

        $r_post = new WP_Query( $args );

        $content = null;
        $this_id = null;
        while( $r_post->have_posts() ){
            $r_post->the_post();
            $content = get_the_content();
            $this_id = get_the_ID();
        }

        $output = "<div class='wpid-content' data-post-id='".$this_id."'>";
        $output .= $content;
        $output .= "</div>";

        wp_reset_postdata();
        
        echo $output;
        die();

    }

    public function wpid_send_email(){

        $postID = ( isset($_POST['post_id']) && !empty($_POST['post_id']) ) ? $_POST['post_id'] : null ;

        if( $postID == null ){
            die( json_encode( array( "response"=>500,"message"=>"Unable to read the created form." ) ) );
        }

        $userdata = wp_get_current_user();

        $to = $userdata->user_email ;

        $subject = 'Questionnaire Drive';

        $headers = "From: ". get_option('admin_email', false) ;
        $headers .= "Reply-To: ". get_option('admin_email', false) ;
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $query = get_post( $postID );
        $message = apply_filters('the_content', $query->post_content);

        $response = mail($to, $subject, $message, $headers);

        die( json_encode( array( "response"=>200, "message"=>$response )));


    }

    public function wpid_generate_questionnaire_files()
    {

        $data = isset($_POST['data']) && !empty($_POST['data']) ? $_POST['data'] : null;
        $file_name = isset($_POST['filename']) && !empty($_POST['filename']) ? $_POST['filename'] : null;
        
        $post_content = "";

        if ($data == null) {
            die(json_encode(array("response" => 400, "message" => "No data has been received to process")));
        }

        require WPID_DIR . "/inc/fpdf.php";


        $pdf = new FPDF();  // initialize PDF
        $phpWord = new \PhpOffice\PhpWord\PhpWord();    //initialize word
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $section = $phpWord->addSection(); // add section for Word

        $data = json_decode( stripslashes( $data ) );

        foreach( $data as $title=>$content ){

         if( empty( $title ) || empty( $content ) || count( $content ) < 1 ){
            continue;
         }
         
         $pdf->AddPage();

            $pdf->Cell(0,10,'',0,1);
            $pdf->SetFont('Arial','B',18);
            $pdf->Write( 8, $title );

            $section->addText(  $title , array("name"=>"Arial","size"=>"18"), array('space' => array('after' => 200) ) );

            $post_content .= "<h2>" . $title . "<h2>";

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
                  
                  $pdf->Cell(0,10,'',0,1); 
                  if( $ex_cat != $cat_title ){
                     $pdf->Cell(0,10,'',0,1);   $post_content .= "<br/>";
                     $pdf->SetFont('Arial','B',16);
                     $pdf->Write( 8, $cat_title );  $post_content .= "<h4>" . $cat_title . "</h4>" ;
                     $section->addText(  $cat_title , array("name"=>"Arial","size"=>"16") );
                     
                     $ex_cat = $cat_title;
                  }
                  $pdf->SetFont('Arial','B',14);
                  $pdf->Write( 8, $qa_title );  $post_content .= "<h5>". $qa_title ."</h5>";
                  $section->addText(  $qa_title , array("name"=>"Arial","size"=>"14"), array('space' => array('after' => 1200) ) );
                  $pdf->Cell(0,10,'',0,1);  
                  $pdf->Cell(0,40,'',1,1);
                  $post_content .= "<span class='wpid-response'><br/><br/><br/><br/></span>";

               }else{
                  $pdf->Cell(0,20,'',0,1);          $post_content .= "<br/>";
                  $pdf->SetFont('Arial','B',14);
                  $pdf->Write( 8, $single_row );    $post_content .= "<h5>". $single_row . "</h5>";
                  $section->addText(  $single_row , array("name"=>"Arial","size"=>"14"), array('space' => array('after' => 1200) ) );
                  $pdf->Cell(0,10,'',0,1);          $post_content .= "<span class='wpid-response'><br/>";
                  $pdf->Cell(0,40,'',1,1);          $post_content .= "<br/><br/><br/><br/></span>";
               }
            }
        
            $section->addPageBreak();
        }

        $userdata = wp_get_current_user();
        $username = $userdata->user_login;
        wp_insert_post( array(
                'post_status'=>'publish',
                'post_type'=>'wpid_submissions',
                'post_content'=>$post_content,
                'post_title'=> 'Interview Questionnaire By: '. $username  ) );

        $upload_dir = wp_upload_dir(); 
        
        wp_reset_postdata();
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save( $upload_dir['path'] . "/" . str_replace(".pdf",".docx", $file_name ) );

        $pdf->Output("F", $upload_dir['path'] . "/" . $file_name );die();

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
