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
use Tab;

class Uninstaller extends AbstractInstaller
{
    public function execute(): bool
    {
        $this->uninstallConfig();

        return $this->uninstallTab();
    }

    private function uninstallConfig(): void
    {
        if (empty($this->fieldValues)) {
            return;
        }

        foreach (array_keys($this->fieldValues) as $name) {
            Configuration::deleteByName($name);
        }
    }

    private function uninstallTab(): bool
    {
        return (new TabBuilder(new Tab()))
            ->className(Config::CONTROLLER_NAME)
            ->uninstall();
    }
}
