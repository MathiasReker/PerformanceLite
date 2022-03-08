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

class ClearLogTableCommand implements Command
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
        $range = CommandUserConfig::getRangeByKey('PP_LOG_TABLE_CLEANER');

        $amount = $this->output->cleanLogTable($range);

        return [
            'amount' => $amount,
        ];
    }
}
