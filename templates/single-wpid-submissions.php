<?php
/**
 * 
 * 
 * 
 */

 get_header();

 ?> 
 <?php

$post_id = get_the_ID();

 echo "<div id='wpid-current-submissions' class='wpid-display-form'>";

 echo "<div id='wpid-controller-container'>";
 
 echo "<a class='btn btn-primary' href='#' id='wpid-print-dive'>PRINT DIVE</a>";
 $pdf_fileurl = get_post_meta( $post_id, "wpid_submissions_pdf_file", true );
 $doc_fileurl = get_post_meta( $post_id, "wpid_submissions_doc_file", true );

 if( !empty( $pdf_fileurl ) ){
     echo "<a download class='btn btn-primary' href='". $pdf_fileurl ."'>SAVE AS PDF</a>";
 }

 if( !empty( $doc_fileurl ) ){
     echo "<a download class='btn btn-primary' href='". $doc_fileurl ."'>SAVE AS DOCX</a>";
 }

 if( !empty( $post_id ) ){
    echo "<a class='btn btn-primary' href='#' id='wpid-email-dive'>EMAIL DIVE</a>";
 }

 echo "</div>";


 echo "<div class='wpid-content' data-post-id='". $post_id ."'>";

 echo the_content( );

echo "</div></div>";

 get_footer();