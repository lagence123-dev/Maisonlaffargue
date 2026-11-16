<{$atmLink.tag|escape:'htmlall':'UTF-8'}
    {$atmLink.linkAttribute}="{$atmLink.url|escape:'htmlall':'UTF-8'}"
    title="{$atmLink.title|escape:'htmlall':'UTF-8'}"
    {if !empty($atmLink.row.target)} target="{$atmLink.row.target|escape:'htmlall':'UTF-8'}"{/if}
    class="{if $atmLink.isNotClickable}adtm_unclickable{/if} {if $atmLink.isMultiline}a-multiline{/if} {if !empty($atmLink.isFirstLevel)}a-niveau1{/if}"
    {if !empty($atmLink.data_type.type)} data-type="{$atmLink.data_type.type|escape:'htmlall':'UTF-8'}"{/if}
    {if isset($atmLink.data_type.id) && $atmLink.data_type.id} data-id="{$atmLink.data_type.id|escape:'htmlall':'UTF-8'}"{/if}
>

{if $atmLink.type == 'menu'}
    <span class="advtm_menu_span advtm_menu_span_{$atmLink.row.id_menu|intval}">
{/if}
{strip}
{if $atmLink.icon}
    {if in_array($atmLink.row.image_type, ['i-fa', 'i-mi'])}
        <i class="pmAtmIcon {$atmLink.row.image_class|escape:'htmlall':'UTF-8'}"></i>
    {else}
        <img src="{$atmLink.icon.src|escape:'htmlall':'UTF-8'}"
            alt="{$atmLink.icon.alt|escape:'htmlall':'UTF-8'}"
            title="{$atmLink.icon.alt|escape:'htmlall':'UTF-8'}"
            {if $atmLink.icon.width && $atmLink.icon.width > 0} width="{$atmLink.icon.width|intval}"{/if}
            {if $atmLink.icon.height && $atmLink.icon.height > 0} height="{$atmLink.icon.height|intval}"{/if}
            class="adtm_menu_icon img-responsive img-fluid"
            loading="lazy"
        />
    {/if}
{/if}

<span>{$atmLink.title|escape:'htmlall':'UTF-8'|nl2br nofilter}</span>
{/strip}
{if $atmLink.type == 'menu'}
    </span>
{/if}

</{$atmLink.tag|escape:'htmlall':'UTF-8'}>
