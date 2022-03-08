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

namespace PrestaShop\Module\PerformanceLite\domain\service\parser;

use Configuration;
use PrestaShop\Module\PerformanceLite\resources\config\Config;

abstract class AbstractResourceHeader
{
    protected function textAreaToArray(string $key): array
    {
        return array_filter(explode(Config::PIPE_SEPARATOR, Configuration::get($key)));
    }
}
