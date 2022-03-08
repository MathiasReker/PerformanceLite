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

namespace PrestaShop\Module\PerformanceLite\domain\service\command;

use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseConfiguration;
use PrestaShop\Module\PerformanceLite\resources\config\Database;

class ConfigurationUpdateCommand implements Command
{
    /**
     * @var DatabaseConfiguration
     */
    private $output;

    /**
     * @var string
     */
    private $key;

    public function __construct(DatabaseConfiguration $output, string $key)
    {
        $this->output = $output;
        $this->key = $key;
    }

    /**
     * @return array{result: bool}
     */
    public function execute(): array
    {
        $value = Database::getSystemSettings()[$this->key];

        $this->output->updateValue($this->key, $value);

        return [];
    }
}
