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

namespace PrestaShop\Module\PerformanceLite\domain\service\validation;

use Module;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteValidationException;
use PrestaShop\Module\PerformanceLite\resources\config\Config;
use Validate;

class TextValidator implements Validator
{
    /**
     * @var string
     */
    private $field;

    public function __construct(string $value)
    {
        $this->field = $value;
    }

    /**
     * @throws PerformanceLiteValidationException
     */
    public function mustBeEmptyOrAnInteger(): self
    {
        if (!empty($this->field)) {
            if (!Validate::isInt($this->field)) {
                $message = $this->getModuleInstance()->l('The value must be empty or an integer.');
                throw new PerformanceLiteValidationException($message);
            }
        }

        return $this;
    }

    private function getModuleInstance()
    {
        return Module::getInstanceByName(Config::MODULE_NAME);
    }

    public function mustBeBetween(int $from, int $to, int $default): self
    {
        if ($this->field < $from || $this->field > $to || !Validate::isInt($this->field)) {
            $this->field = (string) $default;
        }

        return $this;
    }

    /**
     * @throws PerformanceLiteValidationException
     */
    public function mustBeAColor(): self
    {
        if (!Validate::isColor($this->field)) {
            $message = $this->getModuleInstance()->l('The value must be of a valid color format.');
            throw new PerformanceLiteValidationException($message);
        }

        return $this;
    }

    public function execute(): string
    {
        return $this->field;
    }
}
