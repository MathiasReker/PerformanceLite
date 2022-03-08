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

use PrestaShop\Module\PerformanceLite\web\util\View;

class CacheForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Cache', $this->className),
                    'icon' => 'icon-rocket',
                ],
                'description' => $this->module->l('The module serves pages of the website as static content. Therefore, new visitors and crawlers will load your web pages much faster. Once a product is added to the cart or the user login to the website, the content gets dynamic, and PrestaShop\'s default behaviour is used instead of a static page cache. This is done to prevent conflicts with other modules and to avoid overrides. However, static resources like CSS and JS will still be loaded from the browser cache.', $this->className),
                'input' => [
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Page Cache', $this->className),
                        'name' => 'PP_PAGE_CACHE',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . $this->module->l('The module serves as a static HTML document of the website. As a result, new visitors and crawlers will load your web pages much faster.', $this->className)
                            . '<br>' . sprintf(
                                $this->module->l('%s Information: If your webshop is in debug mode, the page cache will be turned off.', $this->className),
                                View::displayInformationIcon()
                            ) . '<br>' . sprintf(
                                $this->module->l('%s Information: Some cookie-policy modules are not compatible with this cached technique.', $this->className),
                                View::displayInformationIcon()
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
                        'type' => 'switch',
                        'label' => $this->module->l('Browser cache', $this->className),
                        'name' => 'PP_HTACCESS_CACHE_CONTROL',
                        'is_bool' => true,
                        'desc' => sprintf(
                            $this->module->l('Enabling browser cache, adds Cache-Control HTTP header holds directives for caching in both requests and responses (Apache only). %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/http-cache/',
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
                ],
                'submit' => ['title' => $this->module->l('Save', $this->className)],
            ],
        ];
    }
}
