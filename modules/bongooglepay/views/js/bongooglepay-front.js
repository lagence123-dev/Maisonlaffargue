/*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
$(document).ready(function () {
  let pageName = prestashop.page.page_name;
  if (pageName == 'product' || pageName == 'checkout') {
    bonGooglePay();
    prestashop.on('updatedProduct', function () {
      bonGooglePay();
    });
  }
});

function bonGooglePay() {
  let buttonWrapper = document.getElementById('bon-google-checkout'),
    successButton = $('.bongoogle-success-button'),
    totalPrice = '',
    billingAddressRequired = '',
    pageName = prestashop.page.page_name,
    paymentsClient = null,
    currency = '';
  if (pageName == 'product') {
    totalPrice = $('#price_bongooglepay').attr('data-price');
    currency = $('#currency_bongooglepay').attr('data-currency');
  } else if (pageName == 'checkout') {
    totalPrice = total_checkout.toString();
    currency = currency_checkout;
  }
  if (bonshipping_billing_enable == 1 && pageName == 'product') {
    billingAddressRequired = true;
  } else {
    billingAddressRequired = false;
  }

  const baseRequest = {
    apiVersion: 2,
    apiVersionMinor: 0
  };

  function getPaymentParams() {
    let params = {};
    if (bon_payment_provider == 'stripe') {
      params = {
        gateway: bon_payment_provider,
        'stripe:version': '2020-08-27',
        'stripe:publishableKey': bon_provider_id
      };
    } else {
      params = {
        gateway: bon_payment_provider,
        gatewayMerchantId: bon_provider_id
      };
    }

    return params;
  }

  const tokenizationSpecification = {
    type: 'PAYMENT_GATEWAY',
    parameters: getPaymentParams()
  };

  const baseCardPaymentMethod = {
    type: 'CARD',
    parameters: {
      allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
      allowedCardNetworks: [
        'AMEX',
        'DISCOVER',
        'INTERAC',
        'JCB',
        'MASTERCARD',
        'VISA'
      ],
      billingAddressRequired: billingAddressRequired,
      billingAddressParameters: {
        format: 'FULL',
        phoneNumberRequired: true
      }
    }
  };

  const cardPaymentMethod = Object.assign(
    {
      tokenizationSpecification: tokenizationSpecification
    },
    baseCardPaymentMethod
  );

  const isReadyToPayRequest = Object.assign({}, baseRequest);
  isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];
  onGooglePayLoaded();

  function getGooglePaymentsClient() {
    if (paymentsClient === null) {
      if (
        bon_shipping_address_enable == 1 &&
        pageName == 'product' &&
        bonshipping_delivery_enable == 1
      ) {
        paymentsClient = new google.payments.api.PaymentsClient({
          environment: environment, //TEST or PRODUCTION
          merchantName: bon_merchant_name,
          merchantId: bon_google_id,
          paymentDataCallbacks: {
            onPaymentAuthorized: onPaymentAuthorized,
            onPaymentDataChanged: onPaymentDataChanged
          }
        });
      } else {
        paymentsClient = new google.payments.api.PaymentsClient({
          environment: environment, //TEST or PRODUCTION
          merchantName: bon_merchant_name,
          merchantId: bon_google_id,
          paymentDataCallbacks: {
            onPaymentAuthorized: onPaymentAuthorized
          }
        });
      }
    }
    return paymentsClient;
  }

  function onGooglePayLoaded() {
    const paymentsClient = getGooglePaymentsClient();
    paymentsClient
      .isReadyToPay(isReadyToPayRequest)
      .then(function (response) {
        if (response.result) {
          addGooglePayButton();
        }
      })
      .catch(function (err) {
        console.error(err);
      });
  }
  function onPaymentAuthorized(paymentData) {
    return new Promise(function (resolve, reject) {
      processPayment(paymentData)
        .then(function () {
          resolve({
            transactionState: 'SUCCESS'
          });
        })
        .catch(function () {
          resolve({
            transactionState: 'ERROR',
            error: {
              intent: 'PAYMENT_AUTHORIZATION',
              message: 'Insufficient funds',
              reason: 'PAYMENT_DATA_INVALID'
            }
          });
        });
    });
  }

  function onPaymentDataChanged(intermediatePaymentData) {
    return new Promise(function (resolve, reject) {
      let shippingAddress = intermediatePaymentData.shippingAddress;
      let shippingOptionData = intermediatePaymentData.shippingOptionData;
      let paymentDataRequestUpdate = {};
      if (
        intermediatePaymentData.callbackTrigger == 'INITIALIZE' ||
        intermediatePaymentData.callbackTrigger == 'SHIPPING_ADDRESS'
      ) {
        if (shippingAddress && shippingAddress.administrativeArea == 'NJ') {
          paymentDataRequestUpdate.error = getGoogleUnserviceableAddressError();
        } else if (bonshipping_delivery_enable == 1 && pageName == 'product') {
          paymentDataRequestUpdate.newShippingOptionParameters =
            getGoogleDefaultShippingOptions();
          let selectedShippingOptionId =
            paymentDataRequestUpdate.newShippingOptionParameters
              .defaultSelectedOptionId;
          paymentDataRequestUpdate.newTransactionInfo =
            calculateNewTransactionInfo(selectedShippingOptionId);
        }
      } else if (intermediatePaymentData.callbackTrigger == 'SHIPPING_OPTION') {
        paymentDataRequestUpdate.newTransactionInfo =
          calculateNewTransactionInfo(shippingOptionData.id);
      }
      resolve(paymentDataRequestUpdate);
    });
  }

  function getGoogleShippingAddressParameters() {
    let countries = JSON.parse(countrie_iso);
    return {
      allowedCountryCodes: countries,
      phoneNumberRequired: true
    };
  }

  function getGoogleUnserviceableAddressError() {
    return {
      reason: 'SHIPPING_ADDRESS_UNSERVICEABLE',
      message: 'Cannot ship to the selected address',
      intent: 'SHIPPING_ADDRESS'
    };
  }

  function getGoogleDefaultShippingOptions() {
    return {
      defaultSelectedOptionId: 'shipping-001',
      shippingOptions: getShippingOption()
    };
  }

  function getTransactionInfo() {
    let transactionInfo = {
      totalPriceStatus: 'FINAL',
      totalPrice: totalPrice,
      currencyCode: currency
    };
    return transactionInfo;
  }

  function getShippingOption() {
    let shippingOptions = [];
    if (bon_google_delivery_first != 'undefined') {
      shippingOptions.push({
        id: 'shipping-001',
        label: bon_google_delivery_first
      });
    }
    if (bon_google_delivery_second != 'undefined') {
      shippingOptions.push({
        id: 'shipping-002',
        label: bon_google_delivery_second
      });
    }
    return shippingOptions;
  }

  function calculateNewTransactionInfo(shippingOptionId) {
    let newTransactionInfo = getGoogleTransactionInfo();
    let shippingCost = getShippingCosts()[shippingOptionId];
    newTransactionInfo.displayItems.push({
      type: 'LINE_ITEM',
      label: 'Shipping cost',
      price: shippingCost,
      status: 'FINAL'
    });
    let totalPrice = 0.0;
    newTransactionInfo.displayItems.forEach(
      (displayItem) => (totalPrice += parseFloat(displayItem.price))
    );
    newTransactionInfo.totalPrice = totalPrice.toString();
    return newTransactionInfo;
  }

  function getGoogleTransactionInfo() {
    return {
      displayItems: [
        {
          label: 'Subtotal',
          type: 'SUBTOTAL',
          price: totalPrice
        }
      ],
      currencyCode: currency,
      totalPriceStatus: 'FINAL',
      totalPriceLabel: 'Total'
    };
  }

  function getShippingCosts() {
    return {
      'shipping-001': '0.00',
      'shipping-002': '10'
    };
  }

  //create payment request data
  function getGooglePaymentDataRequest() {
    const paymentDataRequest = Object.assign({}, baseRequest);
    paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
    paymentDataRequest.transactionInfo = getTransactionInfo();
    paymentDataRequest.merchantInfo = {
      merchantId: bon_google_id,
      merchantName: bon_merchant_name
    };
    paymentDataRequest.emailRequired = true;
    if (
      bon_shipping_address_enable == 1 &&
      bonshipping_delivery_enable == 1 &&
      pageName == 'product'
    ) {
      paymentDataRequest.callbackIntents = [
        'SHIPPING_ADDRESS',
        'SHIPPING_OPTION',
        'PAYMENT_AUTHORIZATION'
      ];
      paymentDataRequest.shippingAddressRequired = true;
      paymentDataRequest.shippingAddressParameters =
        getGoogleShippingAddressParameters();
      paymentDataRequest.shippingOptionRequired = true;
    } else if (
      bon_shipping_address_enable == 1 &&
      bonshipping_delivery_enable == 0 &&
      pageName == 'product'
    ) {
      paymentDataRequest.callbackIntents = [
        'SHIPPING_ADDRESS',
        'PAYMENT_AUTHORIZATION'
      ];
      paymentDataRequest.shippingAddressRequired = true;
      paymentDataRequest.shippingAddressParameters =
        getGoogleShippingAddressParameters();
    } else {
      paymentDataRequest.callbackIntents = ['PAYMENT_AUTHORIZATION'];
    }
    return paymentDataRequest;
  }

  function checkedSubscription() {
    const confirmButton = $('#payment-confirmation button');
    const googleButton = $('.bon-googlepay-checkout .gpay-card-info-container');
    const messegConfirm = $('.bon-googlepay-required');
    if (confirmButton.prop('disabled')) {
      googleButton.prop('disabled', 'true');
      messegConfirm.addClass('active');
    } else {
      googleButton.removeAttr('disabled');
      messegConfirm.removeClass('active');
    }
  }

  // create button
  function addGooglePayButton() {
    let paymentDataRequest = getGooglePaymentDataRequest();
    const paymentsClient = getGooglePaymentsClient();
    const button = paymentsClient.createButton({
      onClick: () => {
        paymentsClient.loadPaymentData(paymentDataRequest);
      },
      buttonColor: bon_button_color
    });
    if (buttonWrapper) {
      buttonWrapper.appendChild(button);
      $(document).on('change', checkedSubscription);
    }
  }

  // process payment
  function processPayment(paymentData) {
    return new Promise(function (resolve, reject) {
      setTimeout(function () {
        let paymentDescription = paymentData.paymentMethodData.description,
          paymentToken = paymentData.paymentMethodData.tokenizationData.token,
          gpay_token = '',
          payerName = null,
          phoneNumber = null,
          shippingAddress = null,
          senderPostalCode = null,
          senderCity = null,
          senderCountryCode = null,
          data = '',
          payerEmail = paymentData.email;
        if (
          bon_shipping_address_enable == 1 &&
          bonshipping_delivery_enable == 1 &&
          pageName == 'product'
        ) {
          price = getShippingCosts()[paymentData.shippingOptionData.id];
          totalPrice = parseFloat(totalPrice) + parseFloat(price);
        }
        if (bon_shipping_address_enable == 1 && pageName == 'product') {
          phoneNumber = paymentData.shippingAddress.phoneNumber;
          shippingAddress = `${paymentData.shippingAddress.address1} ${paymentData.shippingAddress.address2} ${paymentData.shippingAddress.address3}`;
          senderPostalCode = paymentData.shippingAddress.postalCode;
          payerName = paymentData.shippingAddress.name;
          senderCountryCode = paymentData.shippingAddress.countryCode;
          senderCity = paymentData.shippingAddress.locality;
        }
        if (bon_payment_provider == 'liqpay') {
          // gpay_token = btoa(paymentToken);
          gpay_token = paymentToken;
        } else if (bon_payment_provider == 'adyen') {
          gpay_token = paymentData.paymentMethodData.tokenizationData.token;
        } else if (bon_payment_provider == 'checkoutltd') {
          gpay_token = JSON.parse(
            paymentData.paymentMethodData.tokenizationData.token
          );
        } else if (bon_payment_provider == 'stripe') {
          let gpay_token_id = JSON.parse(
            paymentData.paymentMethodData.tokenizationData.token
          );
          gpay_token = gpay_token_id.id;
        }

        if (pageName == 'product') {
          let product_id = $('#product_page_product_id').val();
          data = {
            gpay_token,
            paymentDescription,
            totalPrice,
            currency,
            payerEmail,
            payerName,
            phoneNumber,
            shippingAddress,
            pageName,
            senderPostalCode,
            senderCity,
            product_name,
            product_id,
            senderCountryCode
          };
        } else if (pageName == 'checkout') {
          data = {
            gpay_token,
            pageName,
            paymentDescription
          };
        }
        $('.bonloader-checkout').css('display', 'block');

        $.ajax({
          type: 'POST',
          url: mod_dir + 'bongooglepay/controllers/front/redirect.php',
          data: JSON.stringify(data),
          dataType: 'json',
          contentType: 'application/json; charset=utf-8',
          success: function (msg) {
            if (pageName == 'product') {
              //show popup
              if (msg.success == 1) {
                if (bongoogle_create_order == 1) {
                  document.location.href = msg.redirect_url;
                } else {
                  $('.bongoogle-success-modal h2').html(msg.alert);
                  $('.bongoogle-success-modal h5').html(msg.alertDescription);
                  $('.bongoogle-success-icon').removeClass('error');
                  successButton.click();
                }

              } else {
                $('.bongoogle-success-modal h2').html(msg.error);
                $('.bongoogle-success-icon').addClass('error');
                successButton.click();
              }
            } else if (pageName == 'checkout') {
              //redirect
              if (msg.success == 1) {
                $('.bongoogle-success-modal h2').html(msg.alert);
                $('.bongoogle-success-modal h5').html(msg.alertDescription);
                $('.bongoogle-success-icon').removeClass('error');
              } else {
                $('.bongoogle-success-modal h2').html(msg.error);
                $('.bongoogle-success-icon').addClass('error');
              }
              successButton.click();
              document.location.href = msg.redirect_url;
            }
          }
        });
        resolve({});
      }, 300);
    });
  }
}
