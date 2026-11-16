<style>

  .js-payment-MoneticoPaiement {
    max-width: 360px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    border #24b9d7 4px solid;
    margin-bottom: 20px;
  }

  .js-payment-MoneticoPaiement .row div:has(input) {
    height: 50px;
    padding: 4px;
    font-size: 17px;
  }

  .js-payment-MoneticoPaiement .row #card-exp-date-{$token} #hostedfield {
    text-align: center;
  }

  #MoneticoPaiement-modal-{$token} iframe,
  #MoneticoPaiement-modal-{$token} iframe iframe,
  {
    border: none;
  }

  #MoneticoPaiement-modal-{$token} .modal-content,
  #MoneticoPaiement-modal-{$token} .modal-charging {

    display: flex;
    justify-content: center;

    /*
    width: 455px;
    height: 646px;
    */
  }

  #MoneticoPaiement-modal-{$token} .modal-dialog {
    align-content: center;
    /* the height must be set in .modal-body below */
    width: 800px;
    max-width: 90%;
  }

  #MoneticoPaiement-modal-{$token} .modal-body {
    align-content: center;
    min-width: 100%;
    /* the width must be set in .modal-dialog above */
    height: 800px;
    max-height: 90%;
  }

  .MoneticoPaiement-payment-form-error {
    margin-left: 7px;
    margin-right: 7px;
  }

  #MoneticoPaiement-modal-{$token} #modal-charging,
  #MoneticoPaiement-modal-{$token} .loader {
    display: block;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 9px solid #f39517;
    animation: mp-spinner-animation-1 0.8s infinite linear alternate,
    mp-spinner-animation-2 1.6s infinite linear;
  }

  {if $smarty.const._PS_VERSION_ ge '9.0.0'}
    .monetico-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1040;
    }
  {/if}

  @keyframes mp-spinner-animation-1 {
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

  @keyframes mp-spinner-animation-2 {
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

<div id="MoneticoPaiement-modal-{$token}" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-charging">
          <span class="loader"></span>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="MoneticoPaiement-payment-form-{$token}" class="js-payment-MoneticoPaiement cardform">
  <div class="row">
    <div>
      <div id="card-holder-name-{$token}"></div>
    </div>
  </div>
  <div class="row">
    <div>
      <div id="card-number-{$token}"></div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 left">
      <div id="card-exp-date-{$token}"></div>
    </div>
    <div class="col-sm-6 right">
      <div id="card-cvx-{$token}"></div>
    </div>
  </div>

  <div class="row">
    <div class="MoneticoPaiement-payment-form-error">
      <div class="error alert alert-danger" style="display: none;margin-top: 10px;">
        <span class="single-error" style="display: none"></span>
        <span class="multiple-error"
              style="display: none">{l s="Please check your entries"  mod='MoneticoPaiement'}</span>
      </div>
    </div>
  </div>
</div>

<script>
  // check if payment form is completed
  let MoneticoPaiementPaymentForm{$codeSiteId} = false;

  window.addEventListener("load", function (event) {
    // style
    var style = {
      focusedClass: {
        colorProperty: '#333'
      },
      invalidClass: {
        colorProperty: '#d9360b',
        borderProperty: '2px solid #d9360b',
      },
      emptyClass: {
        colorProperty: 'rgba(63, 58, 58, 0.7)'
      }
    };

    var cardExpStyle = {
      baseClass: {
        textAlignProperty: 'center'
      },
      focusedClass: {
        colorProperty: '#333'
      },
      invalidClass: {
        colorProperty: '#d9360b',
        borderProperty: '2px solid #d9360b',
      },
      emptyClass: {
        colorProperty: 'rgba(63, 58, 58, 0.7)'
      }
    };
    // hostedFields
    var hostedFieldsPayment{$codeSiteId} = MoneticoPaiement.HostedFields();
    if (hostedFieldsPayment{$codeSiteId}.initialize('{$ept}', '{$token}')) {


      var hostedField1 = hostedFieldsPayment{$codeSiteId}.createField('cardNumber', {
        style: style,
        placeholder: ['{l s="Card number"  mod='MoneticoPaiement'}']
      });


      var hostedField2 = hostedFieldsPayment{$codeSiteId}.createField('cardExpDate', {
        style: cardExpStyle,
        placeholder: [
          '{l s="MM"  mod='MoneticoPaiement'}',
          '{l s="YY"  mod='MoneticoPaiement'}'
        ]
      });


      var hostedField3 = hostedFieldsPayment{$codeSiteId}.createField('cardCvx', {
        style: style,
        placeholder: [
          '{l s='Verification Code'  mod='MoneticoPaiement'}'
        ]
      });


      var hostedField4 = hostedFieldsPayment{$codeSiteId}.createField('cardHolderName', {
        style: style,
        placeholder: [
          '{l s='Name of the card holder'  mod='MoneticoPaiement'}'
        ]
      });

      hostedField1.generate('card-number-{$token}');
      hostedField2.generate('card-exp-date-{$token}');
      hostedField3.generate('card-cvx-{$token}');
      hostedField4.generate('card-holder-name-{$token}');

      hostedFieldsPayment{$codeSiteId}.addEventListener('change', function (event) {
        $("#MoneticoPaiement-payment-form-{$token} .error").css('display', 'none');
        MoneticoPaiementPaymentForm{$codeSiteId} = false;
        if (event.detail.complete) {
          MoneticoPaiementPaymentForm{$codeSiteId} = true;
        } else if (event.detail.error) {
          updateErrors{$codeSiteId}(event.detail.error)
        }
      });

      function updateErrors{$codeSiteId}(errors) {
        let current_errors = Object.entries(errors);
        $("#MoneticoPaiement-payment-form-{$token} .single-error").css('display', 'block').html("");
        $("#MoneticoPaiement-payment-form-{$token} .multiple-error").css('display', 'none');
        var first_errors = current_errors.shift();
        if (current_errors.length === 0 || ["expired_request", "invalid_request"].includes(first_errors[1].description)) {
          $("#MoneticoPaiement-payment-form-{$token} .single-error").css('display', 'block');
          $("#MoneticoPaiement-payment-form-{$token} .single-error").html(first_errors[1].message);
        } else {
          $("#MoneticoPaiement-payment-form-{$token} .multiple-error").css('display', 'block');
        }

        $("#MoneticoPaiement-payment-form-{$token} .error").css('display', 'block');
      }
    }
  });

  /* MODAL */
  function initHfPayment_{$codeSiteId}() {
    if (!MoneticoPaiementPaymentForm{$codeSiteId}) {
      return false;
    }

    let payment_service_iframe{$codeSiteId} = document.createElement("IFRAME");
    $("#MoneticoPaiement-modal-{$token} .modal-body .modal-charging").css('display', 'flex');

    // 'nofilter' modifier to the paymentServiceUrl template because it would fuck up ampersands (&),
    // which would render links without url rewrite unusable
    payment_service_iframe{$codeSiteId}.src = "{$paymentServiceUrl nofilter}&token={$token}&codeSiteId={$codeSiteId}&cartId={$cartId}&seal={$seal}";
    payment_service_iframe{$codeSiteId}.setAttribute("sandbox", "allow-forms allow-scripts allow-same-origin allow-top-navigation");
    payment_service_iframe{$codeSiteId}.style = "min-width:100%;min-height:100%;border:none;display:none;";

    payment_service_iframe{$codeSiteId}.onload = function () {
      $("#MoneticoPaiement-modal-{$token} .modal-body .modal-charging").css('display', 'none');
      $("#MoneticoPaiement-modal-{$token} .modal-body iframe").css("display", "block");
    };

    $("#MoneticoPaiement-modal-{$token} .modal-body").append(payment_service_iframe{$codeSiteId});

    {if $smarty.const._PS_VERSION_ lt '9.0.0'}
      $("#MoneticoPaiement-modal-{$token}").modal({
        show: true,
        backdrop: 'static',//pour désactiver la fermeture de la fenetre avec un click hors du cadre
        keyboard: true//pour désactiver la fermeture de la fenetre avec la touche ESC
      });

    {else}
      openMoneticoModal('{$token}');
    {/if}
  }

  {if $smarty.const._PS_VERSION_ ge '9.0.0'}
    function openMoneticoModal(token) {
      var modal = document.getElementById('MoneticoPaiement-modal-' + token)

      if (!modal) {
        return;
      }

      var backdrop = document.createElement('div');
      backdrop.className = 'monetico-backdrop';
      document.body.appendChild(backdrop);

      modal.style.display = 'block';
      modal.classList.add('show');
      modal.removeAttribute('arial-hidden')
      document.body.classList.add('modal-open');

      function onKeyDown(e) {
        if (e.key === "Escape" || e.key === 'Esc') {
          closeModal();
        }
      }

      document.addEventListener('keydown', onKeyDown)

      function closeModal() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');

        if (backdrop) {
          backdrop.remove()
        }

        document.removeEventListener('keydown', onKeyDown);
      }
    }
  {/if}
</script>
