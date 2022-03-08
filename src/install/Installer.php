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

namespace PrestaShop\Module\PerformanceLite\install;

use Configuration;
use PrestaShop\Module\PerformanceLite\resources\config\Config;
use PrestaShop\Module\PerformanceLite\resources\config\Field;
use Tab;
use Tools;

class Installer extends AbstractInstaller
{
    private const PARENT_CLASS_NAME = 'IMPROVE';

    private const ICON = 'whatshot';

    public function execute(): bool
    {
        $this->checkPhpVersion();

        $this->installConfig();

        return $this->installTab();
    }

    private function checkPhpVersion(): void
    {
        if (Tools::version_compare(Tools::checkPhpVersion(), Config::MINIMUM_PHP_VERSION)) {
            $error = sprintf(
                $this->module->l('The module requires PHP %s or higher.', $this->className),
                Config::MINIMUM_PHP_VERSION
            );

            $this->displayError($error);
        }
    }

    private function installConfig(): void
    {
        $configs = Field::getPreconfiguredValues();

        if (empty($configs)) {
            return;
        }

        foreach ($configs as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                $error = sprintf(
                    $this->module->l('The configuration %s has not been installed.', $this->className),
                    $key
                );

                $this->displayError($error);
            }
        }
    }

    private function installTab(): bool
    {
        return (new TabBuilder(new Tab()))
            ->module($this->module->name)
            ->displayName($this->module->displayName)
            ->className(Config::CONTROLLER_NAME)
            ->parentClassName(self::PARENT_CLASS_NAME)
            ->icon(self::ICON)
            ->install();
    }
}
