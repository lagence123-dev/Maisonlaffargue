<?php

declare(strict_types=1);

namespace MoneticoPaiement\Prestashop\SupportDataFile;

use Configuration;
use Db;
use DbQuery;
use Module;
use MoneticoPaiement;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\DataCollectorInterface;
use mysqli;
use PDO;
use PDOStatement;

/**
 * Collect data about Prestashop
 */
final class CMSDataCollector implements DataCollectorInterface
{
    public function collect(): array
    {
        $dbServerInfo = null;
        $pdo = Db::getInstance()->getLink();
        if ($pdo instanceof PDO) {
            $dbServerInfo = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        } else if ($pdo instanceof mysqli) { // I'm not sure when it could be the case, PDO is around since PHP5.1
            $dbServerInfo = $pdo->server_info;
        }

        $options = Configuration::getMultiple([
            'PS_LOCALE_LANGUAGE',
            'PS_LOCALE_COUNTRY',
            'PS_SHOP_DOMAIN', 'PS_SHOP_DOMAIN_SSL',
            'PS_TIMEZONE',
        ]);

        return [
            'cms' => [
                'name' => 'prestashop',
                'version' => _PS_VERSION_,
                'database' => $dbServerInfo, // ie: "10.4.25-MariaDB"
                'site_url' => $options['PS_SHOP_DOMAIN_SSL'] . __PS_BASE_URI__,
                'config' => $options,
                'file_creation_date' => Helper::getFileCreationDate(_PS_ROOT_DIR_ . '/docs/CHANGELOG.txt'),
                'file_modification_date' => Helper::getFileModifDate(_PS_ROOT_DIR_ . '/docs/CHANGELOG.txt'),
                'database_logs' => $this->collectDatabaseLogs(),
                'modules' => $this->getInstalledModulesData(),
            ],
        ];
    }

    /**
     * @return array<string, array<string, string|array<string>> Keys are the module names
     */
    private function getInstalledModulesData(): array
    {
        $modules = Module::getModulesInstalled();
        // Note that by default Prestashop 8.2 comes with 60+ preinstalled and active modules.

        $returnedModuleData = [];

        /** @var array{id_module: string, name: string, active: string, version: string} $module */
        foreach ($modules as $moduleAsArray) {
            $moduleName = $moduleAsArray['name'];
            if ($moduleName === MoneticoPaiement::MODULE_NAME) {
                continue;
            }

            $module = Module::getInstanceByName($moduleName);
            if (!($module instanceof Module) ) {
                continue;
            }

            if ($module->author === 'PrestaShop') {
                continue;
            }

            $hooks = [];
            $possibleHooks = $module->getPossibleHooksList();
            foreach ($possibleHooks as $possibleHook) {
                if ($possibleHook['registered']) {
                    $hooks[] = $possibleHook['name'];
                }
            }

            $moduleFolderPath = _PS_ROOT_DIR_ . "/modules/$moduleName";
            $moduleMainFilePath = "$moduleFolderPath/$moduleName.php";

            $overrides = glob($moduleFolderPath . '/override/**/*.php');
            foreach ($overrides ?? [] as $i => $overridePath) {
                // just keep the relative path from the override folder
                $overrides[$i] = str_replace($moduleFolderPath . '/override/', '', $overridePath);
            }

            $returnedModuleData[$moduleName] = [
                'version' => $module->version,
                'author' => $module->author,
                'is_active' => (string) (int) $module->active,
                'overrides' => $overrides,
                'hooks' => $hooks,
                'file_creation_date' => Helper::getFileCreationDate($moduleMainFilePath),
                'file_modification_date' => Helper::getFileModifDate($moduleMainFilePath),
            ];
        }

        return $returnedModuleData;
    }

    /**
     * Does a raw extract of the ps_log table.
     *
     * @return array<array<string, string>>
     */
    private function collectDatabaseLogs(): array
    {
        // Note that we could have used the more modern \PrestaShopBundle\Entity\Repository\LogRepository
        // But I just couldn't find out how to properly inject it or build its instance myself

        $query = (new DbQuery())
            ->select('*')
            ->from('log')
            ->where("`message` like '%monetico%'")
            ->orderBy('id_log DESC')
            ->limit(500);

        $result = Db::getInstance()->query($query);
        assert($result instanceof PDOStatement);

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
