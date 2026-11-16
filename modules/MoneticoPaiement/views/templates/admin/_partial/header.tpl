<div class="bootstrap">
    <div class="panel">
        <div class="row monetico-header">
            <div class="col-sm-6">
                <div>
                    <img src="{$smarty.const.__PS_BASE_URI__}modules/MoneticoPaiement/views/img/logo.png">
                    <div style="padding-top:5px">
                        <span class="badge badge-success">v{$actual_version|escape:'html'}</span>
                        <a href="{$smarty.const.__PS_BASE_URI__}modules/MoneticoPaiement/documentation/Module_PrestaShop_Documentation_Utilisateur.pdf"
                           target="_blank">{l s='Link to documentation' mod='MoneticoPaiement'}</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
            </div>
        </div>
    </div>
</div>

{if isset($smarty.session.MP_THEMIS_DATA) and isset($smarty.get.configure) and $smarty.get.configure eq 'MoneticoPaiement'}
    {assign var=displayable_notice value={isset($smarty.session.MP_THEMIS_DATA.current_status.status) and ($smarty.session.MP_THEMIS_DATA.current_status.status eq 'deprecated' or $smarty.session.MP_THEMIS_DATA.current_status.status eq 'obsolete')}}
    {if $displayable_notice eq true and isset($smarty.session.MP_THEMIS_DATA.current_status.statusDescription)}
        {if $smarty.session.MP_THEMIS_DATA.current_status.status eq 'deprecated'}
            {assign var=type value='warning'}
        {elseif $smarty.session.MP_THEMIS_DATA.current_status.status eq 'obsolete'}
            {assign var=type value='danger'}
        {/if}
        <div id="session_themis_notif">
            <div class="bootstrap">
                <div class="module_confirmation conf confirm alert alert-{$type}">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <p>
                        <b>{$smarty.session.MP_THEMIS_DATA.general_data.module_name}</b><br>

                        {$smarty.session.MP_THEMIS_DATA.current_status.statusDescription}

                        {if isset($smarty.session.MP_THEMIS_DATA.future_status.formated_date_next_status)}
                            <em>({$smarty.session.MP_THEMIS_DATA.future_status.formated_date_next_status})</em>
                        {/if}
                        {if isset($smarty.session.MP_THEMIS_DATA.general_data.dl_module_href) and isset($smarty.session.MP_THEMIS_DATA.general_data.dl_module_text)}
                            <br>
                            <a href="{$smarty.session.MP_THEMIS_DATA.general_data.dl_module_href}"
                               target="_blank">{$smarty.session.MP_THEMIS_DATA.general_data.dl_module_text}</a>
                        {/if}
                    </p>
                </div>
            </div>
        </div>
    {/if}
{/if}

{if isset($smarty.session.MP_DEMO_MODE)}
    <div id="session_demo_notif">
        {foreach from=$smarty.session.MP_DEMO_MODE key=$type item=$messages}
            <div class="bootstrap">
                <div class="module_confirmation conf confirm alert alert-{$type}">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {if $type eq 'warning'}
                        {$messages.always_display}
                        {if isset($smarty.get.type) and !isset($smarty.get.deleteMoneticoPaiement) and $smarty.get.type eq 'codesite'}
                            &nbsp{$messages.on_codesite_form_display}
                        {elseif isset($smarty.get.type) and !isset($smarty.get.deleteMoneticoPaiement) and $smarty.get.type eq 'ept'}
                            &nbsp{$messages.on_ept_form_display}
                        {/if}
                    {else}
                        {foreach from=$messages item=$msg}
                            <p>{$msg}</p>
                        {/foreach}
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
{/if}
