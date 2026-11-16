<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Update configuration score values
 *
 * @author      zentria
 */
class TC_Controller_getData_for_data_viz extends TC_Controller
{
    /**
     * Main method, call in connector.php
     */
    public function getContent()
    {
        // Check parameters
        if (!Tools::getIsset('date_start') || !Tools::getIsset('date_end')) {
            return $this->displayError('You must specify a date start and end date');
        }
        if (Tools::getValue('date_start') > Tools::getValue('date_end') || Tools::getValue('date_end') < Tools::getValue('date_start')) {
            return $this->displayError('Date range is not valid date start => date end, you need to respect the order');
        }

        $date_start = Tools::getValue('date_start');
        $date_end = Tools::getValue('date_end');

        // Define functions to call generically with their expected keys
        $generationFunctions = [
            'getNumberOfCarts' => 'number_carts',
            'getCartsAmount' => 'carts_amount',
            'getDayRevenue' => 'day_revenue',
            'getNumberOfOrders' => 'number_orders',
            'getNumberOfProductsOrdered' => 'number_products_ordered',
            'getOrdersFromExistingCustomers' => 'orders_from_existing_customers',
            'getOrdersFromNewCustomers' => 'orders_from_new_customers',
            'getOrdersPerCustomer1' => 'orders_per_customer_1',
            'getOrdersPerCustomer2' => 'orders_per_customer_2',
            'getOrdersPerCustomer3' => 'orders_per_customer_3',
            'getOrdersPerCustomer4' => 'orders_per_customer_4',
            'getProductsBestSalesQuantity' => 'products_best_sales_quantity',
            'getProductsBestSalesAmount' => 'products_best_sales_amount',
            'getOrdersPerCarrier' => 'orders_per_carrier',
            'getOrdersPerPaymentMethod' => 'orders_per_payment_method',
        ];

        $datasets = [];
        $expectedKeys = [];

        foreach ($generationFunctions as $functionName => $keyName) {
            $fullFunctionName = 'getWebsiteDataViz_' . $functionName;
            $data = TC_Toolkit_data_viz::$fullFunctionName($date_start, $date_end);
            if (is_array($data)) {
                $datasets[] = $data;
            }
            $expectedKeys[] = $keyName;
        }

        $merged = $this->mergeDataByDate($datasets);
        return $this->fillMissingDates($merged, $date_start, $date_end, $expectedKeys);
    }

    /**
     * Prepare a readable error for return
     *
     * @param string $error_msg
     *
     * @return array all data to get
     */
    public function displayError($error_msg = '')
    {
        return [
            'error' => true,
            'error_msg' => $error_msg,
            'message' => $error_msg,
        ];
    }

    /**
     * Merge multiple data arrays by date/period key
     *
     * @param array $datasets Variable number of datasets to merge
     *
     * @return array Merged data grouped by date
     */
    private function mergeDataByDate($datasets)
    {
        $merged = [];
        $allKeys = [];

        // Loop through all datasets to build merged array and collect all possible keys
        foreach ($datasets as $data) {
            if (!is_array($data)) {
                continue;
            }

            foreach ($data as $row) {
                $key = isset($row['date']) ? $row['date'] : (isset($row['period']) ? $row['period'] : null);

                if ($key === null) {
                    continue;
                }

                // Collect all keys except date/period
                foreach ($row as $rowKey => $value) {
                    if ($rowKey !== 'date' && $rowKey !== 'period') {
                        $allKeys[$rowKey] = true;
                    }
                }

                if (isset($merged[$key])) {
                    $merged[$key] = array_merge($merged[$key], $row);
                } else {
                    $merged[$key] = $row;
                }
            }
        }

        // Fill missing keys with zero value for each date
        foreach ($merged as $key => $row) {
            foreach ($allKeys as $dataKey => $true) {
                if (!isset($row[$dataKey])) {
                    $merged[$key][$dataKey] = 0;
                }
            }
        }

        return $merged;
    }

    /**
     * Fill missing dates in the date range with zero values
     *
     * @param array $merged Merged data array with dates as keys
     * @param string $date_start Start date (Y-m-d format)
     * @param string $date_end End date (Y-m-d format)
     * @param array $expectedKeys List of expected data keys
     *
     * @return array Complete array with all dates filled
     */
    private function fillMissingDates($merged, $date_start, $date_end, $expectedKeys)
    {
        // Collect all data keys from existing entries
        $allKeys = [];
        foreach ($merged as $row) {
            foreach ($row as $rowKey => $value) {
                if ($rowKey !== 'date' && $rowKey !== 'period') {
                    $allKeys[$rowKey] = true;
                }
            }
        }

        // Add expected keys to ensure they're always present
        foreach ($expectedKeys as $key) {
            $allKeys[$key] = true;
        }

        // Fill missing dates in the range
        $current_date = strtotime($date_start);
        $end_date = strtotime($date_end);

        while ($current_date <= $end_date) {
            $date_string = date('Y-m-d', $current_date);

            if (!isset($merged[$date_string])) {
                // Create entry with date and all keys set to 0
                $merged[$date_string] = ['date' => $date_string];
                foreach ($allKeys as $dataKey => $true) {
                    $merged[$date_string][$dataKey] = '0.00';
                }
            } else {
                // Ensure all expected keys exist even for dates with partial data
                foreach ($allKeys as $dataKey => $true) {
                    if (empty($merged[$date_string][$dataKey])) {
                        $merged[$date_string][$dataKey] = '0.00';
                    }
                }
            }

            $current_date = strtotime('+1 day', $current_date);
        }

        // Sort by date to ensure chronological order
        ksort($merged);

        return array_values($merged);
    }
}
