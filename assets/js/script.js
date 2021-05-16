jQuery(document).ready(function($) {

    console.log("main script is loaded!");

    $(".wpqm-conitue-btn").on("click", function(et) {

        et.preventDefault();

        console.log("clicked!")
        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Next = $this.parents().find(".form-card.active").next();

        currentEl.removeClass("active");
        console.log(Next)


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

        $(".wpqm-back-btn").removeAttr("disabled");

        if (Next.hasClass("last-card")) {
            $this.attr("disabled", "disabled");
        }


    });

    $(".wpqm-back-btn").on("click", function(et) {

        et.preventDefault();

        let $this = $(this);
        let currentEl = $this.parents().find(".form-card.active");
        let Prev = $this.parents().find(".form-card.active").prev();

        currentEl.removeClass("active");
        console.log(Prev)

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

        $(".wpqm-back-btn").removeAttr("disabled");

        if (Prev.hasClass("first-card")) {
            $this.attr("disabled", "disabled");
        }

        $(".wpqm-conitue-btn").removeAttr("disabled");


    });

})