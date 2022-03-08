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

use Configuration;
use Tools;

class SwitchValidator implements Validator
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function disableIfEmptyField(string $value): self
    {
        if (empty(Tools::getValue($value))) {
            $this->value = '';
        }

        return $this;
    }

    public function disableIfFalse(string $value): self
    {
        if (!Configuration::get($value)) {
            $this->value = '';
        }

        return $this;
    }

    public function execute(): string
    {
        return $this->value;
    }
}
