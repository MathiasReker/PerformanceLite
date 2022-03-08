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

class DatabaseSettingsRepository extends Connection
{
    public function updateValue(string $key, string $value): void
    {
        $this->getConnection()->execute('SET GLOBAL ' . pSQL($key) . ' = ' . pSQL($value));
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function getValue(string $key): string
    {
        try {
            $values = (array) $this->getConnection(false)
                ->executeS('SHOW VARIABLES LIKE "' . pSQL($key) . '"');

            if (empty($values)) {
                return '';
            }

            return $values[0]['Value'];
        } catch (PrestaShopDatabaseException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            throw new PerformanceLiteDatabaseException();
        }
    }
}
