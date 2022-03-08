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

namespace PrestaShop\Module\PerformanceLite\domain\service\validation;

use Tools;

class ServerSettingsValidator
{
    /**
     * @return array<int, bool|string>
     */
    public function checkBoolean(string $key, bool $recommended, bool $default): array
    {
        $iniKey = (string) ini_get($key);

        $current = $this->isOn($iniKey) ?: $default;

        $status = $current !== $recommended;

        return [
            $key,
            $current ? 'On' : 'Off',
            $recommended ? 'On' : 'Off',
            $status,
        ];
    }

    private function isOn(string $key): bool
    {
        if ('0' === $key) {
            return false;
        }

        if ('off' === Tools::strtolower($key)) {
            return false;
        }

        return '' !== Tools::strtolower($key);
    }

    /**
     * @return array<int, bool|string>
     */
    public function checkString(string $key, string $recommended): array
    {
        $current = ini_get($key) ?: '';

        $status = $current !== $recommended;

        return [
            $key,
            $current,
            $recommended,
            $status,
        ];
    }

    /**
     * @return array<int, bool|int|string>
     */
    public function checkInteger(string $key, int $recommended): array
    {
        $current = ini_get($key) ?: '';

        $status = (int) $current !== $recommended;

        return [
            $key,
            $current,
            $recommended,
            $status,
        ];
    }

    /**
     * @return array<int, bool|string>
     */
    public function checkByte(string $key, string $recommended): array
    {
        $current = ini_get($key) ?: '';

        $status = Tools::convertBytes($current) !== Tools::convertBytes($recommended);

        return [
            $key,
            $current,
            $recommended,
            $status,
        ];
    }
}
