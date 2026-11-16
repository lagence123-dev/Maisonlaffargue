{if $atmItemWithExtraIcon && !empty($atmItem.have_icon)}
    {if in_array($atmItem.image_type, ['i-fa', 'i-mi'])}
        <i class="pmAtmIcon {$atmItem.image_class|escape:'htmlall':'UTF-8'}"></i>
    {else}
        <img src="{$iconPath|escape:'htmlall':'UTF-8'}" alt="{$atmItem.name|escape:'htmlall':'UTF-8'}" title="{$atmItem.name|escape:'htmlall':'UTF-8'}" />
    {/if}
{/if}
{$atmItem.name|escape:'htmlall':'UTF-8'}
