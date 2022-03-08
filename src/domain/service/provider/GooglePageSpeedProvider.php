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

namespace PrestaShop\Module\PerformanceLite\domain\service\provider;

use Exception;
use PrestaShop\Module\PerformanceLite\domain\service\http\proxy\SimpleCache;

class GooglePageSpeedProvider
{
    private const GOOGLE_PAGE_SPEED_API = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /**
     * @var string
     */
    private $url;

    /**
     * @throws Exception
     *
     * @return array<string>
     */
    public function getFontDisplay(): array
    {
        return $this->getGooglePageSpeedResult('font-display');
    }

    /**
     * @throws Exception
     *
     * @return array<string>
     */
    private function getGooglePageSpeedResult(string $type): array
    {
        $result = [];

        $googlePageSpeedReport = $this->getGooglePageSpeedReport();

        $googlePageSpeedResults = (array) json_decode($googlePageSpeedReport, true);

        if (isset($googlePageSpeedResults['error'])) {
            return $result;
        }

        $audits = (array) $googlePageSpeedResults['lighthouseResult']['audits'][$type]['details']['items'];

        foreach ($audits as $audit) {
            $result[] = $audit['url'];
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function getGooglePageSpeedReport(): string
    {
        $params = [
            'url' => $this->url,
            'category' => 'performance',
        ];

        return (new SimpleCache())
            ->expiresAfter(3600)
            ->get(self::GOOGLE_PAGE_SPEED_API, self::GOOGLE_PAGE_SPEED_API . '?' . http_build_query($params));
    }

    /**
     * @throws Exception
     *
     * @return array<string>
     */
    public function getUserRelPreconnect(): array
    {
        return $this->getGooglePageSpeedResult('uses-rel-preconnect');
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
