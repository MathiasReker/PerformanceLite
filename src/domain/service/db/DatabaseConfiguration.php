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

use Configuration;

class DatabaseConfiguration
{
    public function updateValue(string $key, string $value): void
    {
        Configuration::updateValue($key, $value);
    }
}
