jQuery(document).ready(function($) {

    window.wpid_competencies_selection = [];
    window.wpid_competencies_request = [];

    if( $(".form-card.request-competencies").find("div ul").length > 0 ){
        $(".form-card.request-competencies").html("<div class='empty'>Processing data</div>");
    }

    $(".wpid-conitue-btn").on("click", function(et) {

        et.preventDefault();

        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Next = $this.parents().find(".form-card.active").next();

        currentEl.removeClass("active");

        Next.addClass("active");
        Next.show();

        currentEl.animate({ opacity: 0 }, {
            step: function(now) {
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

        if (Next.hasClass("last-card")) {
            $this.attr("disabled", "disabled");
        }
        if( Next.find("ul.wpid-main-container").hasClass("core_competencies_list") ){
            if( Next.find("ul.wpid-main-container input:checked").length == 0 ){
                $this.attr("disabled", "disabled");
            }
            
        }

        if( $this.hasClass("request-competencies") ){

            if( window.wpid_competencies_selection != null && window.wpid_competencies_selection != window.wpid_competencies_request )
            {
                window.wpid_competencies_request = window.wpid_competencies_selection;
                $.ajax({
                "url":wpid_data.ajaxurl,
                "type":"POST",
                "dataType":"JSON",
                "data":{"action":"wpid_request_competencies_qa","wp_nonce": wpid_data.wpid_nonce,"core_selections": window.wpid_competencies_request },
                beforeSend:function(dt){
                    $(".form-card.request-competencies").html("");
                    console.log("Requesting data!");
                }}).then(function( data ){
                    if( data == null ){
                        alert("Request failed!")
                    }else{
                        $this.removeClass("request-competencies");
                        $(".form-card.request-competencies").append(data.response);
                    }

                });
            }
        }


    });

    $(document).on("change","ul.wpid-main-container.core_competencies_list input[type='checkbox']", function(et){

        let $this = $(this);

        if( $this.is(":checked") ){
            window.wpid_competencies_selection.push( $this.attr("data-slug") );
        }else{
            let newArray = window.wpid_competencies_selection.filter( function(value,index,arr){
                 if( value != $this.attr("data-slug") ){ return value; }
            });
            window.wpid_competencies_selection = newArray;
        }

        if( $("ul.wpid-main-container.core_competencies_list input:checked").length == 0 ){
            $(".wpid-conitue-btn").attr("disabled","disbaled");
            $(".wpid-conitue-btn").removeClass("request-competencies");
        }else{
            $(".wpid-conitue-btn").removeAttr("disabled");
            $(".wpid-conitue-btn").addClass("request-competencies");

        }

    });

    $(".wpid-back-btn").on("click", function(et) {

        et.preventDefault();

        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Prev = $this.parents().find(".form-card.active").prev();

        currentEl.removeClass("active");
        Prev.addClass("active");
        Prev.show();

        currentEl.animate({ opacity: 0 }, {
            step: function(now) {
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

        $(".wpid-conitue-btn").removeAttr("disabled");

        if( Prev.find("ul.wpid-main-container").hasClass("core_competencies_list") ){
            if( Prev.find("ul.wpid-main-container input:checked").length == 0 ){
                $this.attr("disabled", "disabled");
            }else{
                $(".wpid-conitue-btn").removeAttr("disabled");
                $(".wpid-conitue-btn").addClass("request-competencies");
            }
            
        }


    });

    console.log("Script V: 1.0.0");

})