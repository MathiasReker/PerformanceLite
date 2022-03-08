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

use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;

class CleanTablesCommand implements Command
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
     * @throws PerformanceLiteDatabaseException
     *
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $amount = 0;
        $amount += $this->output->cleanDoubletsConfiguration();
        $amount += $this->output->cleanLangConfiguration();
        $amount += $this->output->cleanLangTable();
        $amount += $this->output->cleanShopTable();
        $amount += $this->output->cleanStockAvailable();

        return [
            'amount' => $amount,
        ];
    }
}
