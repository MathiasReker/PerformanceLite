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

namespace PrestaShop\Module\PerformanceLite\resources\config;

use PrestaShop\Module\PerformanceLite\domain\service\util\LinkService;
use Shop;

class Field
{
    private function __construct()
    {
    }

    /**
     * @return array<string, int>
     */
    public static function getPreconfiguredValues(): array
    {
        return [
            'PP_CONVERT_JPEG_TO_WEBP_QUALITY' => Config::DEFAULT_JPEG_TO_WEBP_QUALITY,
            'PP_CONVERT_PNG_TO_WEBP_QUALITY' => Config::DEFAULT_PNG_TO_WEBP_QUALITY,
            'PP_LOG_EXCEPTIONS' => true,
            'PP_CACHE_WARMER_SITEMAPS' => implode('|', self::getSitemapUrls()),
        ];
    }

    private static function getSitemapUrls(): array
    {
        $shops = Shop::getShops(true, null, true);

        $result = [];
        foreach ($shops as $shopId) {
            $file = $shopId . '_index_sitemap.xml';
            if (file_exists(_PS_ROOT_DIR_ . \DIRECTORY_SEPARATOR . $file)) {
                $result[] = LinkService::getBaseLink() . $file;
            }
        }

        return $result;
    }

    /**
     * Set values for the inputs. Define if they are multi-language.
     *
     * @return array<string, bool>
     */
    public static function getFieldValues(): array
    {
        return [
            'PP_DEBUG_PROFILING' => false,
            'PP_CART_TABLE_CLEANER' => false,
            'PP_CART_RULE_TABLE_CLEANER' => false,
            'PP_STATS_SEARCH_TABLE_CLEANER' => false,
            'PP_CONNECTION_TABLE_CLEANER' => false,
            'PP_PAGE_NOT_FOUND_TABLE_CLEANER' => false,
            'PP_LOG_TABLE_CLEANER' => false,
            'PP_MAIL_TABLE_CLEANER' => false,
            'PP_PRECONNECT_LINKS' => false,
            'PP_PRECONNECT_LINKS_TEXT' => false,
            'PP_PRELOAD_FONTS' => false,
            'PP_PRELOAD_FONTS_TEXT' => false,
            'PP_INSTANT_LOAD_LINK' => false,
            'PP_PAGE_CACHE' => false,
            'PP_MINIFY_HTML' => false,
            'PP_OPTIMIZE_ATTRIBUTES' => false,
            'PP_LAZY_LOAD_IMG' => false,
            'PP_LAZY_LOAD_IFRAME' => false,
            'PP_LAZY_LOAD_VIDEO' => false,
            'PP_LAZY_LOAD_FOOTER' => false,
            'PP_CONVERT_TO_WEBP_JPEG' => false,
            'PP_CONVERT_TO_WEBP_PNG' => false,
            'PP_CONVERT_JPEG_TO_WEBP_QUALITY' => false,
            'PP_CONVERT_PNG_TO_WEBP_QUALITY' => false,
            'PP_MINIFY_SVG' => false,
            'PP_IMG_SIZE' => false,
            'PP_LOAD_SCRIPT_ASYNC' => false,
            'PP_USE_PASSIVE_LISTENERS' => false,
            'PP_ADD_NOOPENER' => false,
            'PP_CSS_HTTP2_PUSH' => false,
            'PP_ORIGIN_AGENT_CLUSTER' => false,
            'PP_DECODE_IMG_ASYNC' => false,
            'PP_CLEAR_CART_TABLE' => false,
            'PP_HTACCESS_DEFLATE' => false,
            'PP_HTACCESS_CACHE_CONTROL' => false,
            'PP_LOG_EXCEPTIONS' => false,
            'PP_DISABLE_OPTIMIZATION_ORDER' => false,
            'PP_CACHE_WARMER_SITEMAPS' => false,
        ];
    }
}
