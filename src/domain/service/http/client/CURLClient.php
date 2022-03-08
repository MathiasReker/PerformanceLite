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

namespace PrestaShop\Module\PerformanceLite\domain\service\http\client;

use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDownloadResourceException;
use PrestaShop\Module\PerformanceLite\resources\config\Config;

class CURLClient
{
    private const TIMEOUT = 60;

    private const MAXREDIRS = 5;

    private const CONNECTTIMEOUT = 5;

    private const ENCODING = '';

    private const REFERER = '';

    private const FOLLOWLOCATION = true;

    private const RETURNTRANSFER = true;

    private const SSL_VERIFYPEER = false;

    private const SSL_VERIFYHOST = false;

    private const HEADER = false;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @throws PerformanceLiteDownloadResourceException
     */
    public function __construct(string $source)
    {
        $this->source = $source;

        $this->response();
    }

    /**
     * @throws PerformanceLiteDownloadResourceException
     */
    private function response(): void
    {
        $ch = curl_init();
        if (!$ch) {
            throw new PerformanceLiteDownloadResourceException('Failed to connect with CURL');
        }

        $options = [
            \CURLOPT_URL => $this->source,
            \CURLOPT_RETURNTRANSFER => self::RETURNTRANSFER,
            \CURLOPT_HEADER => self::HEADER,
            \CURLOPT_FOLLOWLOCATION => self::FOLLOWLOCATION,
            \CURLOPT_ENCODING => self::ENCODING,
            \CURLOPT_REFERER => self::REFERER,
            \CURLOPT_CONNECTTIMEOUT => self::CONNECTTIMEOUT,
            \CURLOPT_TIMEOUT => self::TIMEOUT,
            \CURLOPT_MAXREDIRS => self::MAXREDIRS,
            \CURLOPT_SSL_VERIFYPEER => self::SSL_VERIFYPEER,
            \CURLOPT_SSL_VERIFYHOST => self::SSL_VERIFYHOST,
            \CURLOPT_USERAGENT => Config::USER_AGENT,
        ];

        curl_setopt_array($ch, $options);
        $this->body = curl_exec($ch);
        $error = curl_error($ch);
        $this->statusCode = curl_getinfo($ch, \CURLINFO_HTTP_CODE);
        $this->contentType = curl_getinfo($ch, \CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($error) {
            throw new PerformanceLiteDownloadResourceException('Something went wrong downloading the content');
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponse(): string
    {
        return $this->body;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
