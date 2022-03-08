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

use PrestaShop\Module\PerformanceLite\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDefineValueException;
use PrestaShop\Module\PerformanceLite\resources\config\Database;

class ConfigurationUpdateConfigValueCommand implements Command
{
    /**
     * @var DefineValueService
     */
    private $output;

    /**
     * @var string
     */
    private $key;

    public function __construct(DefineValueService $output, string $key)
    {
        $this->output = $output;
        $this->key = $key;
    }

    /**
     * @throws PerformanceLiteDefineValueException
     *
     * @return array{result: bool}
     */
    public function execute(): array
    {
        $value = Database::getConfigValues()[$this->key];

        $this->output->updateValue($this->key, $value);

        return [];
    }
}
