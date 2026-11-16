{foreach from=$paymentOptions item="paymentOption"}
    <section class="ps-hidden-by-js js-payment-binary js-payment-MoneticoPaiement js-payment-MoneticoPaiement-{$paymentOption.MoneticoPaymentMethodNumber} mt-1 disabled">
        <div id="MoneticoPaiement-{$paymentOption.MoneticoPaymentMethodNumber}-buttons-container">
            <div class="MoneticoPaiement-button" data-mp-cs="{$paymentOption.MoneticoPaymentMethodNumber}">
                <button id="init_{$paymentOption.paymentType}_payment_btn"
                        onclick="init{$paymentOption.paymentType}Payment_{$paymentOption.MoneticoPaymentMethodNumber}()"
                        class="btn btn-primary center-block"
                        type="submit"
                        disabled="true">{$paymentOption.btn_label}
                </button>
            </div>
        </div>
    </section>
{/foreach}