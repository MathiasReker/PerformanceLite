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

class ProfilerForm extends AbstractForm
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFields(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Profiler', $this->className),
                    'icon' => 'icon-bolt',
                ],
                'description' => $this->module->l('In software engineering, profiling is a form of dynamic program analysis that measures, for example, the space (memory) or time complexity of a program, the usage of particular instructions, or the frequency and duration of function calls.', $this->className),
                'warning' => $this->module->l('This is for advanced users only. Do not enable this feature if your website is in production. Web admins and developers use this feature to debug a slow website.', $this->className),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Profiling', $this->className),
                        'name' => 'PP_DEBUG_PROFILING',
                        'is_bool' => true,
                        'desc' => $this->module->l('The profiler is a great way to discover the bottlenecks in your code. Once enabled, each page on either the front-end or the back-end of your store will display a lot of information about the page and its files.', $this->className),
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
