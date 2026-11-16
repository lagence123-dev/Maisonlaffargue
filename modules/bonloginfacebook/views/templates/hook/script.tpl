{*
* 2015-2020 Bonpresta
*
* Bonpresta Facebook login
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
* @author Bonpresta
* @copyright 2015-2020 Bonpresta
* @license http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{* facebook login start *}

<script defer>
  var base_dir = '{$mod_dir|escape:"htmlall":"UTF-8"}',
  islogged = '{$islogged|escape:"htmlall":"UTF-8"}';

  function injectFbSdkAsync(d, s, id) {
    var js,
      fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.async = true;
    js.defer = true;

    js.src = 'https://connect.facebook.net/en_US/sdk.js';
    fjs.parentNode.insertBefore(js, fjs);
  }

  $(function() {
    const $facebookButton = $('.bon-facebooklogin-button');
    setTimeout(() => {
      injectFbSdkAsync(document, 'script', 'facebooklogin-jssdk');
    }, 500);

    $facebookButton.on('click', function() {
      fblogin();
    })

    window.fbAsyncInit = function() {
      FB.init({
        appId: '{$bon_facebook_app_id|escape:"htmlall":"UTF-8"}',
        autoLogAppEvents: true,
        xfbml: false,
        version: 'v7.0',
        status: true
      });
      siteLoginStatus();
    };

    function siteLoginStatus() {
      if (islogged > 0) {
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            var fblogpic = getCookie('fb_user_pic'),
              fblogcheck = getCookie('fb_user_id');
            if (fblogcheck != undefined && fblogcheck.length > 0) {
              $('#bon-login-image').html(
                '<img id="facebook-fb-img" height="31" src="' + fblogpic + '"/>'
              );
              $('.user-info .account.bon-tooltip').css('display', 'none');
              $('.bon-logout').on('click', fblogout);
              $('.account-bon-logout').on('click', fblogout);
            }
          }
        });
      }
    }

    function fblogin() {
      FB.login(
        function(response) {
          setCookie('fb_user_id', response.authResponse.userID, 1);
          if (response.status == 'connected') {
            login(response.authResponse.accessToken);
          }
        }, {
          scope: 'email,public_profile,'
        }
      );
      return false;
    }

    function login(token) {
      get_profile_pic();
      $('.bon-facebooklogin-button-loading').show();
      $.ajax({
        type: 'POST',
        url: base_dir + 'bonloginfacebook/controllers/front/facebookconnect.php',
        data: 'accessToken=' + token + '',
        success: function(msg) {
          window.location.href = login_link;
        }
      });
    }

    function get_profile_pic() {
      FB.api('/me', function(response) {
        var src = 'https://graph.facebook.com/' + response.id + '/picture';
        setCookie('fb_user_pic', src, 1);
      });
    }

    function fblogout() {
      FB.logout(function(response) {
        deleteCookie('fb_user_id');
        deleteCookie('fb_user_pic');
        logout();
      });
    }
  });

  function logout() {
    window.location.href = logout_link;
  }

  function setCookie(c_name, value, expires) {
    document.cookie = c_name + "=" + escape(value);
  }

  function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
  }

  function getCookie(name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
      x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
      y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
      x = x.replace(/^\s+|\s+$/g, "");
      if (x == name) {
        return unescape(y);
      }
    }
  }
</script>

{* facebook login end *}


{* google login start *}

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
  let buttons = document.querySelectorAll(".bon-googlelogin-button");
  var startApp = function() {
    function handleCredentialResponse(response) {
      var userObject = response.credential;
      var id_token = userObject;
      $.ajax({
        type: "POST",
        url: base_dir + 'bonloginfacebook/controllers/front/googleconnect.php',
        data: "accessToken=" + id_token + "",
        success: function(msg) {
          let google_msg = JSON.parse(msg)
          setCookie('google_user_pic', google_msg.picture, 1);
          window.location.href = login_link;
        },
      });
    }

    function renderGoogleButton() {
      buttons.forEach(function(button) {
        google.accounts.id.renderButton(
          button, { theme: "outline", size: "medium", text: "signin", shape: "square", width: 170, logo_alignment: "center" }
        );
      });
    }

    function initializeGoogle() {
      google.accounts.id.initialize({
        client_id: '{$bon_google_app_id|escape:"htmlall":"UTF-8"}',
        callback: handleCredentialResponse,
      });
    }

    function checkGoogleLoaded() {
      if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
        initializeGoogle();
        renderGoogleButton();
        googleStatus();
      } else {
        setTimeout(checkGoogleLoaded, 100);
      }
    }

    checkGoogleLoaded();
  };

  function googleStatus() {
    if (islogged == 1) {
      var googleImg = getCookie('google_user_pic');
      if (typeof googleImg !== 'undefined' && googleImg !== '') {
        $('#bon-login-image').html('<img id="google-img" height="31" src="' + googleImg + '"/>');
        $('.user-info .account.bon-tooltip').css('display', 'none');
        $('.bon-logout').on('click', googleSignOut);
        $('.account-bon-logout').on('click', googleSignOut);
      }
    }
  }

  function googleSignOut() {
    deleteCookie('google_user_pic');
    window.location.href = logout_link;
  }

  setTimeout(startApp, 600);
</script>

{* google login end *}