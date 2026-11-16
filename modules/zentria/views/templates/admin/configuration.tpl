{*
  *  Copyright 2025 Zentria
  *
  *  @author    Zentria <zentria@profileo.com>
  *  @copyright 2025 Zentria
  *  @license   Commercial
  *  link      https://zentria.profileo.com
  *}

<div id="zentria_settings">
    <form id="configuration_form" class="form-horizontal" method="post" enctype="multipart/form-data" novalidate="">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-cogs"></i>&nbsp;{l s='Zentria' mod='zentria'}
            </div>
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="zentria-logo"></span>
                    </label>
                    <div class="col-lg-9 ">
                        <div class="alert alert-success">
                            {l s='The module is successfully installed' mod='zentria'}<br><br>
                            {l s='If you have just installed the module and have a valid subscription, you can start a scan by clicking "Launch a manual scan" on' mod='zentria'} <a target="_blank" href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}">https://zentria.profileo.com/</a>.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Module version' mod='zentria'}
                    </label>
                    <div class="col-lg-9">
                            {$module_version|escape:'html':'UTF-8'}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Module active' mod='zentria'}
                    </label>
                    <div class="col-lg-9 ">
                        {if $is_module_enabled}
                            <div class="alert alert-success mb-none w-100p">{l s='Yes' mod='zentria'}</div>
                        {else}
                            <div class="alert alert-danger mb-none w-100p">{l s='No' mod='zentria'}</div>
                        {/if}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Communication tokens exchanged with the Zentria server' mod='zentria'}
                    </label>
                    <div class="col-lg-9">
                        {if $is_communication_token_set}
                                <div class="alert alert-success mb-none w-100p d-inline-block mr-1">Oui</div>
                                <button type="submit" class="btn btn-default" name="invalidate_token_zentria" {literal}onclick="return confirm('{/literal}{l s='Are you sure you want to invalidate the communication token?' mod='zentria'}{literal}')"{/literal}>{l s='Invalidate token' mod='zentria'}</button>
                        {else}
                            <div class="alert alert-danger mb-none w-100p">{l s='No' mod='zentria'}</div>
                        {/if}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Launch scan' mod='zentria'}
                    </label>
                    <div class="col-lg-9 ">
                        <a href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/index.php?controller=zentriadashboard&setDomain={$domain_name|escape:'html':'UTF-8'}&launch_scan=1" target="_blank" class="btn btn-default">{l s='Launch' mod='zentria'}</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Communication test with the Zentria server' mod='zentria'}
                    </label>
                    <div class="col-lg-9 ">
                        <button type="submit" class="btn btn-default" name="test_communication">{l s='Launch the communication test' mod='zentria'}</button>
                        {if isset($test_communication_resp)}
                            {if ! $test_communication_resp.err}
                                <div class="alert alert-success mt-1">{$test_communication_resp.msg|escape:'html':'UTF-8'}</div>
                            {else}
                                <div class="alert alert-danger mt-1">
                                    {$test_communication_resp.msg|escape:'html':'UTF-8'}
                                    {if ! empty($test_communication_resp.url_controller)}
                                        ,&nbsp;<a href="{$test_communication_resp.url_controller|escape:'html':'UTF-8'}" target="_blank">{$test_communication_resp.url_controller_label|escape:'html':'UTF-8'}</a>
                                    {/if}
                                </div>
                            {/if}
                        {/if}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='A problem? We are here to help you' mod='zentria'}
                    </label>
                    <div class="col-lg-9 ">
                        <a href="https://zentria.profileo.com/{$iso_lang|escape:'html':'UTF-8'}/index.php?controller=contact" target="_blank" class="btn btn-default">{l s='Contact us' mod='zentria'}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>