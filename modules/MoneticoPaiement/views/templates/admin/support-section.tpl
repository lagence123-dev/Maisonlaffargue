<?php
// loaded from \MoneticoPaiement::renderSupportSection()
?>

{if $smarty.const._PS_VERSION_ ge '9.0.0'}
  <style>
    #support_section .panel-heading {
      padding-bottom: 0;
    }

    #support_section .panel-heading i.process-icon-help {
      margin: 0;
    }
  </style>
{/if}


<div class="panel" id="support_section">
    <div class="panel-heading">
        <i class="process-icon-help" style="::before; display:inline;"></i>
        {l s='Support' mod='MoneticoPaiement'}
    </div>

    <div class="panel-body">
        <p>
            {l s='In order to improve the quality and speed of your exchanges with the technical support, please click the \'Download\' button below and join the downloaded file by email with your request.' mod='MoneticoPaiement'}
        </p>

        <br>

        <form method="post">
            <div>
                <input type="hidden" name="type" value="monetico-download-support-data">
                <input type="submit" class="btn btn-primary" name=""
                    value="{l s='Download support data' mod='MoneticoPaiement'}">
            </div>
        </form>

        {if isset($supportFileError) }
            <br>
            <p style="color:red;"><strong>{$supportFileError}</strong></p>
        {/if}
    </div>
</div>
