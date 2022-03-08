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

namespace PrestaShop\Module\PerformanceLite\web\form;

use Configuration;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\domain\service\provider\PHPVersionProvider;
use PrestaShop\Module\PerformanceLite\domain\service\provider\PrestaShopVersionProvider;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteInvalidResourceException;
use PrestaShop\Module\PerformanceLite\web\util\View;
use Tools;

class SystemAnalyticsForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        $iconBolt = View::displayBoltIcon();

        $prestaShopVersionProvider = new PrestaShopVersionProvider();

        try {
            $psVersion = $prestaShopVersionProvider->getPrestashopLatestVersion();
        } catch (PerformanceLiteInvalidResourceException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            $psVersion = $this->module->l('Unknown', $this->className);
        }

        $prestaShopVersionProvider = [
            sprintf(
                $this->module->l('PrestaShop version (%s). It is recommended to upgrade the store to the latest PrestaShop as new versions include performance improvements and security fixes.', $this->className),
                _PS_VERSION_
            ),
            $prestaShopVersionProvider->isPrestaShopUpToDate(),
            sprintf(
                $this->module->l('Update PrestaShop to the latest version (%s).', $this->className),
                $psVersion
            ),
        ];

        $phpVersion = new PHPVersionProvider();
        $phpVersion = [
            sprintf(
                $this->module->l('PHP version (%s). It is recommended to upgrade to the latest PHP version as new PHP includes bug fixes, performance improvements and security fixes.', $this->className),
                Tools::checkPhpVersion()
            ),
            $phpVersion->isPhpVersionUpToDate(),
            sprintf(
                $this->module->l('Update PHP to the latest version (%s).', $this->className),
                $phpVersion->getNewestPhpVersionForThisPsVersion()
            ),
        ];

        $sslEnabled = [
            $this->module->l('Enable SSL. If you own an SSL certificate for your websites domain name, you can activate SSL encryption (https://) for customer account identification and order processing.', $this->className),
            (bool) Configuration::get('PS_SSL_ENABLED'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SSL_ENABLED'
            ),
        ];

        $sslEnabledEverywhere = [
            $this->module->l('Enable SSL on all pages. When enabled, all the pages of your webshop will be SSL-secured.', $this->className),
            (bool) Configuration::get('PS_SSL_ENABLED_EVERYWHERE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SSL_ENABLED_EVERYWHERE'
            ),
        ];

        $smartyCache = [
            $this->module->l('Smarty Cache. It should be enabled except for debugging.', $this->className),
            (bool) Configuration::get('PS_SMARTY_CACHE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SMARTY_CACHE'
            ),
        ];

        $smartyLocal = [
            $this->module->l('Multi-front optimizations. It should be enabled if you want to avoid storing the smarty cache on NFS. In most cases, it is best to disable it.', $this->className),
            !(bool) Configuration::get('PS_SMARTY_LOCAL'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SMARTY_LOCAL'
            ),
        ];

        $smartyType = [
            $this->module->l('Caching type should be set to file system.', $this->className),
            'filesystem' === Configuration::get('PS_SMARTY_CACHING_TYPE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SMARTY_CACHING_TYPE'
            ),
        ];

        $smartyClearCache = [
            $this->module->l('The clear cache should be set to never clear cache files. This is faster in production, but you must clear the cache manually once Smarty templates change.', $this->className),
            'never' === Configuration::get('PS_SMARTY_CLEAR_CACHE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_SMARTY_CLEAR_CACHE'
            ),
        ];

        $cssThemeCache = [
            $this->module->l('Smart cache for CSS.', $this->className),
            (bool) Configuration::get('PS_CSS_THEME_CACHE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_CSS_THEME_CACHE'
            ),
        ];

        $jsThemeCache = [
            $this->module->l('Smart cache for JavaScript.', $this->className),
            (bool) Configuration::get('PS_JS_THEME_CACHE'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_JS_THEME_CACHE'
            ),
        ];

        $apacheOptimization = [
            $this->module->l('Apache optimization. This will add directives to your .htaccess file, improving caching and compression.', $this->className),
            (bool) Configuration::get('PS_HTACCESS_CACHE_CONTROL'),
            View::displayBtnAjax(
                'configurationUpdate',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                'PS_HTACCESS_CACHE_CONTROL'
            ),
        ];

        $devMode = [
            $this->module->l('Debug mode. Once your webshop is in production, you must disable the debug mode to increase the performance.', $this->className),
            !_PS_MODE_DEV_,
            View::displayBtnAjax(
                'configurationUpdateConfigValue',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                '_PS_MODE_DEV_'
            ),
        ];

        $devProfiling = [
            $this->module->l('Debug profiling. Once your webshop is in production, you must disable the debug profiling to increase the performance.', $this->className),
            !_PS_DEBUG_PROFILING_,
            View::displayBtnAjax(
                'configurationUpdateConfigValue',
                sprintf($this->module->l('%s Execute', $this->className), $iconBolt),
                $this->module->l('Are you sure?', $this->className),
                '_PS_DEBUG_PROFILING_'
            ),
        ];

        $result = View::displayArrayAsTable(
            $this->createTableSettings([
                $sslEnabled,
                $sslEnabledEverywhere,
                $smartyCache,
                $smartyLocal,
                $smartyType,
                $smartyClearCache,
                $cssThemeCache,
                $jsThemeCache,
                $apacheOptimization,
                $devMode,
                $devProfiling,
            ])
        );

        $result .= View::displayArrayAsTable(
            $this->createTableVersions([$prestaShopVersionProvider, $phpVersion])
        );

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('System Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => $this->module->l('Scans the settings of your webshop and recommend options for better performance.', $this->className),
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => $result,
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }

    private function createTableSettings(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                $this->module->l('Status', $this->className) => $row[1]
                    ? View::displayLabelSuccess($this->module->l('Well done!', $this->className))
                    : View::displayLabelInfo(
                        $this->module->l('Can be improved', $this->className)
                    ),
                $this->module->l('Check', $this->className) => $row[0],
                View::displayAlign($this->module->l('How to fix', $this->className)) => View::displayAlign($row[2]),
            ];
        }

        return $result;
    }

    private function createTableVersions(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                $this->module->l('Status', $this->className) => $row[1]
                    ? View::displayLabelSuccess($this->module->l('Well done!', $this->className))
                    : View::displayLabelInfo(
                        $this->module->l('Can be improved', $this->className)
                    ),
                $this->module->l('Check', $this->className) => $row[0],
                $this->module->l('How to fix', $this->className) => $row[2],
            ];
        }

        return $result;
    }
}
