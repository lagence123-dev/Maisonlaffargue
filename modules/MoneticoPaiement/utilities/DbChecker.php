<?php

use MoneticoPaiement\Model\MoneticoPaiementAliasBC;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite3dsv2;
use MoneticoPaiement\Model\MoneticoPaiementCodeSiteOptions;
use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementRefund;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;

trait DbChecker
{
    /**
     * Get all monetico's objects' instanciations for tables to check
     *
     * @return array
     */
    public function getMoneticoModels(): array
    {
        return [
            new MoneticoPaiementAliasBC(),
            new MoneticoPaiementCodeSite(),
            new MoneticoPaiementCodeSite3dsv2(),
            new MoneticoPaiementCodeSiteOptions(),
            new MoneticoPaiementEPT(),
            new MoneticoPaiementRefund(),
            new MoneticoPaiementTransaction(),
        ];
    }

    /**
     * Get mandatory tables and fields for MP module to run
     *
     * @return array
     */
    public function getMoneticoTablesFields(): array
    {
        // get list of type CONSTs
        $reflection = new ReflectionClass('\ObjectModel');
        $type_const = $reflection->getConstants();

        // creation of array [tableName => [tableFields => [fieldsCaracteristics]]]
        $tables_fields = [];

        $models = $this->getMoneticoModels();

        foreach ($models as $model) {
            $model_fields = $model::$definition['fields'];
            $model_primary = $model::$definition['primary'];

            foreach ($model_fields as &$field) {
                $field['type'] = array_search($field['type'], $type_const);
            }

            if (!in_array($model_primary, $model_fields)) {
                $model_fields[$model_primary] = [
                    "type" => "TYPE_INT",
                    "validate" => "isUnsignedInt",
                    "required" => true,
                ];
            }

            $tables_fields[$model::$definition['table']] = $model_fields;
        }

        return $tables_fields;
    }

    /**
     * Get already installed tables and fields on the client's db
     *
     * @return array
     */
    public function getMoneticoInstalledTablesFields(): array
    {
        $models = $this->getMoneticoModels();

        $table_fields = [];

        foreach ($models as $model) {
            $fields = \Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $model::$definition['table'] . "`");

            if ($fields && count($fields) > 0) {
                $table_fields_names = [];

                foreach ($fields as $field) {
                    $table_fields_names[] = $field['Field'];
                }

                $table_fields[$model::$definition['table']] = $table_fields_names;
            }

        }

        return $table_fields;
    }

    /**
     * Compare mandatory fields to already installed fields
     *
     * @return array|bool array of missing fields | false if no tables already installed
     */
    public function compareDbFields()
    {
        // Get already installed table's fields
        $installed_tables_fields = $this->getMoneticoInstalledTablesFields();

        if (empty($installed_tables_fields)) {
            return false;
        }

        $missing_fields = [];

        // Get mandatory tables and fields for MP module to run
        $mandatory_tables_fields = $this->getMoneticoTablesFields();

        foreach ($mandatory_tables_fields as $table => $fields) {
            foreach ($fields as $field => $field_details) {
                // Create array of missing fields with details (type, size, required) and sorted by table
                if (!in_array($field, $installed_tables_fields[$table])) {
                    $missing_fields[$table][$field] = $field_details;
                }
            }
        }

        return $missing_fields;
    }

    /**
     * Add the missing fields to affected tables in the client's db
     *
     * @return bool
     */
    public function dbTreatment(): bool
    {
        $return = true;
        // Compare mandatory fields and already installed fields to get missing fields in the client's db
        $missing_fields = $this->compareDbFields();

        if ($missing_fields && count($missing_fields) > 0) {
            foreach ($missing_fields as $table => $fields) {
                foreach ($fields as $field => $field_details) {
                    $return &= $this->createSqlReq($table, $field, $field_details);
                }
            }
        }

        return $return;
    }

    /**
     * Create SQL request to add a missing field in db
     *
     * @param $table
     * @param $field
     * @param $field_details
     *
     * @return bool
     */
    public function createSqlReq($table, $field, $field_details): bool
    {
        $return = true;
        // Request basis
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD COLUMN IF NOT EXISTS `' . $field . '` ';

        // Add field type
        switch ($field_details['type']) {
            case 'TYPE_INT':
                $sql .= 'INT';
                break;

            case 'TYPE_FLOAT':
                $sql .= 'FLOAT';
                break;

            case 'TYPE_STRING':
                $sql .= 'VARCHAR';
                break;

            case 'TYPE_BOOL':
                $sql .= 'TINYINT(1)';
                break;

            case 'TYPE_DATE':
                $sql .= 'DATETIME';
                break;

            case 'TYPE_HTML':
                $sql .= 'TEXT';
                break;

            default:
                break;
        }

        // Add field size
        if (isset($field_details['size'])) {
            $sql .= '(' . intval($field_details['size']) . ')';
        } else if ($field === 'ept_instalment_term_nb') {
            $sql .= '(1)';
        } else if (str_contains($field, 'ept_instalment_pct')) {
            $sql .= '(2)';
        } else if ($field === 'code_site_payment_method') {
            $sql .= '(64)';
        } else if ($field_details['type'] === 'TYPE_INT') {
            $sql .= '(11)';
        } else if ($field_details['type'] === 'TYPE_STRING') {
            $sql .= '(255)';
        }

        // Add UNSIGNED if necessary
        if ((isset($field_details['validate']) && $field_details['validate'] === 'isUnsignedInt') || $field === 'code_site_amount_min' || $field === 'code_site_amount_max') {
            $sql .= ' UNSIGNED';
        }

        // Add NULL or NOT NULL
        $sql .= isset($field_details['required']) && $field_details['required'] ? ' NOT NULL;' : ' NULL;';

        $return &= Db::getInstance()->execute($sql);

        if (!$return) {
            PrestaShopLogger::addLog(sprintf($this->l('An error occured while adding field "%s" in "%s" table.'), $field, $table), 1);
        }

        return $return;
    }
}
