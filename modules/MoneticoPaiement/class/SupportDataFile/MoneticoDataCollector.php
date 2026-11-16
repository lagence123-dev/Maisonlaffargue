<?php

declare(strict_types=1);

namespace MoneticoPaiement\Prestashop\SupportDataFile;

use Db;
use DbQuery;
use Module;
use MoneticoPaiement;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Api\PaymentService\PaymentService;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\DataCollectorInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Throwable;

/**
 * Collect data about our Monetico module
 */
final class MoneticoDataCollector implements DataCollectorInterface
{
    private MoneticoPaiement $module;
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->module = Module::getInstanceByName(MoneticoPaiement::MODULE_NAME);
        $this->configuration = $configuration;
    }

    public function collect(): array
    {
        $module = $this->module;
        $mainFileAbsolutePath = _PS_ROOT_DIR_ . "/modules/{$module->name}/{$module->name}.php";

        return [
            'monetico' => [
                'version' => $module->version,
                'version_history' => $module->getVersionHistory(),
                'file_creation_date' => Helper::getFileCreationDate($mainFileAbsolutePath),
                'file_modification_date' => Helper::getFileModifDate($mainFileAbsolutePath),
                'api_connections' => $this->testApiCommunications(),
                'setup' => $this->getSetup(),
                'last_transactions' => $this->getLastTransactions(),
                'settings_audit_logs' => $module->getSetupAuditLogs(),
            ],
        ];
    }

    /**
     * @return array{themis: bool, stats: bool, payment-api: bool}
     */
    public function testApiCommunications(): array
    {
        $demoEptData = $this->module->getDemoData()['IMMEDIATE'];

        $tests = [
            'themis' => function () use ($demoEptData): bool {
                return (bool)$this->module->ApiThemisSend(['number' => $demoEptData['ept_number'], 'mac' => $demoEptData['ept_mac']], false, true);
            },
            'stats' => function () use ($demoEptData): bool {
                return $this->module->ApiStatSend($demoEptData['ept_number'], $demoEptData['code_site'], $demoEptData['ept_mac']);
            },
            'payment-api' => function (): bool {
                $url = (new PaymentService())->getUrl(); // https://payment-api.e-i.com/paymentservice.cgi

                $httpClient = new MoneticoPaiementApi();
                $httpClient->setCurlOpt(CURLOPT_POST, false); // GET
                $httpClient->setCurlOpt(CURLOPT_TIMEOUT, 2); // seconds

                return $httpClient->process($url, [], []);
            },
        ];

        foreach ($tests as $api => $testCallable) {
            try {
                $success = $testCallable();
                $tests[$api] = $success ? 'OK' : 'ERROR: (error unknown)';
            } catch (\UnexpectedValueException $e) { // thrown by \MoneticoPaiement\Sdk\Api\MoneticoPaiementApi::execute
                $tests[$api] = 'ERROR: ' . $e->getMessage(); // contain the curl error number and message
            } catch (Throwable $e) {
                $tests[$api] = 'ERROR: ' . get_class($e) . ': ' . $e->getMessage();
            }
        }

        return $tests;
    }

    /**
     * Unlike with Wordpress, and with the exception for the demo mode toggles, the setup for the Prestashop module is in the EPT and Codesite tables.
     * So this method basically returns a dump of these tables.
     *
     * @return array<string, mixed>
     */
    private function getSetup(): array
    {
        /** @var array{id: int, number: string, mac: string, payment_mode: string} $epts */
        $epts = MoneticoPaiementHelper::getAllEpt();
        $codesites = MoneticoPaiementHelper::getAllCodeSites(true);

        foreach ($epts as &$ept) { // /!\ REFERENCE
            $ept['codesites'] = [];

            /** @var array{code_site_ept: string} $codeSite */
            foreach ($codesites as $codesite) {
                if ($codesite['code_site_ept'] === $ept['number']) {
                    $ept['codesites'][] = $codesite;
                }
            }
        }
        unset($ept);

        return [
            'demo_mode_active' => $this->module->mode_demo_active(),
            'demo_mode_create_order' => (bool)$this->configuration->get('MP_DEMO_MODE_ORDERS'),
            'epts' => $epts, // with nested codesites, with nested options
        ];
    }

    /**
     * @return array<array<string, string>>
     */
    private function getLastTransactions(): array
    {
        $sql = new DbQuery();
        $sql->select(<<<SQL
        o.id_order, o.id_cart, o.reference, o.total_paid_tax_incl, o.total_paid_tax_excl, o.date_add,
        c.iso_code as currency,
        t.sandbox_transaction, t.order_context,
        sl.name as status_name
        SQL
        );
        $sql->from('orders', 'o');

        $sql->innerJoin('currency', 'c', 'o.id_currency = c.id_currency');
        $sql->innerJoin('order_state_lang', 'sl', 'o.current_state = sl.id_order_state');

        $sql->innerJoin('monetico_transaction', 't', 't.cart_id = o.id_cart');
        $sql->innerJoin('monetico_code_site', 'cs', 'cs.id_code_site = t.code_site_id');
        $sql->innerJoin('monetico_ept', 'e', 'cs.code_site_ept_id = e.id_ept');

        $demoEptNumber = $this->module->getDemoEptNumbers();
        $sql->where('e.ept_number NOT IN (' . pSQL($demoEptNumber) . ')');

        $sql->orderBy('o.id_order DESC');
        $sql->limit(5);

        $dbOrders = Db::getInstance()->executeS($sql);

        $exportedOrders = [];
        /** @var array<string, string> $order */
        foreach ($dbOrders as $order) {
            $orderContext = json_decode($order['order_context'] ?? '[]', true);

            $exportedOrders[] = [
                'ps_id_order' => $order['id_order'],
                'ps_reference' => $order['reference'],
                'ps_id_cart' => $order['id_cart'],
                'total_ttc' => $order['total_paid_tax_incl'],
                'total_ht' => $order['total_paid_tax_excl'],
                'currency' => $order['currency'],
                'status' => $order['status_name'],
                'date_created' => $order['date_add'],

                // Monetico specific stuffs, mostly from the order context
                'monetico_ref' => $orderContext['reference'],
                'is_sandbox' => (bool)$order['sandbox_transaction'],
                'tpe' => $orderContext['TPE'],
                'code_societe' => $orderContext['societe'],
                '3ds' => $orderContext['ThreeDSecureChallenge'] ?? null,
                'mac' => MoneticoPaiementHelper::displayMacSecurity($orderContext['MAC'] ?? ''),
                'aliascb' => isset($orderContext['aliascb']) ? substr($orderContext['aliascb'], 0, 6) . '...' : null,
                'payment_date' => $orderContext['date'] ?? null, // in "d/m/Y:H:i:s" format (ie: "23/06/2025:10:49:45")
                'texte_libre' => $orderContext['texte-libre'] ?? null,
                // other fields in the order context are either redundant with the order data, or contain PII
            ];
        }

        return $exportedOrders;
    }
}
