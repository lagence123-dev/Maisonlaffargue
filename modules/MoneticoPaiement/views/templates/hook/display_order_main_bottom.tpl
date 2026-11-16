{if $nb_refund>0}
    <div class="card mt-2" id="view_monticopaiement_refund_payments_block">
        <div class="card-header">
            <h3 class="card-header-title">
                {l s="Payment Refund with Monetico Paiement" mod='MoneticoPaiement'} ({$nb_refund|escape:'html'})
            </h3>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th class="table-head-date">{l s="Date" mod='Admin.Global'}</th>
                    <th class="table-head-amount">{l s="Amount" mod='Admin.Global'}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach  from=$refunds item=refund}
                    <tr>
                        <td>{$refund.date|escape:'html'}</td>
                        <td>{$refund.amount|escape:'html'}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}

{if $nb_recoveries > 0}
    <div class="card mt-2" id="view_monticopaiement_refund_payments_block">
        <div class="card-header">
            <h3 class="card-header-title">
                {if $nb_recoveries > 1}
                    {l s="Recoveries with Monetico Paiement" mod='MoneticoPaiement'}
                {else}
                    {l s="Recovery with Monetico Paiement" mod='MoneticoPaiement'}
                {/if}
                &nbsp;({$nb_recoveries|escape:'html'})
            </h3>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th class="table-head-date">{l s="Date" mod='Admin.Global'}</th>
                    <th class="table-head-amount">{l s="Amount" mod='Admin.Global'}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach  from=$recoveries item=recovery}
                    <tr>
                        <td>{$recovery.date|escape:'html'}</td>
                        <td>{$recovery.amount|escape:'html'}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}

{if $nb_instalment>0}
    <div class="card mt-2" id="view_monticopaiement_multiple_payments_block">
        <div class="card-header">
            <h3 class="card-header-title">
                {l s="Instalment Payment with Monetico Paiement" mod='MoneticoPaiement'} ({$nb_instalment|escape:'html'})
            </h3>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th class="table-head-number">{l s="Instalment Number" mod='MoneticoPaiement'}</th>
                    <th class="table-head-date">{l s="Date" mod='MoneticoPaiement'}</th>
                    <th class="table-head-amount">{l s="Amount" mod='MoneticoPaiement'}</th>
                    <th class="table-head-statement">{l s="Statement" mod='MoneticoPaiement'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach  from=$instalments item=instalment}
                    <tr>
                        <td>{$instalment.number|escape:'html'}</td>
                        <td>{$instalment.date|escape:'html'}</td>
                        <td>{$instalment.amount|escape:'html'}</td>
                        <td>
                            {if $instalment.succesfull_state===1 }
                                <span class="badge badge-success">{$instalment.state|escape:'html'}</span>
                            {elseif  $instalment.succesfull_state===-1 }
                                <span class="badge badge-danger">{$instalment.state|escape:'html'}</span>
                            {else}
                                <span class="badge badge-info">{$instalment.state|escape:'html'}</span>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}

