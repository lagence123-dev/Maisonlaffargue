{**
 * Advanced Top Menu
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module - https://www.presta-module.com
 * @license   see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *}
{if !empty($class)}<div class="{$class|escape:'htmlall':'UTF-8'}">{/if}

<select {$iduniq = uniqid(mt_rand())} id="atm{$iduniq|escape:'htmlall':'UTF-8'}" style="width:70px;" class="pmSelectFlag">
{foreach from=$languages item=language}
    {assign var='langIsoCode' value=strtoupper($language.iso_code)}
    <option value="{$language.id_lang|intval}" class="pmFlag_{$language.id_lang|intval}"
    {if $language.id_lang == $default_language}selected="selected"{else}selected=""{/if}>{$langIsoCode|escape:'htmlall':'UTF-8'}
    </option>
{/foreach}
</select>

{if !empty($class)}</div>{/if}

<script type="text/javascript">
    initFlags("atm{$iduniq|escape:'htmlall':'UTF-8'}", {$default_language|intval});
</script>
