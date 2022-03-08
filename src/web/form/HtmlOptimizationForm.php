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

use PrestaShop\Module\PerformanceLite\domain\service\util\LinkService;
use PrestaShop\Module\PerformanceLite\web\util\View;

class HtmlOptimizationForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        $minifyHtml = [
            $this->module->l('Remove default HTML comments.', $this->className),
            $this->module->l('Sum up extra whitespace from the DOM.', $this->className),
            $this->module->l('Remove whitespace around tags.', $this->className),
            $this->module->l('Inline CSS, inline JS and conditional comments are protected.', $this->className),
        ];

        $optimizeAttributes = [
            $this->module->l('Remove deprecated anchor jump.', $this->className),
            $this->module->l('Remove deprecated script-mime-types.', $this->className),
            $this->module->l('Remove some empty attributes.', $this->className),
            $this->module->l('Remove value tag from empty input tag.', $this->className),
            $this->module->l('Remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway.', $this->className),
            $this->module->l('Remove "media="all" from all links and styles.', $this->className),
            $this->module->l('Sort CSS-class-names for better Gzip/DEFLATE results.', $this->className),
            $this->module->l('Sort HTML attributes for better Gzip/DEFLATE results.', $this->className),
            $this->module->l('Remove quotes attributes if they don\'t contain characters that necessitate quoting.', $this->className),
            $this->module->l('Remove omitted HTML tags.', $this->className),
        ];

        $link = 'https://validator.w3.org/nu/?doc=' . LinkService::getBaseLink();

        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('HTML Optimization', $this->className),
                    'icon' => 'icon-html5',
                ],
                'description' => sprintf(
                    $this->module->l('Safely reduce the size of your HTML code, making your websites load faster. These features can also optimize the HTML document to follow modern HTML5 markups. You can scan your web application to see the difference using the %s.', $this->className),
                    View::displayLink(
                        $link,
                        $this->module->l('W3C validator', $this->className)
                    )
                ),
                'warning' => 'Testing the website with and without the HTML optimization features is recommended. This is because, in some cases, some of these features could negatively affect the performance.',
                'input' => [
                    [
                        'disabled' => true,
                        'type' => 'switch',
                        'label' => $this->module->l('Minify HTML', $this->className),
                        'name' => 'PP_MINIFY_HTML',
                        'is_bool' => true,
                        'desc' => '</p>'
                            . View::displayProTag() . ' ' . View::displayList($minifyHtml, 'help-block')
                            . '<p>',
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
                        'label' => $this->module->l('Optimize attributes', $this->className),
                        'name' => 'PP_OPTIMIZE_ATTRIBUTES',
                        'is_bool' => true,
                        'desc' => '</p>'
                            . View::displayProTag() . ' ' . View::displayList(
                                $optimizeAttributes,
                                'help-block'
                            )
                            . '<p>',
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
                        'label' => $this->module->l('Defer Javascript', $this->className),
                        'name' => 'PP_LOAD_SCRIPT_ASYNC',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Improve site\'s performance by adding "defer" tag to the external combined javascript bundle. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/efficiently-load-third-party-javascript/',
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
                        'label' => $this->module->l('Async image decoding', $this->className),
                        'name' => 'PP_DECODE_IMG_ASYNC',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Decode the image asynchronously. Rendering of pages and decoding of the image is done in parallel. This makes the page render faster. %s.', $this->className),
                            View::displayLink(
                                'https://usefulangle.com/post/277/img-decoding-attribute',
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
                        'label' => $this->module->l('Add missing image size', $this->className),
                        'name' => 'PP_IMG_SIZE',
                        'is_bool' => true,
                        'desc' => View::displayProTag() . ' ' . sprintf(
                            $this->module->l('Add missing size attributes to the images. %s.', $this->className),
                            View::displayLink(
                                'https://web.dev/optimize-cls/',
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
