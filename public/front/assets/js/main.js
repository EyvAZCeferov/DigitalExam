(function($) {
    "use strict";

    // meanmenu
    $('#mobile-menu').meanmenu({
        meanMenuContainer: '.mobile-menu',
        meanScreenWidth: "1199"
    });

    $(window).on('scroll', function() {
        var scroll = $(window).scrollTop();
        if (scroll < 120) {
            $(".header-sticky").removeClass("sticky");
        } else {
            $(".header-sticky").addClass("sticky");
        }
    });


    //mobile side menu
    $('.side-toggle').on('click', function() {
        $('.side-info').addClass('info-open');
        $('.offcanvas-overlay').addClass('overlay-open');
    })

    $('.side-info-close,.offcanvas-overlay').on('click', function() {
        $('.side-info').removeClass('info-open');
        $('.offcanvas-overlay').removeClass('overlay-open');
    });

    function sliderActive() {
        /*------------------------------------
        Slider
        --------------------------------------*/
        if (jQuery(".slider-active").length > 0) {
            let sliderActive1 = '.slider-active';
            let sliderInit1 = new Swiper(sliderActive1, {
                // Optional parameters
                slidesPerView: 1,
                rtl: false,
                slidesPerColumn: 1,
                paginationClickable: true,
                loop: true,
                autoplay: {
                    delay: 5000,
                },

                // If we need pagination
                pagination: {
                    el: ".cinkes-swiper-pagination",
                    clickable: true,
                },

                // Navigation arrows
                navigation: {
                    nextEl: '.slider-swiper-next',
                    prevEl: '.slider-swiper-prev',
                },

                a11y: false
            });

            function animated_swiper(selector, init) {
                let animated = function animated() {
                    $(selector + ' [data-animation]').each(function() {
                        let anim = $(this).data('animation');
                        let delay = $(this).data('delay');
                        let duration = $(this).data('duration');

                        $(this).removeClass('anim' + anim)
                            .addClass(anim + ' animated')
                            .css({
                                webkitAnimationDelay: delay,
                                animationDelay: delay,
                                webkitAnimationDuration: duration,
                                animationDuration: duration
                            })
                            .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                                $(this).removeClass(anim + ' animated');
                            });
                    });
                };
                animated();
                // Make animated when slide change
                init.on('slideChange', function() {
                    $(sliderActive1 + ' [data-animation]').removeClass('animated');
                });
                init.on('slideChange', animated);
            }

            animated_swiper(sliderActive1, sliderInit1);
        }
    }
    sliderActive();

    /* magnificPopup img view */
    $('.popup-image').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });

    /* magnificPopup video view */
    $('.popup-video').magnificPopup({
        type: 'iframe'
    });

    $('.has-nice-select, .contact-form select').niceSelect();
    // data background
    $("[data-background]").each(function() {
            $(this).css("background-image", "url(" + $(this).attr("data-background") + ") ")
        })
        // data width
    $("[data-width]").each(function() {
            $(this).css("width", $(this).attr("data-width"))
        })
        // data background color
    $("[data-bg-color]").each(function() {
            $(this).css("background-color", $(this).attr("data-bg-color"))
        })
        //for menu active class
    $('.portfolio_nav button').on('click', function(event) {
        $(this).siblings('.active').removeClass('active');
        $(this).addClass('active');
        event.preventDefault();
    });

    // scrollToTop
    $.scrollUp({
        scrollName: 'scrollUp', // Element ID
        topDistance: '300', // Distance from top before showing element (px)
        topSpeed: 300, // Speed back to top (ms)
        animation: 'fade', // Fade, slide, none
        animationInSpeed: 200, // Animation in speed (ms)
        animationOutSpeed: 200, // Animation out speed (ms)
        scrollText: '<i class="icofont icofont-long-arrow-up"></i>', // Text for element
        activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
    });

    // brand active
    const brandActive = new Swiper(".brand-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 1500,
        },
        breakpoints: {
            0: {
                slidesPerView: 2,
            },
            576: {
                slidesPerView: 3,
            },
            768: {
                slidesPerView: 4,
            },
            992: {
                slidesPerView: 5,
            },
            1200: {
                slidesPerView: 6
            }
        }
    });

    // category active
    const categoryActive = new Swiper(".category-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: ".category-prev",
            prevEl: ".category-next",
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3
            },
            1400: {
                slidesPerView: 4
            }
        }
    });

    // event active
    const eventActive = new Swiper(".event-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: ".event-prev",
            prevEl: ".event-next",
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3
            }
        }
    });

    // testimonial active
    const testimonialActive = new Swiper(".testimonial-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        speed: 2000,
        scrollbar: {
            el: '.testimonial-scrollbar',
            draggable: true,
        },
        navigation: {
            nextEl: ".event-prev",
            prevEl: ".event-next",
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3
            },
            1400: {
                slidesPerView: 4
            },
            1800: {
                slidesPerView: 5
            }
        }
    });

    const mainslider = new Swiper(".mainslider-active", {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        speed: 2000,
        allowSlideNext: true,
        allowSlidePrev: true,
        allowTouchMove: true,
        hashNavigation: {
            replaceState: true,
        },
        autoplay: {
            delay: 5000,
        },
        pagination: {
            el: '.swiper-pagination', // Noktaların görüntülendiği element
            clickable: true, // Noktalara tıklanabilirlik özelliği
        },
        navigation: {
            nextEl: ".event-prev", // Sonraki slayt için ok
            prevEl: ".event-next", // Önceki slayt için ok
        },
    });

    // testimonial active 2
    const testimonialActiveTwo = new Swiper(".h3_testimonial-active", {
        slidesPerView: 2,
        spaceBetween: 30,
        loop: true,
        speed: 2000,

        navigation: {
            nextEl: ".h3_testimonial-prev",
            prevEl: ".h3_testimonial-next",
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 2,
            },
            1200: {
                slidesPerView: 2
            },
            1400: {
                slidesPerView: 2
            },
            1800: {
                slidesPerView: 2
            }
        }
    });

    // teacher active
    const teacherActive = new Swiper(".teacher-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        speed: 2000,
        pagination: {
            el: ".teacher-pagination",
            clickable: true,
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3
            }
        }
    });


    // feature course active
    const featureCourseActive = new Swiper(".feature_course-active", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,

        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3
            }
        }
    });

    // odometer
    $('.count_one').appear(function(e) {
        var odo = $(".count_one");
        odo.each(function() {
            var countNumber = $(this).attr("data-count");
            $(this).html(countNumber);
        });
    });

    // odometer 2
    $('.count_two').appear(function(e) {
        var odo = $(".count_two");
        odo.each(function() {
            var countNumber = $(this).attr("data-count");
            $(this).html(countNumber);
        });
    });

    // WOW active
    new WOW().init();

    // product quantity in cart
    var productQuantity = 1;
    // quantity form
    $('.single-product-quantity-box .plus').on('click', function() {
        var selectedInput = $(this).closest('.single-product-quantity-box').find('input');
        productQuantity += 1;
        selectedInput.attr('value', productQuantity);
    });
    $('.single-product-quantity-box .minus').on('click', function() {
        var selectedInput = $(this).closest('.single-product-quantity-box').find('input');
        productQuantity -= 1;
        if (productQuantity < 1) {
            productQuantity = 1;
        }
        selectedInput.attr('value', productQuantity);
    });

    // InHover Active
    $('.location-common').on('mouseenter', function() {
        $(this).addClass('active').parent().siblings().find('.location-common').removeClass('active');
    });

})(jQuery);