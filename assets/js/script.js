jQuery(document).ready(function ($) {

    window.wpid_competencies_selection = [];
    window.wpid_competencies_request = [];

    window.wpid_selected_array = {};


    $(document).on("click", ".submit-selected-qa", function (et) {
        let $this = $(this);
        let filename = $this.attr('data-filename');
        let fileurl = $this.attr('data-fileurl');

        et.preventDefault();

        $("#wpid-questionnaire-container .form-card:not(.wpid-ajax-ignore)").each(
            function (index, Obj) {
                let title = $(Obj).find("div .section-title").text();
                
                let selections = [];
                $(Obj).find("div .wpid-main-container li input:checked").each(function (inde, oj) {
                    selections.push($(oj).val());
                });
                    window.wpid_selected_array[ title ] = selections ;
            }
        );

        $this.attr("disabled","disabled");
        
         $(".wpid-back-btn").hide();

        $.ajax({
            "url":wpid_data.ajaxurl,
            "type":"POST",
            "dataType":"JSON",
            "data": {"action":"wpid_generate_questionnaire_files","data":JSON.stringify( window.wpid_selected_array ),"filename":filename},
            "beforeSend":function( res ){

            }
        }).complete(function(data){
            $("#wpid-questionnaire-controller").append("<a download class='btn btn-primary' href='"+fileurl+"'>DOWNLOAD PDF FILE</a>" );
            $("#wpid-questionnaire-controller").append("<a download class='btn btn-primary' href='"+fileurl.replace(".pdf",".docx") +"'>DOWNLOAD DOCX FILE</a>" );
        });

    });


    if ($(".form-card.request-competencies").find("div ul").length == 0) {
        $(".form-card.request-competencies").html("<div class='empty'>Processing data</div>");
    }

    $(".wpid-conitue-btn").on("click", function (et) {

        et.preventDefault();

        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Next = currentEl.next();

        if( currentEl.find(".wpid-info-section").length > 0 ){
            
            let title = currentEl.find(".wpid-info-section #wpid-position-title").val();
            let position_type = currentEl.find(".wpid-info-section #wpid-position-type").val();
            let industry_name = currentEl.find(".wpid-info-section #wpid-industry-name").val();
            
            if( title == "" || position_type == "" || industry_name == "" ){
                alert("This form can not be left blank. All fields are required!");
                return;
            }


            $.ajax({
                "url":wpid_data.ajaxurl,
                "type":"POST",
                "dataType":"JSON",
                "data":{ "action":"wpid_update_user_meta","wpid_position_title":title,"wpid_position_type":position_type,"wpid_industry":industry_name },
                "beforeSend":function(d){
                    
                }
            }).then(function(data){

            });

        }

        if (Next.hasClass("last-card")) {

            $this.hide();
            $(".submit-selected-qa").show();

        }

        currentEl.removeClass("active");

        Next.addClass("active");
        Next.show();

        currentEl.animate({ opacity: 0 }, {
            step: function (now) {
                // for making fielset appear animation
                opacity = 1 - now;

                currentEl.css({
                    'display': 'none',
                    'position': 'relative'
                });
                Next.css({ 'opacity': opacity });
            },
            duration: 600
        });

        $(".wpid-back-btn").removeAttr("disabled");

        if (Next.find("ul.wpid-main-container").hasClass("core_competencies_list")) {
            if (Next.find("ul.wpid-main-container input:checked").length == 0) {
                $this.attr("disabled", "disabled");
            }

        }

        if ($this.hasClass("request-competencies")) {

            if (window.wpid_competencies_selection != null && window.wpid_competencies_selection != window.wpid_competencies_request) {
                window.wpid_competencies_request = window.wpid_competencies_selection;
                $.ajax({
                    "url": wpid_data.ajaxurl,
                    "type": "POST",
                    "dataType": "JSON",
                    "data": { "action": "wpid_request_competencies_qa", "wp_nonce": wpid_data.wpid_nonce, "core_selections": window.wpid_competencies_request },
                    beforeSend: function (dt) {
                        $(".form-card.request-competencies").html("");
                        
                    }
                }).then(function (data) {
                    if (data == null) {
                        alert("Request failed!")
                    } else {
                        $this.removeClass("request-competencies");
                        $(".form-card.request-competencies").append(data.response);
                    }

                });
            }
        }

    });

    $(document).on("change", "ul.wpid-main-container.core_competencies_list input[type='checkbox']", function (et) {

        let $this = $(this);

        if ($this.is(":checked")) {
            window.wpid_competencies_selection.push($this.attr("data-slug"));
        } else {
            let newArray = window.wpid_competencies_selection.filter(function (value, index, arr) {
                if (value != $this.attr("data-slug")) { return value; }
            });
            window.wpid_competencies_selection = newArray;
        }

        if ($("ul.wpid-main-container.core_competencies_list input:checked").length == 0) {
            $(".wpid-conitue-btn").attr("disabled", "disbaled");
            $(".wpid-conitue-btn").removeClass("request-competencies");
        } else {
            $(".wpid-conitue-btn").removeAttr("disabled");
            $(".wpid-conitue-btn").addClass("request-competencies");

        }

    });

    $(".wpid-back-btn").on("click", function (et) {

        et.preventDefault();

        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Prev = $this.parents().find(".form-card.active").prev();

        currentEl.removeClass("active");
        Prev.addClass("active");
        Prev.show();

        currentEl.animate({ opacity: 0 }, {
            step: function (now) {
                // for making fielset appear animation
                opacity = 1 - now;

                currentEl.css({
                    'display': 'none',
                    'position': 'relative'
                });
                Prev.css({ 'opacity': opacity });
            },
            duration: 600
        });

        $(".wpid-back-btn").removeAttr("disabled");

        if (Prev.hasClass("first-card")) {
            $this.attr("disabled", "disabled");
        }

        if ($(".wpid-conitue-btn").hide() ) {
            $(".wpid-conitue-btn").show();
            $(".submit-selected-qa").hide();
            $(".submit-selected-qa").removeAttr("disabled");
        }

        $(".wpid-conitue-btn").removeAttr("disabled");

        if (Prev.find("ul.wpid-main-container").hasClass("core_competencies_list")) {
            if (Prev.find("ul.wpid-main-container input:checked").length == 0) {
                $this.attr("disabled", "disabled");
            } else {
                $(".wpid-conitue-btn").removeAttr("disabled");
                $(".wpid-conitue-btn").addClass("request-competencies");
            }

        }


    });

})