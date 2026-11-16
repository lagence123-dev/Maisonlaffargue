<?php

use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Api\Stats\Stats;
use MoneticoPaiement\Sdk\Api\Themis\Themis;
use MoneticoPaiement\Sdk\Utilities\Collections\Language;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;

trait Apis
{
    /**
     * @param array{number: string, mac: string} $ept
     *
     * @return false|string False if the request wasn't successful, the response body as a string otherwise.
     */
    public function ApiThemisSend(array $ept, bool $withPlanning = false, bool $rethrowCurlError = false)
    {
        $response = false;

        $module = Module::getInstanceByName(MoneticoPaiement::MODULE_NAME);
        $context = Context::getContext();
        // Format version to match API requirements
        $version = str_replace('.', '-', $module->version);
        // Check BO language
        $mp_langs_class = new ReflectionClass(Language::class);
        $mp_langs = $mp_langs_class->getConstants();
        $bo_lang = strtoupper($context->language->getIsoCode());
        $lang = in_array($bo_lang, array_keys($mp_langs)) ? $bo_lang : 'EN';
        // Themis
        $themis = new Themis($version, 'prestashop');
        $themis->setHmac($ept['mac']);
        $themis->setPointOfSaleId($ept['number']);
        $themis->setEuroInfoCultre($lang);
        $themis->setUrl($withPlanning);
        // API request
        $request = new MoneticoPaiementApi();
        $request->setCurlOpt(CURLOPT_POST, false);
        $request->setCurlOpt(CURLOPT_TIMEOUT, 3);

        try {
            $request->process($themis->getUrl(), [], $themis->getHeaderData(), '', 'GET');
            $response = $request->getResponse();
        } catch (Throwable $e) {
            $log_message = "[" . $module->displayName . "] " . $e->getMessage();
            PrestaShopLogger::addLog($log_message);

            if ($rethrowCurlError && get_class($e) === UnexpectedValueException::class) {
                throw $e; // so that it is picked-up by \MoneticoPaiement\Prestashop\SupportDataFile\MoneticoDataCollector::testApiCommunications
            }
        }

        return $response;
    }

    public function ApiStatSend(string $ept_number, string $code_site, string $mac): bool
    {
        $stats = new Stats();

        $stats->setSiteCode($code_site);
        $stats->setCmsName('PrestaShop');
        $stats->setCmsVersion(_PS_VERSION_);
        $stats->setCmsModuleVersion($this->version);
        $stats->setPhpVersion(Helper::getFormatingVersion(phpversion()));
        $stats->setMerchantUrl(Tools::getHttpHost(true) . __PS_BASE_URI__);
        $stats->setOSVersion(php_uname('s') . ' ' . php_uname('r'));
        // API
        $request = new MoneticoPaiementApi();
        $request->setCurlOpt(CURLOPT_RETURNTRANSFER, false);
        $request->setCurlOpt(CURLOPT_TIMEOUT, 2);

        $hmacComputer = new HmacComputer();
        $eptMac = $mac ?? MoneticoPaiementHelper::getEptMacFromEptNumber($ept_number);
        $seal = $hmacComputer->sealFields($stats->jsonSerialize(), $eptMac);

        // finally, process the request
        return $request->process($stats->getUrl(), $stats->jsonSerialize(), ['Content-Type:application/json'], $ept_number . ':' . $seal);
    }
}
