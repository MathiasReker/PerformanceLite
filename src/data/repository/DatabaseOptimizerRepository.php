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

use PrestaShop\Module\PerformanceLite\data\util\Connection;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;
use PrestaShopDatabaseException;

class DatabaseOptimizerRepository extends Connection
{
    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function changeEngineToInnoDb(): int
    {
        try {
            $result = 0;
            $tableResults = (array) $this->getConnection()->executeS('
                SELECT table_name
                FROM INFORMATION_SCHEMA.TABLES
                WHERE ENGINE = "MyISAM" AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
            ');
            $tables = array_column($tableResults, 'table_name');
            foreach ($tables as $table) {
                $this->getConnection()->execute('ALTER TABLE ' . bqSQL($table) . ' ENGINE=InnoDB');
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
    public function repairTables(): int
    {
        try {
            $result = 0;
            $tables = (array) $this->getConnection()->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');
            foreach ($tables as $table) {
                $currentTable = _DB_PREFIX_ . current($table);
                if (mb_strlen($currentTable) > 64) {
                    continue;
                }
                $this->getConnection()->execute('REPAIR TABLE `' . $currentTable . '`');
                ++$result;
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
    public function optimizeTables(): int
    {
        try {
            $result = 0;
            $tables = (array) $this->getConnection()->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');
            foreach ($tables as $table) {
                $currentTable = _DB_PREFIX_ . current($table);
                if (mb_strlen($currentTable) > 64) {
                    continue;
                }
                $this->getConnection()->execute('OPTIMIZE TABLE `' . $currentTable . '`');
                ++$result;
            }

            return $result;
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }
}
