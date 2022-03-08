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

use Tools;
use Validate;

class TextAreaValidator implements Validator
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array<string>
     */
    private $array;

    /**
     * @var string
     */
    private $separator;

    public function __construct(string $value)
    {
        $this->field = $value;
    }

    public function removeEmptyKeys(): self
    {
        $this->array = array_filter($this->array);

        return $this;
    }

    public function removeInvalidUrls(): self
    {
        $temp = [];

        foreach ($this->array as $value) {
            if (Validate::isUrl($value)) {
                $temp[] = $value;
            }
        }

        $this->array = $temp;

        return $this;
    }

    public function removeDuplicates(): self
    {
        $this->array = Tools::arrayUnique($this->array);

        return $this;
    }

    public function execute(): string
    {
        return $this->arrayToText();
    }

    private function arrayToText(): string
    {
        return implode($this->separator, $this->array);
    }

    public function removeWhitespace(): self
    {
        $temp = [];

        foreach ($this->array as $value) {
            $temp[] = trim($value);
        }

        $this->array = $temp;

        return $this;
    }

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        $this->array = explode($separator, $this->field);

        return $this;
    }
}
