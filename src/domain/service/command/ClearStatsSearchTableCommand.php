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

use PrestaShop\Module\PerformanceLite\domain\service\command\util\CommandUserConfig;
use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteInvalidDateException;

class ClearStatsSearchTableCommand implements Command
{
    /**
     * @var DatabaseCleaner
     */
    private $output;

    public function __construct(DatabaseCleaner $output)
    {
        $this->output = $output;
    }

    /**
     * @throws PerformanceLiteInvalidDateException
     *
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $range = CommandUserConfig::getRangeByKey('PP_STATS_SEARCH_TABLE_CLEANER');

        $amount = $this->output->cleanStatsSearchTable($range);

        return [
            'amount' => $amount,
        ];
    }
}
