/**
 * 2015-2020 Bonpresta
 *
 * Bonpresta Awesome Image Slider
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2020 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */

$(document).ready(function () {

    // determines the number of slides
    let numberSlides = $('#bonswiper .swiper-slide').length;

    if (BON_SLICK_CAROUSEL_AUTOPLAY == 1) {
        var BON_CAROUSEL_TIME = BON_SLICK_CAROUSEL_TIME;
    } else {
        var BON_CAROUSEL_TIME = 9999999999999999;
    }
    if (BON_SLICK_CAROUSEL_LOOP == 1) {
        var BON_SLICK_CAROUSEL_LOOP_SCRIPT = true;
    } else {
        var BON_SLICK_CAROUSEL_LOOP_SCRIPT = false;
    }
    if (BON_SLICK_CAROUSEL_DRAG == 1) {
        var BON_SLICK_CAROUSEL_DRAG_SCRIPT = false;
    } else {
        var BON_SLICK_CAROUSEL_DRAG_SCRIPT = true;
    }

    var swiper = new Swiper("#bonswiper", {
        speed: 1000,
        preloadImages: false,
        loop: BON_SLICK_CAROUSEL_LOOP_SCRIPT,
        cssMode: BON_SLICK_CAROUSEL_DRAG_SCRIPT,
        parallax: true,
        autoplay: {
            delay: BON_CAROUSEL_TIME,
        },
        navigation: {
            nextEl: "#bonswiper .bonswiper-button-next",
            prevEl: "#bonswiper .bonswiper-button-prev",
        },

        pagination: {
            el: "#bonswiper .bonswiper-pagination",
            clickable: true,
        },
    });

    removeVideoMobile(swiper);
    var player = document.getElementById('video-element');

    if (player != null) {
        sliderChange(swiper, numberSlides);
    }
});

function sliderChange(swiper, numberSlides) {
    swiper.on('activeIndexChange', function () {
        btnMute.removeAttribute('title');
        btnPlayPause.removeAttribute('title');

        var slideActive = $('.swiper-slide-active');
        var realIndex = slideActive.data('swiper-slide-index')+1;
        if(typeof realIndex === 'undefined') {
            realIndex = slideActive.index() + 1;
        }
let vid =  this.slides[this.activeIndex];

 
        if ($(vid).children('#video-container').hasClass('video-container')) {
            if ($('#btnPlayPause').hasClass('play') || $('#btnPlayPause').hasClass('pause')) {
                $('#btnPlayPause').trigger('click');
            } else {
                $('.pause').trigger('click');
            }
        } else {
            $('.pause').trigger('click');
            
        }
    });

    // $("#bonswiper").on('afterChange', function (event, swiper, currentSlide) {
    //     btnMute.removeAttribute('title');
    //     btnPlayPause.removeAttribute('title');
    //     if (currentSlide == $('#bonswiper .swiper-slide').length - 1) {
    //         if ($('#btnPlayPause').hasClass('play') || $('#btnPlayPause').hasClass('pause')) {
    //             $('#btnPlayPause').trigger('click').attr('class', 'pause').addClass('active-sound');
    //         } else {
    //             $('.pause').trigger('click');
    //         }
    //         $('.slick-prev').addClass('white-arrow');
    //         $('.slick-next').addClass('white-arrow');
    //     } else {
    //         $('.pause').trigger('click');
    //         $('.slick-prev').removeClass('white-arrow');
    //         $('.slick-next').removeClass('white-arrow');
    //     }
    // });
}

$(window).resize(function () {
    removeVideoMobile(swiper);
});

var player = document.getElementById('video-element');
var btnPlayPause = document.getElementById('btnPlayPause');
var btnMute = document.getElementById('btnMute');

if (player != null) {
    playVideo();
}

function playVideo() {
    player.addEventListener('play', function() {
        changeButtonType(btnPlayPause, 'pause');
    }, false);

    player.addEventListener('pause', function() {
        changeButtonType(btnPlayPause, 'play');
    }, false);


    player.addEventListener('ended', function() {
        this.pause();
    }, false);
}

function playPauseVideo() {
    if (player.paused || player.ended) {
        changeButtonType(btnPlayPause, 'pause');
        player.play();
    } else {
        changeButtonType(btnPlayPause, 'play');
        player.pause();
    }
}

function stopVideo() {
    player.pause();
    if (player.currentTime) player.currentTime = 0;
}

function muteVolume() {
    if (player.muted) {
        // Change the button to a mute button
        changeButtonType(btnMute, 'mute');
        player.muted = false;
    } else {
        // Change the button to an unmute button
        changeButtonType(btnMute, 'unmute');
        player.muted = true;
    }
}

function changeButtonType(btn, value) {
    btn.innerHTML = value;
    btn.className = value;
}

function removeVideoMobile(swiper) {
    if ($(window).width() <= '575') {
        for (let i = 0; i < $('#bonswiper .swiper-slide').length; i++) {
            let videoMobile = $('#video-container').parent();
            let index = videoMobile.attr('data-swiper-slide-index');
            swiper.removeSlide(index);
        }
    }
}