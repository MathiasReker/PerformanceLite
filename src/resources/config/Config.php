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

use PrestaShop\Module\PerformanceLite\domain\service\util\ContextService;

class Config
{
    public const DEFAULT_MODE_FOLDER = 0755;

    public const DEFAULT_PNG_TO_WEBP_QUALITY = 85;

    public const DEFAULT_JPEG_TO_WEBP_QUALITY = 75;

    public const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36';

    public const MODULE_NAME = 'performancelite';

    public const CONTROLLER_NAME = 'AdminPerformanceLite';

    public const CMS_NAME = 'PrestaShop';

    public const MINIMUM_PHP_VERSION = '7.1';

    public const EXCEPTION_LOG = 'exception.log';

    public const PIPE_SEPARATOR = '|';

    private function __construct()
    {
    }

    public static function getJsLink(): string
    {
        return self::getModuleRelativeLink() . 'views/js/';
    }

    private static function getModuleRelativeLink(): string
    {
        return __PS_BASE_URI__ . 'modules/' . self::MODULE_NAME . '/';
    }

    public static function getCssLink(): string
    {
        return self::getModuleRelativeLink() . 'views/css/';
    }

    public static function getLogo(): string
    {
        return self::getModuleRelativeLink() . 'logo.png';
    }

    private static function getModulePath(): string
    {
        return _PS_MODULE_DIR_ . self::MODULE_NAME . '/';
    }

    public static function getVarPath(): string
    {
        return self::getModulePath() . 'var/';
    }

    public static function getLogPath(): string
    {
        return self::getModulePath() . 'var/logs/';
    }

    public static function getCachePrivatePath(): string
    {
        return self::getModulePath() . 'var/cache/';
    }

    public static function getHttpCachePath(): string
    {
        return self::getCachePrivatePath() . ContextService::getShop()->id . '/';
    }

    public static function getModuleCachePath(): string
    {
        return self::getCachePrivatePath() . 'm/';
    }
}
