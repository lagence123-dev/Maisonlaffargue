{if $nb_refund>0}
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-money"></i>
            {l s="Payment Refund with Monetico Paiement" mod='MoneticoPaiement'} <span class="badge">{$nb_refund|escape:'html'}</span>
        </div>
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th class="table-head-date">{l s="Date" mod='Admin.Global'} </th>
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

{if $nb_instalment>0}
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-money"></i>
            {l s="Instalment Payment with Monetico Paiement" mod='MoneticoPaiement'} <span
                    class="badge">{$nb_instalment|escape:'html'}</span>
        </div>
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th class="table-head-number">{l s="Instalment Number" mod='MoneticoPaiement'}</th>
                    <th class="table-head-date">{l s="Date" mod='MoneticoPaiement'} </th>
                    <th class="table-head-amount">{l s="Amount" mod='MoneticoPaiement'}</th>
                    <th class="table-head-state">{l s="Statement" mod='MoneticoPaiement'}</th>
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
                                <span class="label label-success">{$instalment.state|escape:'html'}</span>
                            {elseif  $instalment.succesfull_state===-1 }
                                <span class="label label-danger">{$instalment.state|escape:'html'}</span>
                            {else}
                                <span class="label label-info">{$instalment.state|escape:'html'}</span>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}