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

namespace PrestaShop\Module\PerformanceLite\domain\service\command\util;

use Configuration;

class CommandUserConfig
{
    private function __construct()
    {
    }

    public static function getRangeByKey(string $key): int
    {
        $range = Configuration::get($key);
        if (!empty($range)) {
            return (int) $range;
        }

        return -1;
    }
}
