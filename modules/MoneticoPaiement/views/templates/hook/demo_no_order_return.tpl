{extends "$layout"}

{block name="content"}
    <h1>{l s="Congratulations !"  mod='MoneticoPaiement'}</h1>
    <p>{l s="You have just completed the payment of your order using Monetico Paiement !" mod='MoneticoPaiement'}</p>
    <div>
        <p>
            {l s="You have activated the demo mode of our module, but no order has been created in your back-office." mod='MoneticoPaiement'}<br>
            {l s="If you want to create an order for each payment simulation, please activate \"Demonstration orders\" in the configuration page of the module." mod='MoneticoPaiement'}
        </p>
    </div>
{/block}