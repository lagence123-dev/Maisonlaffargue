{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="advancedordernotes_wrapper card">
<fieldset class="card-body">
<div class="panel row">

    <div class="panel-heading">
      {l s='Order notes' mod='advancedordernotes'}
    </div>

    <input type="hidden" id="hidden_id_customer" value="{$id_customer|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" id="hidden_id_employee" value="{$id_employee|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" id="aon_token" value="{$aon_token|escape:'htmlall':'UTF-8'}" />

    <table width="100%" cellspacing="0" cellpadding="0" class="table">
        <colgroup>
            <col width="5%">
                <col width="">
                    <col width="20%">
                        <col width="20%">

        </colgroup>
        <thead>
            <tr>
                <th>{l s='#' mod='advancedordernotes'}</th>
                <th>{l s='Order note' mod='advancedordernotes'}</th>
                <th>{l s='Status' mod='advancedordernotes'}</th>
                <th>{l s='Employee' mod='advancedordernotes'}</th>
                <th>{l s='Date' mod='advancedordernotes'}</th>
                {if $note_status_delete == 1 OR $enable_admin == 1}
                    <th>{l s='Actions' mod='advancedordernotes'}</th>
                {/if}
            </tr>
        </thead>

        <tbody id="order_notes">
            {if $size != 0} {assign var=number value=1} {foreach $notes as $note} {if $number == $count_size}
            <tr style="background-color:#73b900;">
                <td style="color:#fff; background-color:#73b900;">{$note.nr|escape:'htmlall':'UTF-8'}</td>
                <td style="color:#fff; background-color:#73b900;">  <div class="aon_text">{$note.note|escape:'htmlall':'UTF-8'}</div>
                    {if $enable_admin == 1 AND $aon_is_admin == true}
                        <div class="hidden_el aon_element">
                            <textarea style="margin-bottom: 5px; margin-top:10px;" class="aon_textarea textarea_{$note.id|escape:'htmlall':'UTF-8'}">{$note.note|escape:'htmlall':'UTF-8'}</textarea>
                            <button class="btn btn-default aon_update_textarea" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Update' mod='advancedordernotes'}</button>                           
                        </div>

                    {/if}</td>
                <td style="color:#fff; background-color:#73b900;">

                    {if $note.note_status}<span class="note_status" style="padding:0px 10px; border-radius:5px;  background-color:{$note.note_background|escape:'htmlall':'UTF-8'}; color:{$note.note_color|escape:'htmlall':'UTF-8'}">{$note.note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}
                     <div class="hidden_el aon_element">
                       
                        <select class="aon_status_update aon_update_status_{$note.id|escape:'htmlall':'UTF-8'}"  rel="{$note.id|escape:'htmlall':'UTF-8'}"  style="margin-bottom: 5px; margin-top:10px;" >
                            <option value="">{l s='No status' mod='advancedordernotes'}</option>
                            {foreach from=$note_statuses item=s}
                                <option value="{$s.name|escape:'htmlall':'UTF-8'}">{$s.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>

                        <button class="btn btn-default aon_update_status"  style="margin-bottom: 5px; margin-top:10px;" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Update' mod='advancedordernotes'}</button>     
                    </div>

                </td>
                <td style="color:#fff; background-color:#73b900;">{$note.employee|escape:'htmlall':'UTF-8'}</td>
                <td style="color:#fff; background-color:#73b900;">{$note.date|escape:'htmlall':'UTF-8'}</td>
                 {if $note_status_delete == 1 OR $enable_admin == 1}
                    <td style="color:#fff; background-color:#73b900;">

                        {if $note_status_delete == 1}
                            <button class="btn btn-default delete_note" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Remove' mod='advancedordernotes'}</button>
                        {/if}

                        {if $enable_admin == 1 AND $aon_is_admin == true}
                            <button class="btn btn-default edit_aon" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Edit' mod='advancedordernotes'}</button>
                        {/if}

                    </td>
                {/if}
            </tr>
            {else}
            <tr>
                <td>{$note.nr|escape:'htmlall':'UTF-8'}</td>
                <td><div class="aon_text">{$note.note|escape:'htmlall':'UTF-8'}</div>
                    {if $enable_admin == 1 AND $aon_is_admin == true}
                        <div class="hidden_el aon_element">
                            <textarea style="margin-bottom: 5px; margin-top:10px;" class="aon_textarea textarea_{$note.id|escape:'htmlall':'UTF-8'}">{$note.note|escape:'htmlall':'UTF-8'}</textarea>
                            <button class="btn btn-default aon_update_textarea" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Update' mod='advancedordernotes'}</button>                           
                        </div>

                    {/if}</td>
                <td>
                    {if $note.note_status}<span class="note_status" style="padding:0px 10px; border-radius:5px;  background-color:{$note.note_background|escape:'htmlall':'UTF-8'}; color:{$note.note_color|escape:'htmlall':'UTF-8'}">{$note.note_status|escape:'htmlall':'UTF-8'}</span>{else}-{/if}
                     <div class="hidden_el aon_element">
                       
                        <select class="aon_status_update aon_update_status_{$note.id|escape:'htmlall':'UTF-8'}"  rel="{$note.id|escape:'htmlall':'UTF-8'}"  style="margin-bottom: 5px; margin-top:10px;" >
                            <option value="">{l s='No status' mod='advancedordernotes'}</option>
                            {foreach from=$note_statuses item=s}
                                <option value="{$s.name|escape:'htmlall':'UTF-8'}">{$s.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>

                        <button class="btn btn-default aon_update_status"  style="margin-bottom: 5px; margin-top:10px;" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Update' mod='advancedordernotes'}</button>     
                    </div>
                </td>
                <td>{$note.employee|escape:'htmlall':'UTF-8'}</td>
                <td>{$note.date|escape:'htmlall':'UTF-8'}</td>
                 {if $note_status_delete == 1 OR $enable_admin == 1}
                    <td style="color:#fff; background-color:#73b900;">

                        {if $note_status_delete == 1}
                            <button class="btn btn-default  delete_note" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Remove' mod='advancedordernotes'}</button>
                        {/if}
                       
                        {if $enable_admin == 1 AND $aon_is_admin == true}
                            <button class="btn btn-default  edit_aon" rel="{$note.id|escape:'htmlall':'UTF-8'}">{l s='Edit' mod='advancedordernotes'}</button>
                        {/if}

                    </td>
                {/if}
            </tr>
            {/if} {$number = $number +1} {/foreach} {else}
            <tr class="noorders">
                <td colspan="5">{l s='There are no notes for this order' mod='advancedordernotes'}</td>
            </tr>
            {/if}
        </tbody>
    </table>

    <br/>
    <div class="col-lg-12 col-md-12">
        <textarea class="col-lg-12 col-md-12"  id="ordernotes_comment" placeholder="{l s='Enter your note here...' mod='advancedordernotes'}"></textarea>
        {if $note_status == 1}
       
                <select id="note_statuses" class="col-lg-2 col-md-2" style="float:left;margin-top:15px;">
                    <option value="">{l s='No status' mod='advancedordernotes'}</option>
                    {foreach from=$note_statuses item=s}
                        <option value="{$s.name|escape:'htmlall':'UTF-8'}">{$s.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
        {else}
        <input type="hidden" name="" id="note_statuses" value=""  />            
        {/if}
        <input class="button btn btn-primary pull-right" type="submit" name="addNote" id="addOrderNotecustomer" value="{l s='Add note' mod='advancedordernotes'}" style="float:right; margin-top:15px; " />
        <div style="clear:both"></div>
    </div>

    <div class="clear" style="margin-bottom: 10px;"></div>
</div>
</fieldset>
<br/>
</div>