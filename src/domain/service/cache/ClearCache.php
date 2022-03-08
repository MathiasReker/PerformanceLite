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

namespace PrestaShop\Module\PerformanceLite\domain\service\cache;

use Category;
use Media;
use PrestaShop\Module\PerformanceLite\data\repository\QueryCacheRepository;
use PrestaShop\Module\PerformanceLite\domain\service\util\DirectoryService;
use Tools;

class ClearCache
{
    public function clearXmlCache(): void
    {
        Tools::clearXMLCache();
    }

    public function resetQueryCache(): void
    {
        (new QueryCacheRepository())->resetQueryCache();
    }

    public function flushQueryCache(): void
    {
        (new QueryCacheRepository())->flushQueryCache();
    }

    public function clearOpCache(): bool
    {
        if (\function_exists('opcache_get_status')) {
            opcache_reset();

            return true;
        }

        return false;
    }

    public function clearApcCache(): bool
    {
        if (\function_exists('apc_clear_cache')) {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');

            return true;
        }

        return false;
    }

    public function clearMediaCache(): void
    {
        Media::clearCache();
    }

    public function clearSmartyCacheAndSfCache(): void
    {
        Tools::clearSmartyCache();
        Tools::clearSf2Cache('dev');
        Tools::clearSf2Cache('prod');

        self::regenerateCache();
    }

    private function regenerateCache(): void
    {
        Tools::generateIndex();
        Category::regenerateEntireNtree();
    }

    public function clearLogs(bool $analyze = false): int
    {
        $logPath = _PS_CORE_DIR_ . $this->getLogPath();

        $result = (new DirectoryService($logPath))->countFilesInDirectory();

        if (!$analyze) {
            Tools::deleteDirectory($logPath, false);
        }

        return $result;
    }

    private function getLogPath(): string
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<=')) {
            return '/app/logs/';
        }

        return '/var/logs/';
    }
}
