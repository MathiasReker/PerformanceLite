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

namespace PrestaShop\Module\PerformanceLite\domain\service\db;

use PrestaShop\Module\PerformanceLite\data\repository\DatabaseCleanerRepository;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteInvalidDateException;

class DatabaseCleaner
{
    /**
     * @var DatabaseCleanerRepository
     */
    private $cleanerRepository;

    public function __construct(DatabaseCleanerRepository $cleanerRepository)
    {
        $this->cleanerRepository = $cleanerRepository;
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanDoubletsConfiguration(): int
    {
        return $this->cleanerRepository->cleanConfigurationDoublets();
    }

    public function cleanLangConfiguration(): int
    {
        return $this->cleanerRepository->cleanConfigurationLang();
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanLangTable(): int
    {
        return $this->cleanerRepository->cleanLangTable();
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanShopTable(): int
    {
        return $this->cleanerRepository->cleanShopTable();
    }

    public function cleanStockAvailable(): int
    {
        return $this->cleanerRepository->cleanStockAvailable();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanCartTable(int $range): int
    {
        return $this->cleanerRepository->cleanCartTable($range);
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanRuleTable(int $range): int
    {
        return $this->cleanerRepository->cleanRuleTable($range);
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanConnectionTables(int $range): int
    {
        return $this->cleanerRepository->cleanConnectionTables($range);
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanStatsSearchTable(int $range): int
    {
        return $this->cleanerRepository->cleanStatsSearchTable($range);
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanLogTable(int $range): int
    {
        return $this->cleanerRepository->cleanLogTable($range);
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanMailTable(int $range): int
    {
        return $this->cleanerRepository->cleanMailTable($range);
    }

    public function cleanExpiredSpecificPrices(): int
    {
        return $this->cleanerRepository->cleanExpiredSpecificPrices();
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     */
    public function cleanPageNotFoundTable(int $range): int
    {
        return $this->cleanerRepository->cleanPageNotFoundTable($range);
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function cleanGuestTable(): int
    {
        return $this->cleanerRepository->cleanGuestTable();
    }
}
