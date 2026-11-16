<?php
// Utilities > Collections
require 'Utilities/Collections/ACSResponseValue.php';
require 'Utilities/Collections/Currency.php';
require 'Utilities/Collections/Language.php';
require 'Utilities/Collections/PaymentAdditionalNetwork.php';
require 'Utilities/Collections/PaymentCaptureResponseValue.php';
require 'Utilities/Collections/PaymentDisplayMethod.php';
require 'Utilities/Collections/PaymentEnvironment.php';
require 'Utilities/Collections/PaymentMeans.php';
require 'Utilities/Collections/PaymentMode.php';
require 'Utilities/Collections/PaymentProtocol.php';
require 'Utilities/Collections/PaymentServiceResponseValue.php';
require 'Utilities/Collections/PaymentStateResponseValue.php';
require 'Utilities/Collections/RefundResponseValue.php';
require 'Utilities/Collections/ThreeDSecureChallengeValue.php';
// Utilities > Models
require 'Utilities/Interfaces/MoneticoRequestInterface.php';
// Utilities > Models
require 'Utilities/Models/Order/Customer.php';
require 'Utilities/Models/OrderContext/Address.php';
require 'Utilities/Models/OrderContext/Billing.php';
require 'Utilities/Models/OrderContext/Browser.php';
require 'Utilities/Models/OrderContext/Client.php';
require 'Utilities/Models/OrderContext/Shipping.php';
require 'Utilities/Models/OrderContext/ShoppingCart.php';
require 'Utilities/Models/OrderContext/ShoppingCartItems.php';
require 'Utilities/Models/Payment/Amount.php';
require 'Utilities/Models/MerchantConfiguration.php';
require 'Utilities/Models/OrderContext.php';
require 'Utilities/Models/OrderModel.php';
// Utilities > Traits
require 'Utilities/Traits/LogoWithAdditionalCards.php';
require 'Utilities/Traits/WhiteList.php';
// Utilities
require 'Utilities/Helper.php';
require 'Utilities/HmacComputer.php';
require 'Utilities/SupportDataFile/DataCollectorInterface.php';
require 'Utilities/SupportDataFile/FileCollectorInterface.php';
require 'Utilities/SupportDataFile/SupportDataFileException.php';
require 'Utilities/SupportDataFile/LogFileCollector.php';
require 'Utilities/SupportDataFile/TechDataCollector.php';
require 'Utilities/SupportDataFile/MainInfoFileCollector.php';
require 'Utilities/SupportDataFile/PHPInfoFileCollector.php';
require 'Utilities/SupportDataFile/ZipFileBuilder.php';
// API > HostedFields
require 'Api/HostedFields/PaymentMeanTokenGenerator/Response.php';
require 'Api/HostedFields/PaymentMeanTokenGenerator/Context.php';
require 'Api/HostedFields/PaymentMeanTokenGenerator.php';
// API > Payment
require 'Api/Payment/MPRequestAbstract.php';
require 'Api/Payment/Capture.php';
require 'Api/Payment/Refund.php';
require 'Api/Payment/State.php';
// API > Payment Service
require 'Api/PaymentService/Payment/PaymentOptions/MerchantIdentification.php';
require 'Api/PaymentService/Payment/InstalmentPayment.php';
require 'Api/PaymentService/Payment/PaymentMean.php';
require 'Api/PaymentService/Payment/PaymentOptions.php';
require 'Api/PaymentService/Payment/PreauthorisationPayment.php';
require 'Api/PaymentService/Authentication/Details.php';
require 'Api/PaymentService/Authentication.php';
require 'Api/PaymentService/MerchantConfiguration.php';
require 'Api/PaymentService/Order.php';
require 'Api/PaymentService/Payment.php';
require 'Api/PaymentService/PaymentService.php';
// API > PaymentSession
require 'Api/PaymentSession/Beneficiary.php';
require 'Api/PaymentSession/ErgonomySettings.php';
require 'Api/PaymentSession/MerchantConfiguration.php';
require 'Api/PaymentSession/Order.php';
require 'Api/PaymentSession/Payer.php';
require 'Api/PaymentSession/PaymentMean.php';
require 'Api/PaymentSession/PaymentSessionInitiation.php';
// API > PaymentSession/Kyc
require 'Api/PaymentSession/Kyc/Kyc.php';
require 'Api/PaymentSession/Kyc/Individual.php';
require 'Api/PaymentSession/Kyc/Professional.php';
// API > Stats
require 'Api/Stats/Stats.php';
// API > Themis
require 'Api/Themis/Themis.php';
// API >
require 'Api/MoneticoPaiementApi.php';
// Payment Page
require 'PaymentPage/PaymentRequest.php';
require 'PaymentPage/DifferedPaymentRequest.php';
require 'PaymentPage/ImmediatePaymentRequest.php';
require 'PaymentPage/InstalmentPaymentRequest.php';
require 'PaymentPage/PartialPaymentRequest.php';
require 'PaymentPage/IpaPaymentRequest.php';
// Response
require 'Response/PaymentResponse.php';
require 'Response/PaymentSessionResponse.php';
require 'Response/PaymentNotification.php';
// Response > PaymentService
require 'Response/PaymentServiceResponse.php';
require 'Response/PaymentService/Data.php';
require 'Response/PaymentService/NextStep.php';
// Response > PaymentSession
require 'Response/PaymentSession/Data.php';
require 'Response/PaymentSession/Error.php';
require 'Response/PaymentSession/Event.php';
require 'Response/PaymentSession/NextStep.php';


// --------------------------------------------------
// Actual autoloader
// Most require above should now be useless.

// copied from https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
spl_autoload_register(function (string $fqcn): void {
    // project-specific namespace prefix
    $prefix = 'MoneticoPaiement\\Sdk\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $fqcn, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($fqcn, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});