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

namespace PrestaShop\Module\PerformanceLite\domain\service\command;

use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseOptimizer;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;

class RepairTablesCommand implements Command
{
    /**
     * @var DatabaseOptimizer
     */
    private $output;

    public function __construct(DatabaseOptimizer $output)
    {
        $this->output = $output;
    }

    /**
     * @throws PerformanceLiteDatabaseException
     *
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $amount = $this->output->repairTables();

        return [
            'amount' => $amount,
        ];
    }
}
