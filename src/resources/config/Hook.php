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

namespace PrestaShop\Module\PerformanceLite\resources\config;

class Hook
{
    private function __construct()
    {
    }

    /**
     * Returns array of used hooks.
     *
     * @return array<string>
     */
    public static function getHooks(): array
    {
        return [
            'actionAdminControllerSetMedia',
        ];
    }
}
