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

namespace PrestaShop\Module\PerformanceLite\domain\service\form;

use PrestaShop\Module\PerformanceLite\domain\service\validation\SwitchValidator;
use PrestaShop\Module\PerformanceLite\domain\service\validation\TextAreaValidator;
use PrestaShop\Module\PerformanceLite\domain\service\validation\TextValidator;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteValidationException;
use PrestaShop\Module\PerformanceLite\resources\config\Config;

class FormValidate
{
    /**
     * @var string
     */
    private $result;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $field, string $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function validate(): self
    {
        try {
            switch ($this->field) {
                case 'PP_CART_TABLE_CLEANER':
                case 'PP_CART_RULE_TABLE_CLEANER':
                case 'PP_STATS_SEARCH_TABLE_CLEANER':
                case 'PP_CONNECTION_TABLE_CLEANER':
                case 'PP_PAGE_NOT_FOUND_TABLE_CLEANER':
                case 'PP_LOG_TABLE_CLEANER':
                case 'PP_MAIL_TABLE_CLEANER':
                    $this->result = (new TextValidator($this->value))
                        ->mustBeEmptyOrAnInteger()
                        ->execute();
                    break;
                case 'PP_PRELOAD_FONTS_TEXT':
                case 'PP_PRECONNECT_LINKS_TEXT':
                case 'PP_CACHE_WARMER_SITEMAPS':
                    $this->result = (new TextAreaValidator($this->value))
                        ->setSeparator(Config::PIPE_SEPARATOR)
                        ->removeWhitespace()
                        ->removeEmptyKeys()
                        ->removeDuplicates()
                        ->removeInvalidUrls()
                        ->execute();
                    break;
                case 'PP_CONVERT_JPEG_TO_WEBP_QUALITY':
                    $this->result = (new TextValidator($this->value))
                        ->mustBeBetween(0, 100, Config::DEFAULT_JPEG_TO_WEBP_QUALITY)
                        ->execute();
                    break;
                case 'PP_CONVERT_PNG_TO_WEBP_QUALITY':
                    $this->result = (new TextValidator($this->value))
                        ->mustBeBetween(0, 100, Config::DEFAULT_PNG_TO_WEBP_QUALITY)
                        ->execute();
                    break;
                case 'PP_PRELOAD_FONTS':
                    $this->result = (new SwitchValidator($this->value))
                        ->disableIfEmptyField('PP_PRELOAD_FONTS_TEXT')
                        ->execute();
                    break;
                case 'PP_PRECONNECT_LINKS':
                    $this->result = (new SwitchValidator($this->value))
                        ->disableIfEmptyField('PP_PRECONNECT_LINKS_TEXT')
                        ->execute();
                    break;
                case 'PP_LOAD_SCRIPT_ASYNC':
                    $this->result = (new SwitchValidator($this->value))
                        ->disableIfFalse('PS_JS_THEME_CACHE')
                        ->execute();
                    break;

                default:
                    $this->result = $this->value;
            }
        } catch (PerformanceLiteValidationException $e) {
            $this->error = $e->getMessage();
        }

        return $this;
    }

    /**
     * @return array{result: string, error: string}
     */
    public function getResponse(): array
    {
        return [
            'result' => $this->result,
            'error' => $this->error,
        ];
    }
}
