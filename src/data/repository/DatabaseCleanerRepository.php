<?php
/**
 * This file is part of the performancelite package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformanceLite\data\repository;

use DbQuery;
use Module;
use PrestaShop\Module\PerformanceLite\data\util\Connection;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteInvalidDateException;
use PrestaShopDatabaseException;

class DatabaseCleanerRepository extends Connection
{
    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanConfigurationDoublets(): int
    {
        try {
            $sql = (new DbQuery())
                ->select('*')
                ->from('configuration');
            $rows = $this->getConnection()->executeS($sql);

            $config = [];
            $result = 0;
            foreach ($rows as $row) {
                $key = $row['id_shop_group'] . '-|-' . $row['id_shop'] . '-|-' . $row['name'];

                if (\in_array($key, $config, true)) {
                    $this->getConnection()->delete('configuration', '`id_configuration` = ' . (int) $row['id_configuration']);
                    $result += $this->getConnection()->Affected_Rows();
                } else {
                    $config[] = $key;
                }
            }

            return $result;
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }

    public function cleanConfigurationLang(): int
    {
        $this->getConnection()->delete('configuration_lang', '`id_configuration` NOT IN (SELECT `id_configuration` FROM `' . _DB_PREFIX_ . 'configuration`)
            OR `id_configuration` IN (SELECT `id_configuration` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` IS NULL OR `name` = "")');

        $result = 0;
        $result += $this->getConnection()->Affected_Rows();
        foreach ($this->bubble($this->getCheckAndFixQueries()) as $query) {
            if (isset($query[4]) && !Module::isInstalled($query[4])) {
                continue;
            }

            $this->getConnection()->delete(bqSQL($query[0]), '`' . bqSQL($query[1]) . '` NOT IN (SELECT `' . bqSQL($query[3]) . '` FROM `' . _DB_PREFIX_ . bqSQL($query[2]) . '`)');
            $result += $this->getConnection()->Affected_Rows();
        }

        return $result;
    }

    private function bubble(array $array): array
    {
        $sorted = false;
        $size = \count($array);
        while (!$sorted) {
            $sorted = true;
            for ($i = 0; $i < $size - 1; ++$i) {
                for ($j = $i + 1; $j < $size; ++$j) {
                    if ($array[$i][2] === $array[$j][0]) {
                        $tmp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $tmp;
                        $sorted = false;
                    }
                }
            }
        }

        return $array;
    }

    private function getCheckAndFixQueries(): array
    {
        return [
            ['access', 'id_profile', 'profile', 'id_profile'],
            ['accessory', 'id_product_1', 'product', 'id_product'],
            ['accessory', 'id_product_2', 'product', 'id_product'],
            ['address_format', 'id_country', 'country', 'id_country'],
            ['attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'],
            ['carrier_group', 'id_carrier', 'carrier', 'id_carrier'],
            ['carrier_group', 'id_group', 'group', 'id_group'],
            ['carrier_zone', 'id_carrier', 'carrier', 'id_carrier'],
            ['carrier_zone', 'id_zone', 'zone', 'id_zone'],
            ['cart_cart_rule', 'id_cart', 'cart', 'id_cart'],
            ['cart_product', 'id_cart', 'cart', 'id_cart'],
            ['cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'],
            ['cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_country', 'id_country', 'country', 'id_country'],
            ['cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_group', 'id_group', 'group', 'id_group'],
            ['cart_rule_lang', 'id_cart_rule', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_lang', 'id_lang', 'lang', 'id_lang'],
            ['cart_rule_product_rule', 'id_product_rule_group', 'cart_rule_product_rule_group', 'id_product_rule_group'],
            ['cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'],
            ['cart_rule_product_rule_value', 'id_product_rule', 'cart_rule_product_rule', 'id_product_rule'],
            ['category_group', 'id_category', 'category', 'id_category'],
            ['category_group', 'id_group', 'group', 'id_group'],
            ['category_product', 'id_category', 'category', 'id_category'],
            ['category_product', 'id_product', 'product', 'id_product'],
            ['cms', 'id_cms_category', 'cms_category', 'id_cms_category'],
            ['cms_block', 'id_cms_category', 'cms_category', 'id_cms_category', 'blockcms'],
            ['cms_block_page', 'id_cms', 'cms', 'id_cms', 'blockcms'],
            ['cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block', 'blockcms'],
            ['connections', 'id_shop', 'shop', 'id_shop'],
            ['connections', 'id_shop_group', 'shop_group', 'id_shop_group'],
            ['connections_page', 'id_connections', 'connections', 'id_connections'],
            ['connections_page', 'id_page', 'page', 'id_page'],
            ['connections_source', 'id_connections', 'connections', 'id_connections'],
            ['customer', 'id_shop', 'shop', 'id_shop'],
            ['customer', 'id_shop_group', 'shop_group', 'id_shop_group'],
            ['customer_group', 'id_customer', 'customer', 'id_customer'],
            ['customer_group', 'id_group', 'group', 'id_group'],
            ['customer_message', 'id_customer_thread', 'customer_thread', 'id_customer_thread'],
            ['customer_thread', 'id_shop', 'shop', 'id_shop'],
            ['customization', 'id_cart', 'cart', 'id_cart'],
            ['customization_field', 'id_product', 'product', 'id_product'],
            ['customized_data', 'id_customization', 'customization', 'id_customization'],
            ['delivery', 'id_carrier', 'carrier', 'id_carrier'],
            ['delivery', 'id_shop', 'shop', 'id_shop'],
            ['delivery', 'id_shop_group', 'shop_group', 'id_shop_group'],
            ['delivery', 'id_zone', 'zone', 'id_zone'],
            ['feature_product', 'id_feature', 'feature', 'id_feature'],
            ['feature_product', 'id_product', 'product', 'id_product'],
            ['feature_value', 'id_feature', 'feature', 'id_feature'],
            ['group_reduction', 'id_category', 'category', 'id_category'],
            ['group_reduction', 'id_group', 'group', 'id_group'],
            ['homeslider', 'id_homeslider_slides', 'homeslider_slides', 'id_homeslider_slides', 'homeslider'],
            ['homeslider', 'id_shop', 'shop', 'id_shop', 'homeslider'],
            ['hook_module', 'id_hook', 'hook', 'id_hook'],
            ['hook_module', 'id_module', 'module', 'id_module'],
            ['hook_module_exceptions', 'id_hook', 'hook', 'id_hook'],
            ['hook_module_exceptions', 'id_module', 'module', 'id_module'],
            ['hook_module_exceptions', 'id_shop', 'shop', 'id_shop'],
            ['image', 'id_product', 'product', 'id_product'],
            ['message', 'id_cart', 'cart', 'id_cart'],
            ['message_readed', 'id_employee', 'employee', 'id_employee'],
            ['message_readed', 'id_message', 'message', 'id_message'],
            ['module_access', 'id_profile', 'profile', 'id_profile'],
            ['module_country', 'id_country', 'country', 'id_country'],
            ['module_country', 'id_module', 'module', 'id_module'],
            ['module_country', 'id_shop', 'shop', 'id_shop'],
            ['module_currency', 'id_currency', 'currency', 'id_currency'],
            ['module_currency', 'id_module', 'module', 'id_module'],
            ['module_currency', 'id_shop', 'shop', 'id_shop'],
            ['module_group', 'id_group', 'group', 'id_group'],
            ['module_group', 'id_module', 'module', 'id_module'],
            ['module_group', 'id_shop', 'shop', 'id_shop'],
            ['module_preference', 'id_employee', 'employee', 'id_employee'],
            ['order_carrier', 'id_order', 'orders', 'id_order'],
            ['order_cart_rule', 'id_order', 'orders', 'id_order'],
            ['order_detail', 'id_order', 'orders', 'id_order'],
            ['order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'],
            ['order_history', 'id_order', 'orders', 'id_order'],
            ['order_invoice', 'id_order', 'orders', 'id_order'],
            ['order_invoice_payment', 'id_order', 'orders', 'id_order'],
            ['order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'],
            ['order_return', 'id_order', 'orders', 'id_order'],
            ['order_return_detail', 'id_order_return', 'order_return', 'id_order_return'],
            ['order_slip', 'id_order', 'orders', 'id_order'],
            ['order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'],
            ['orders', 'id_shop', 'shop', 'id_shop'],
            ['orders', 'id_shop_group', 'group_shop', 'id_shop_group'],
            ['pack', 'id_product_item', 'product', 'id_product'],
            ['pack', 'id_product_pack', 'product', 'id_product'],
            ['page', 'id_page_type', 'page_type', 'id_page_type'],
            ['page_viewed', 'id_date_range', 'date_range', 'id_date_range'],
            ['page_viewed', 'id_shop', 'shop', 'id_shop'],
            ['page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'],
            ['product_attachment', 'id_attachment', 'attachment', 'id_attachment'],
            ['product_attachment', 'id_product', 'product', 'id_product'],
            ['product_attribute', 'id_product', 'product', 'id_product'],
            ['product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'],
            ['product_attribute_combination', 'id_product_attribute', 'product_attribute', 'id_product_attribute'],
            ['product_attribute_image', 'id_image', 'image', 'id_image'],
            ['product_attribute_image', 'id_product_attribute', 'product_attribute', 'id_product_attribute'],
            ['product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'],
            ['product_carrier', 'id_product', 'product', 'id_product'],
            ['product_carrier', 'id_shop', 'shop', 'id_shop'],
            ['product_country_tax', 'id_country', 'country', 'id_country'],
            ['product_country_tax', 'id_product', 'product', 'id_product'],
            ['product_country_tax', 'id_tax', 'tax', 'id_tax'],
            ['product_download', 'id_product', 'product', 'id_product'],
            ['product_group_reduction_cache', 'id_group', 'group', 'id_group'],
            ['product_group_reduction_cache', 'id_product', 'product', 'id_product'],
            ['product_sale', 'id_product', 'product', 'id_product'],
            ['product_supplier', 'id_product', 'product', 'id_product'],
            ['product_supplier', 'id_supplier', 'supplier', 'id_supplier'],
            ['product_tag', 'id_product', 'product', 'id_product'],
            ['product_tag', 'id_tag', 'tag', 'id_tag'],
            ['range_price', 'id_carrier', 'carrier', 'id_carrier'],
            ['range_weight', 'id_carrier', 'carrier', 'id_carrier'],
            ['referrer_cache', 'id_connections_source', 'connections_source', 'id_connections_source'],
            ['referrer_cache', 'id_referrer', 'referrer', 'id_referrer'],
            ['search_index', 'id_product', 'product', 'id_product'],
            ['search_word', 'id_lang', 'lang', 'id_lang'],
            ['search_word', 'id_shop', 'shop', 'id_shop'],
            ['shop_url', 'id_shop', 'shop', 'id_shop'],
            ['specific_price_priority', 'id_product', 'product', 'id_product'],
            ['stock', 'id_product', 'product', 'id_product'],
            ['stock', 'id_warehouse', 'warehouse', 'id_warehouse'],
            ['stock_available', 'id_product', 'product', 'id_product'],
            ['tab_module_preference', 'id_employee', 'employee', 'id_employee'],
            ['tab_module_preference', 'id_tab', 'tab', 'id_tab'],
            ['tax_rule', 'id_country', 'country', 'id_country'],
            ['warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'],
            ['warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'],
            ['warehouse_product_location', 'id_product', 'product', 'id_product'],
            ['warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'],
        ];
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanLangTable(): int
    {
        try {
            $tables = $this->getConnection()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . '%_lang"');

            $result = 0;
            foreach ($tables as $table) {
                $tableLang = current($table);
                $table = strtr($tableLang, '_lang', '');
                $tableId = 'id_' . preg_replace('/^' . _DB_PREFIX_ . '/', '', $table);

                if (!$this->getConnection()->executeS('SHOW COLUMNS FROM `' . bqSQL($table) . '` LIKE "' . (int) $tableId . '"')) {
                    continue;
                }

                $this->getConnection()->delete(bqSQL($tableLang), (int) $tableId . '` NOT IN (SELECT `' . (int) $tableId . '` FROM `' . bqSQL($table) . '`)', 0, true, false);
                $result += $this->getConnection()->Affected_Rows();

                $this->getConnection()->delete(bqSQL($tableLang), '`id_lang` NOT IN (SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'lang`)', 0, true, false);
                $result += $this->getConnection()->Affected_Rows();
            }

            return $result;
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanShopTable(): int
    {
        try {
            $tables = $this->getConnection()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . '%_shop"');

            $result = 0;
            foreach ($tables as $table) {
                $tableShop = current($table);
                $table = strtr($tableShop, '_shop', '');
                $tableId = 'id_' . preg_replace('/^' . _DB_PREFIX_ . '/', '', $table);

                if ($tableShop === _DB_PREFIX_ . 'carrier_tax_rules_group_shop') {
                    continue;
                }

                if (!$this->getConnection()->executeS('SHOW COLUMNS FROM `' . bqSQL($table) . '` LIKE "' . bqSQL($tableId) . '"')) {
                    continue;
                }

                $this->getConnection()->delete(bqSQL($tableShop), '`' . bqSQL($tableId) . '` NOT IN (SELECT `' . bqSQL($tableId) . '` FROM `' . bqSQL($table) . '`)', 0, true, false);
                $result += $this->getConnection()->Affected_Rows();

                $this->getConnection()->delete(bqSQL($tableShop), '`id_shop` NOT IN (SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`', 0, true, false);
                $result += $this->getConnection()->Affected_Rows();
            }

            return $result;
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }

    public function cleanStockAvailable(): int
    {
        $this->getConnection()->delete('stock_available', '`id_shop` NOT IN (SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`) AND `id_shop_group` NOT IN (SELECT `id_shop_group` FROM `' . _DB_PREFIX_ . 'shop_group`)');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanCartTable(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('cart', '`id_cart` NOT IN (SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders`) AND `date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    private function daysToDate(int $date): int
    {
        $result = strtotime('-' . $date . ' day');
        if (!$result) {
            throw new PerformanceLiteInvalidDateException();
        }

        return $result;
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanRuleTable(int $days): int
    {
        $dateTo = pSQL(date('Y-m-d'));
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));

        $this->getConnection()->delete('cart_rule', '(`active` = 0 OR `quantity` = 0 OR `date_to` < ' . $dateTo . ') AND `date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanConnectionTables(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('connections', '`date_add` < "' . $dateAdd . '"');

        $result = 0;
        $result += $this->getConnection()->Affected_Rows();

        $this->getConnection()->delete('connections_page', '`id_connections` NOT IN (SELECT `id_connections` FROM `' . _DB_PREFIX_ . 'connections`)');
        $result += $this->getConnection()->Affected_Rows();

        $this->getConnection()->delete('connections_source', '`id_connections` NOT IN (SELECT `id_connections` FROM `' . _DB_PREFIX_ . 'connections`)');
        $result += $this->getConnection()->Affected_Rows();

        return $result;
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanStatsSearchTable(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('statssearch', '`date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanLogTable(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('log', '`date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanMailTable(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('mail', '`date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    public function cleanExpiredSpecificPrices(): int
    {
        $this->getConnection()->delete('specific_price', '`id_specific_price_rule` = 0 AND `to` > 0 AND `to` < NOW()');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanPageNotFoundTable(int $days): int
    {
        $dateAdd = pSQL(date('Y-m-d', $this->daysToDate($days)));
        $this->getConnection()->delete('pagenotfound', '`date_add` < "' . $dateAdd . '"');

        return $this->getConnection()->Affected_Rows();
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanGuestTable(): int
    {
        try {
            $this->getConnection()->delete('guest', '(`id_customer` = 0 OR `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer`))
            AND `id_guest` NOT IN (SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'cart`)
            AND `id_guest` NOT IN (SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'connections`)');

            $sql = (new DbQuery())
                ->select('DISTINCT `id_parent`')
                ->from('tab');

            $parents = $this->getConnection()->executeS($sql);
            $result = 0;
            foreach ($parents as $parent) {
                $sql = (new DbQuery())
                    ->select('id_tab')
                    ->from('tab')
                    ->where('`id_parent` = ' . (int) $parent['id_parent'])
                    ->orderBy('IF(class_name IN ("AdminHome", "AdminDashboard"), 1, 2), position');

                $children = $this->getConnection()->executeS($sql);

                $i = 1;
                foreach ($children as $child) {
                    $this->getConnection()->execute('UPDATE `' . _DB_PREFIX_ . 'tab` SET position = ' . $i++ . '
                    WHERE `id_tab` = ' . (int) $child['id_tab'] . '
                    AND `id_parent` = ' . (int) $parent['id_parent']);

                    $result += $this->getConnection()->Affected_Rows();
                }
            }

            return $result;
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }
}
