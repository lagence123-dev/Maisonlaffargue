/*
 * 2015-2019 Bonpresta
 *
 * Bonpresta Advanced Ajax Live Search Product
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
 *  @copyright 2015-2019 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */
$(document).ready(function () {
    $(document).on("keyup", '#input_search', function(){
        var search_key = $(this).val();
        ajaxSearch(search_key);
    });

    $('html').click(function () {
        $("#search_popup").html('');
    });
    $('#search_popup').click(function (event) {
        event.stopPropagation();
    });

    $('.bonsearch_button i').click(function () {
        $(".bonsearch").toggleClass("active");
    });
    function ajaxSearch(search_key) {
        $.ajax({
            type: 'POST',
            url: bon_search_url + '?ajax=1&static_token_bon_search=' + static_token_bon_search,
            async: true,
            data: 'search_key=' + search_key,
            success: function (data) {
                displaySearchData(data);
            }
        }).done(function (msg) {
            displaySearchData(msg);
        });
    }

    function displaySearchData(data) {
        var search_popup = document.querySelector(".bon-nav-bar #search_popup");
        var search_popup_nav = document.querySelector(".navigation-nav1 #search_popup");
        var search_popup_headertop = document.querySelector(".header-top #search_popup");
        search_popup.innerHTML = data;
        if (search_popup_nav) search_popup_nav.innerHTML = data;
        if (search_popup_headertop) search_popup_headertop.innerHTML = data;
    }

    let $bonsearchMicrophone = $("#bonsearch-microphone");
    const SpeechRecognition = window.webkitSpeechRecognition || window.mozSpeechRecognition || window.msSpeechRecognition;

    if (typeof SpeechRecognition == 'function') {
        let recognition = new SpeechRecognition();
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;
        $bonsearchMicrophone.on('click touch', (e) => {
            e.preventDefault()
            recognition.start();
        })
        recognition.onresult = function (event) {
            const last = event.results.length - 1;
            const result = event.results[last][0].transcript;
            $("#input_search").val(result);
            let inputValue = $("#input_search").val();
            console.log(inputValue);
            ajaxSearch(inputValue);
        };
        recognition.onaudiostart = function () {
            $(' #bonsearch-popup-wrapper, .bonsearch-speek-text, .bonsearch-icon-speech').addClass('show')
            $('.bonsearch-error-text').removeClass('show')
        };
        recognition.onspeechend = function () {
            recognition.stop();
            $('#bonsearch-popup-wrapper .popup-close').click();
        };
        recognition.onerror = function (event) {
            $('.bonsearch-error-text').addClass('show')
            $('.bonsearch-speek-text, .bonsearch-icon-speech').removeClass('show')
        };

      
    } else {
        $bonsearchMicrophone.on('click touch', (e) => {
            e.preventDefault()
            $('.bonsearch-unsupport-text').css('display', 'block')
        })
    }
  //close
    let popupContent = document.querySelector('#bonsearch-popup-wrapper .modal-content');
    let popupModal = document.querySelector('#bonsearch-popup-wrapper');
    popupModal.addEventListener('mousedown', function (e) {
        if (!popupContent.contains(e.target)) {
            $('#bonsearch-popup-wrapper').modal('hide')
        }
    });
});