{if $all_alias|count}
    <div class="col">
        <div class="card panel customer-cb-register-card">
            <h3 class="card-header panel-header">
                <i class="material-icons">payment</i>
                {l s="Registered payments cards"  mod='MoneticoPaiement'} (Monetico Paiement)

                <span class="badge badge-primary rounded">{$all_alias|count}</span>
            </h3>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{l s="Company code name"  mod='MoneticoPaiement'}</th>
                        <th>{l s="Alias"  mod='MoneticoPaiement'}</th>
                    </tr>
                    </thead>

                    <tbody>
                    {foreach  from=$all_alias item=alias}
                        <tr class="customer-address">
                            <td class="customer-address-company js-linkable-item cursor-pointer"
                                data-linkable-href="/">{$alias.code_site_name|escape:'html'}</td>
                            <td class="customer-address-name js-linkable-item cursor-pointer">{$alias.value|escape:'html'}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}