jQuery(document).ready(function($) {

    console.log("main script is loaded!");

    $(".wpqm-conitue-btn").on("click", function(et) {

        et.preventDefault();

        $("#wpqm-questionnaire-slider").carousel("next");

    });

})