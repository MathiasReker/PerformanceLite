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

use PrestaShop\Module\PerformanceLite\domain\service\cache\ClearCache;

class ClearLogsCommand implements Command
{
    /**
     * @var ClearCache
     */
    private $output;

    public function __construct(ClearCache $output)
    {
        $this->output = $output;
    }

    /**
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $amount = $this->output->clearLogs(false);

        return [
            'amount' => $amount,
        ];
    }
}
