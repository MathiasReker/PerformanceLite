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

use PrestaShop\Module\PerformanceLite\resources\config\Config;
use PrestaShop\Module\PerformanceLite\web\util\View;

class ResourceLoadingForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Resource loading', $this->className),
                    'icon' => 'icon-font',
                ],
                'description' => $this->module->l('When you open a web page, the browser requests the HTML document from the server, parses its contents, and submits separate requests for any referenced resources. The application knows all about the resources the page needs and which are the most important. We can add additional information to the browser to speed up the loading process.', $this->className),
                'input' => [
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Web font links', $this->className),
                        'name' => 'PP_PRELOAD_FONTS',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Tells the browser to download, cache, and compile fonts as soon as possible. It\'s helpful to have imported fonts inside a stylesheet and load your app faster. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/codelab-preload-web-fonts/',
                                $this->module->l('Read more', $this->className)
                            )
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', $this->className),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', $this->className),
                            ],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 8,
                        'label' => $this->module->l('Preload font links', $this->className),
                        'desc' => View::displayProTag() . ' ' . $this->module->l('Add links to fonts that you would like to preload The module can analyse your website and determine which font must be auto-filled. Using the autofill links feature will override links in the text field.', $this->className) . ' ' . sprintf($this->module->l('Separate each URL by a pipe "%s" without spaces.'), Config::PIPE_SEPARATOR) . '<br>' . View::displayBtnAjax('getPrefetchLink', sprintf($this->module->l('%s Autofill links', $this->className), View::displayMagicIcon()), $this->module->l('Are you sure?', $this->className)),
                        'name' => 'PP_PRELOAD_FONTS_TEXT',
                    ],
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Preconnect to required origins', $this->className),
                        'name' => 'PP_PRECONNECT_LINKS',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Asks the browser to perform a connection to a domain in advance. It\'s helpful when you know you\'ll download something from that domain soon, but you don\'t know what exactly, and you want to speed up the initial connection. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/uses-rel-preconnect/',
                                $this->module->l('Read more', $this->className)
                            )
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', $this->className),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', $this->className),
                            ],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 8,
                        'label' => $this->module->l('Preconnect links', $this->className),
                        'desc' => View::displayProTag() . ' ' . $this->module->l('Add links to resources that you would like to preconnect to. The module can analyse your website and determine which font must be auto-filled. Using the autofill links feature will override links in the text field.', $this->className) . ' ' . sprintf($this->module->l('Separate each URL by a pipe "%s" without spaces.'), Config::PIPE_SEPARATOR) . '<br>' . View::displayBtnAjax('getPreConnectLinks', sprintf($this->module->l('%s Autofill links', $this->className), View::displayMagicIcon()), $this->module->l('Are you sure?', $this->className)),
                        'name' => 'PP_PRECONNECT_LINKS_TEXT',
                    ],
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Prefetch on hover', $this->className),
                        'name' => 'PP_INSTANT_LOAD_LINK',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf($this->module->l('Asks the browser to download and cache a page in the background when a link hovers for 100ms. The download happens with a low priority, so it doesn\'t interfere with more critical resources. However, it\'s helpful when you know you\'ll need that resource on a subsequent page and want to cache it ahead of time. Most chromium-powered browsers support this technique. %s.', $this->className), View::displayLink('https://web.dev/codelab-quicklink/', $this->module->l('Read more', $this->className))) . '<br>' . sprintf($this->module->l('%s Warning: With this feature enabled, the server uses more resources.', $this->className), View::displayWarningIcon()),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', $this->className),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', $this->className),
                            ],
                        ],
                    ],
                ],
                'submit' => ['title' => $this->module->l('Save', $this->className)],
            ],
        ];
    }
}
