{*
 * 2015-2017 Bonpresta
 *
 * Bonpresta Online Chat Tawk
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
 *  @copyright 2015-2017 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

<!--Start of Tawk.to Script-->
<script type="text/javascript">
   var Tawk_API = Tawk_API || {},
      Tawk_LoadStart = new Date();
      setTimeout(() => {
         (function() {
            var s1 = document.createElement("script"),
               s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src='https://embed.tawk.to/{$tawk_id|escape:'htmlall':'UTF-8'}';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
            setTimeout(() => {
               let chatIcon = $('iframe[title="chat widget"]')[0];
               chatIcon.style['transform'] = 'translate(-55px, 0)';
            }, 2000);
         })();
      }, 10000);
</script>
<!--End of Tawk.to Script-->
 