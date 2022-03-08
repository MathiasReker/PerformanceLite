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

namespace PrestaShop\Module\PerformanceLite\domain\service\util;

use PrestaShop\Module\PerformanceLite\resources\config\Config;
use Tools;

class LinkService
{
    private function __construct()
    {
    }

    public static function createCronLink(string $technicalName, $key, bool $ajax): string
    {
        $tokenKey = $ajax ? 'ajax' : 'cron';

        $token = Tools::hashIV(Config::MODULE_NAME . '/' . $tokenKey . $technicalName);

        return ContextService::getLink()->getModuleLink(
            Config::MODULE_NAME,
            'cron',
            [
                'name' => $technicalName,
                'token' => $token,
                'ajax' => $ajax ? true : null,
                'key' => $key,
            ]
        );
    }

    public static function createNormalizedLink(string $url): string
    {
        $url = strtr($url, '\\', '//');

        return str_replace('//', '/', $url);
    }

    public static function createRelativeLink(string $url): string
    {
        return preg_replace('|^(https?:)?//[^/]+(/?.*)|i', '$2', $url);
    }

    public static function getBaseLink(): string
    {
        return self::getLink() . __PS_BASE_URI__;
    }

    public static function getLink(bool $http = true): string
    {
        return Tools::getHttpHost($http, true, true);
    }
}
