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

namespace PrestaShop\Module\PerformanceLite\domain\service\provider;

use PrestaShop\Module\PerformanceLite\domain\service\http\proxy\SimpleCache;
use PrestaShop\Module\PerformanceLite\resources\config\Database;
use Tools;

class PHPVersionProvider
{
    private const PHP_API = 'https://www.php.net/releases/';

    public function isPhpVersionUpToDate(): bool
    {
        return (bool) Tools::version_compare(
            Tools::checkPhpVersion(),
            $this->getNewestPhpVersionForThisPsVersion(),
            '>='
        );
    }

    public function getNewestPhpVersionForThisPsVersion(): string
    {
        $latestVersion = '';

        foreach (Database::getRecommendedDatabaseVersions() as $version => $phpVersion) {
            if (Tools::version_compare(_PS_VERSION_, $version, '>=')) {
                $latestVersion = $phpVersion;
            }
        }

        return $this->getNewestPhpVersion($latestVersion);
    }

    private function getNewestPhpVersion(string $currentVersion): string
    {
        $params = [
            'json' => '1',
            'version' => $currentVersion,
        ];

        $content = (new SimpleCache())
            ->expiresAfter(3600)
            ->get(self::PHP_API, self::PHP_API . '?' . http_build_query($params));

        $versions = (array) json_decode($content, true);

        return $versions['version'] ?: '';
    }
}
