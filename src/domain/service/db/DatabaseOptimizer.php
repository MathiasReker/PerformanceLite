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

use PrestaShop\Module\PerformanceLite\data\repository\DatabaseOptimizerRepository;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;

class DatabaseOptimizer
{
    /**
     * @var DatabaseOptimizerRepository
     */
    private $cleanerRepository;

    public function __construct(DatabaseOptimizerRepository $cleanerRepository)
    {
        $this->cleanerRepository = $cleanerRepository;
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function changeEngineToInnoDb(): int
    {
        return $this->cleanerRepository->changeEngineToInnoDb();
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function repairTables(): int
    {
        return $this->cleanerRepository->repairTables();
    }

    /**
     * @throws PerformanceLiteDatabaseException
     */
    public function optimizeTables(): int
    {
        return $this->cleanerRepository->optimizeTables();
    }
}
