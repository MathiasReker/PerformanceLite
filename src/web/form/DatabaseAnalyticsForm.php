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

use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDatabaseException;
use PrestaShop\Module\PerformanceLite\resources\config\Database;
use PrestaShop\Module\PerformanceLite\web\util\View;

class DatabaseAnalyticsForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        $iconFilter = '<i class="icon icon-filter"></i>';
        $settings = Database::getDatabaseSettings();
        $checkGrids = [];

        $serverConfig = new DatabaseSettings();

        foreach ($settings as $setting => $key) {
            try {
                $checkGrids[] = $serverConfig->formatConfigKey(
                    $setting,
                    (string) $key,
                    View::displayBtnAjax(
                        'updateDbValue',
                        sprintf($this->module->l('%s Optimize value', $this->className), $iconFilter),
                        $this->module->l('Are you sure?', $this->className),
                        $setting
                    ),
                    is_numeric($key)
                );
            } catch (PerformanceLiteDatabaseException $e) {
                LogService::error($e->getMessage(), $e->getTrace());
            }
        }

        $result = [];
        foreach ($checkGrids as $checkGrid) {
            $result[] = [
                $this->module->l('Current setting', $this->className) => sprintf(
                    View::displayMonospaceLink(
                        '%s = <span class="pp-amount">%s</span>'
                    ),
                    $checkGrid[0],
                    $checkGrid[1]
                ),
                $this->module->l('Recommended setting', $this->className) => View::displayMonospaceLink(
                    $checkGrid[0] . ' = ' . $checkGrid[2],
                    true
                ),
                View::displayAlign($this->module->l('Action', $this->className)) => View::displayAlign($checkGrid[3]),
            ];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Database Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => sprintf(
                    $this->module->l('Here are some advanced tips for configuring your database for best performance. These settings are recommended for most PrestaShop websites. %s. By clicking "Optimize value", you update the value to the recommended value. This value is saved until the database is restarted. If you want to change to value permanent, you must do it in %s. The location of your database configuration file depends on your webserver setup.', $this->className),
                    View::displayLink(
                        'https://devdocs.prestashop.com/1.7/scale/optimizations/',
                        $this->module->l('Read more', $this->className)
                    ),
                    'my.conf'
                ),
                'input' => [
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => View::displayArrayAsTable($result),
                        'col' => 12,
                        'name' => '',
                    ],
                ],
            ],
        ];
    }
}
