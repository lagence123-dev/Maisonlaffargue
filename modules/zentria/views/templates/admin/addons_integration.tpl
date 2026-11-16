{*
  *  Copyright 2025 Zentria
  *
  *  @author    Zentria <zentria@profileo.com>
  *  @copyright 2025 Zentria
  *  @license   Commercial
  *  link      https://zentria.profileo.com
  *}

<prestashop-accounts></prestashop-accounts>

{*/**********************}
{** PrestaShop Account **}
{** *******************/*}
{if isset($urlAccountsCdn)}
    <script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>

    <script>
        window?.psaccountsVue?.init();
        if(window.psaccountsVue.isOnboardingCompleted() != true && document.getElementById("module-config") != null)
        {
            document.getElementById("module-config").style.opacity = "0.5";
        }
    </script>
    <br/>
{/if}