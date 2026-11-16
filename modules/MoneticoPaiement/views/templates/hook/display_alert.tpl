<div class="alert alert-{if $success}success{else}danger{/if} d-print-none" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true"><i class="material-icons">close</i></span>
    </button>
    <div class="alert-text">
        {if isset($message)}
            <b>
                {$message|escape:'html'}
            </b>
        {/if}
        {if isset($content)}
            {if isset($content.desc)}
                <p>
                    {$content.desc|escape:'html'}
                </p>
            {/if}
            {if isset($content.rmk)}
                <i>
                    {$content.rmk|escape:'html'}
                </i>
            {/if}
        {/if}
    </div>
</div>