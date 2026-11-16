<div id="ipa-wrapper-{$code_site_id}">
    <div id="component-wrapper-{$code_site_id}" style="max-width: 600px; margin: 3% auto"></div>
    <span class="loader"></span>
</div>

<style>
    #ipa-wrapper-{$code_site_id} {
        width: 100%;
    }

    #ipa-wrapper-{$code_site_id} .loader {
        margin: 0 auto;
    }

    #ipa-wrapper-{$code_site_id} .loader.active {
        display: block;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 9px solid #f39517;
        animation: mp-spinner-animation-1-{$code_site_id} 0.8s infinite linear alternate,
        mp-spinner-animation-2-{$code_site_id} 1.6s infinite linear;
    }

    @keyframes mp-spinner-animation-1-{$code_site_id} {
        0% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%);
        }

        12.5% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 0%, 100% 0%, 100% 0%);
        }

        25% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 100% 100%, 100% 100%);
        }

        50% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%);
        }

        62.5% {
            clip-path: polygon(50% 50%, 100% 0, 100% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%);
        }

        75% {
            clip-path: polygon(50% 50%, 100% 100%, 100% 100%, 100% 100%, 100% 100%, 50% 100%, 0% 100%);
        }

        100% {
            clip-path: polygon(50% 50%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 0% 100%);
        }
    }

    @keyframes mp-spinner-animation-2-{$code_site_id} {
        0% {
            transform: scaleY(1) rotate(0deg);
        }

        49.99% {
            transform: scaleY(1) rotate(135deg);
        }

        50% {
            transform: scaleY(-1) rotate(0deg);
        }

        100% {
            transform: scaleY(-1) rotate(-135deg);
        }
    }
</style>

<script>
    let paymentComponents_{$code_site_id};
    let paymentComponent_{$code_site_id};
    let transaction_seal_{$code_site_id} = '{$transaction_seal}';
    let transaction_date_{$code_site_id} = '{$date}';
    let transaction_reference_{$code_site_id} = '{$reference}';
    let loader = document.querySelector('#ipa-wrapper-{$code_site_id} .loader')
    let condition_to_approve;
    let init_payment_btn;

    function setDomElements() {
        condition_to_approve = document.getElementById('conditions-to-approve')
        init_payment_btn = document.getElementById('init_Ipa_payment_btn');
    }

    function hideBtnAndCgv() {
        condition_to_approve.style.display = 'none';
        init_payment_btn.style.display = 'none';
    }

    function showBtnAndCgv() {
        condition_to_approve.style.display = 'block';
        init_payment_btn.style.display = 'block';
    }

    function initIpaPayment_{$code_site_id}() {
        init_payment_btn.disabled = true;
        loader.classList.toggle('active');

        $.ajax({
            type: "POST",
            // 'nofilter' modifier to the paymentSessionUrl template because it would fuck up ampersands (&),
            // which would render links without url rewrite unusable
            url: '{$paymentSessionUrl nofilter}',
            dataType: 'json',
            data: {
                step: 'init',
                code_site_id: '{$code_site_id}',
                seal: '{$init_seal}',
            },
            success: function (data) {
                // data is what is outputted by \MoneticoPaiementPayment_sessionModuleFrontController::initContent at step 'init'
                transaction_seal_{$code_site_id} = data['transaction_seal'];
                transaction_date_{$code_site_id} = data['transaction_date'];
                transaction_reference_{$code_site_id} = data['transaction_reference'];
                displayComponent_{$code_site_id}(data['session_id']);
            },
            complete: function () {
                loader.classList.toggle('active');
            }
        });
    }


    document.onready = () => {
        setDomElements();
        {if isset($can_restart) and $can_restart}
        displayComponent_{$code_site_id}('{$session_id_{$code_site_id}}');
        {/if}
    }


    function displayComponent_{$code_site_id}(session_id) {
        paymentComponents_{$code_site_id} = MoneticoPaiement.PaymentComponents();
        paymentComponents_{$code_site_id}.initialize(session_id);

        paymentComponent_{$code_site_id} = paymentComponents_{$code_site_id}.createComponent();
        paymentComponent_{$code_site_id}.generate('component-wrapper-{$code_site_id}');

        let createdComponentParent{$code_site_id} = document.getElementById('component-wrapper-{$code_site_id}');
        let createdComponent{$code_site_id} = document.getElementById('component-wrapper-{$code_site_id}').children[0];

        createdComponent{$code_site_id}.style.display = 'block';
        createdComponentParent{$code_site_id}.style.display = 'block';

        if (document.querySelector('input[data-module-name="{$module_name}-{$code_site_id}"]').checked) {
            hideBtnAndCgv()
        }

        document.querySelectorAll('input[name="payment-option"]').forEach(e => e.addEventListener('click', function (e) {
            if (e.target.dataset.moduleName !== null) {
                if (e.target.dataset.moduleName === '{$module_name}-{$code_site_id}') {
                    createdComponent{$code_site_id}.style.display = 'block';
                    createdComponentParent{$code_site_id}.style.display = 'block';
                    hideBtnAndCgv()
                } else {
                    showBtnAndCgv()
                }
            }
        }))

        paymentComponent_{$code_site_id}.addEventListener('paymentMethodSubmitted', (e) => {
            $.ajax({
                type: "POST",
                url: '{$paymentSessionUrl nofilter}',
                dataType: 'json',
                data: {
                    step: 'transaction',
                    code_site_id: '{$code_site_id}',
                    reference: transaction_reference_{$code_site_id},
                    date: transaction_date_{$code_site_id},
                    seal: transaction_seal_{$code_site_id},
                }
            });
        });
    }
</script>