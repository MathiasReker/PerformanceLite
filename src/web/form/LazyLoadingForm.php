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

class LazyLoadingForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Lazy loading', $this->className),
                    'icon' => 'icon-spinner',
                ],
                'description' => $this->module->l('Lazy loading is the practice of delaying load or initialization of resources or objects until they\'re needed to improve performance and save system resources. For instance, if a web page has an image that the user has to scroll down to see, with lazy loading, the image will only be loaded when the user arrives at its location.', $this->className),
                'input' => [
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Lazy load images', $this->className),
                        'name' => 'PP_LAZY_LOAD_IMG',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Lazy load all images. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/browser-level-image-lazy-loading/',
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
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Lazy load iframes', $this->className),
                        'name' => 'PP_LAZY_LOAD_IFRAME',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Lazy load all iframes. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/iframe-lazy-loading/',
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
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Lazy load videos', $this->className),
                        'name' => 'PP_LAZY_LOAD_VIDEO',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Lazy load all videos. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/lazy-loading-video/',
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
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Lazy load footer', $this->className),
                        'name' => 'PP_LAZY_LOAD_FOOTER',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . $this->module->l('Lazy load the footer.', $this->className),
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
