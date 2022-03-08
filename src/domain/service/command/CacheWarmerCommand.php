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

use PrestaShop\Module\PerformanceLite\domain\service\cache\CacheWarmer;

class CacheWarmerCommand implements Command
{
    /**
     * @var CacheWarmer
     */
    private $output;

    public function __construct(CacheWarmer $output)
    {
        $this->output = $output;
    }

    public function execute(): array
    {
        $amount = $this->output->run()->getResult();

        return [
            'amount' => $amount,
        ];
    }
}
