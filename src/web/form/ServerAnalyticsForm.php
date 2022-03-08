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

use PrestaShop\Module\PerformanceLite\domain\service\validation\ServerSettingsValidator;
use PrestaShop\Module\PerformanceLite\web\util\View;

class ServerAnalyticsForm extends AbstractForm
{
    /**
     * @return array<string, array<string, array<int|string, mixed>|string>>
     */
    public function getFields(): array
    {
        $serverConfig = new ServerSettingsValidator();

        if (\function_exists('opcache_get_status')) {
            $opCache = true;
        } else {
            $opCache = false;
        }

        $checkGrids = [
            $serverConfig->checkString('date.timezone', 'UTC'),
            $serverConfig->checkBoolean('session.auto_start', false, false),
            $serverConfig->checkBoolean('short_open_tag', false, false),
            $serverConfig->checkBoolean('display_errors', false, true),
            $serverConfig->checkBoolean('magic_quotes_gpc', false, true),
            $serverConfig->checkByte('memory_limit', '512M'),
            $serverConfig->checkInteger('max_execution_time', 300),
            $serverConfig->checkByte('upload_max_filesize', '20M'),
            $serverConfig->checkByte('post_max_size', '22M'),
            $serverConfig->checkInteger('max_input_vars', 20000),
            $serverConfig->checkBoolean('allow_url_fopen', true, true),
            $serverConfig->checkByte('realpath_cache_size', '4096K'),
            $serverConfig->checkInteger('realpath_cache_ttl', 600),
            $serverConfig->checkBoolean('opcache.enable', true, $opCache),
            $serverConfig->checkBoolean('opcache.enable_cli', false, !$opCache),
            $serverConfig->checkInteger('opcache.memory_consumption', 256),
            $serverConfig->checkInteger('opcache.interned_strings_buffer', 32),
            $serverConfig->checkInteger('opcache.max_accelerated_files', 16229),
            $serverConfig->checkInteger('opcache.max_wasted_percentage', 10),
            $serverConfig->checkInteger('opcache.revalidate_freq', 10),
            $serverConfig->checkBoolean('opcache.fast_shutdown', true, !$opCache),
            $serverConfig->checkBoolean('opcache.enable_file_override', false, !$opCache),
            $serverConfig->checkBoolean('opcache.max_file_size', false, !$opCache),
            $serverConfig->checkBoolean('zlib.output_compression', true, false),
            $serverConfig->checkBoolean('allow_url_fopen', true, true),
            $serverConfig->checkBoolean('allow_url_include', false, false),
        ];

        if (\extension_loaded('suhosin')) {
            $checkGridsSuhosin = [
                $serverConfig->checkInteger('suhosin.get.max_vars', 20000),
                $serverConfig->checkInteger('suhosin.post.max_vars', 20000),
            ];
        } else {
            $checkGridsSuhosin = [];
        }

        $checkGridTotal = array_merge($checkGrids, $checkGridsSuhosin);

        $result = [];
        foreach ($checkGridTotal as $checkGrid) {
            $result[] = [
                $this->module->l('Current setting', $this->className) => View::displayMonospaceLink(
                    $checkGrid[0] . ' = ' . $checkGrid[1]
                ),
                $this->module->l('Recommended setting', $this->className) => View::displayMonospaceLink(
                    $checkGrid[0] . ' = ' . $checkGrid[2],
                    true
                ),
                View::displayAlign($this->module->l('Status', $this->className)) => $checkGrid[3]
                    ? View::displayAlign(View::displayLabelInfo($this->module->l('Can be improved', $this->className)))
                    : View::displayAlign(View::displayLabelSuccess($this->module->l('Well done!', $this->className))),
            ];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Server Analytics', $this->className),
                    'icon' => 'icon-list',
                ],
                'description' => $this->module->l('Here are some advanced tips for configuring PHP for best performance. Your PHP configuration file is named php.ini. This file could be stored in different locations according to your setup. If you are not familiar with php.ini, you can ask your host for help.', $this->className),
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
