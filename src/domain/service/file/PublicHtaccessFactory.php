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

namespace PrestaShop\Module\PerformanceLite\domain\service\file;

use Configuration;

class PublicHtaccessFactory
{
    private $editHtaccess;

    public function __construct($editHtaccess)
    {
        $this->editHtaccess = $editHtaccess;
    }

    public function create(): void
    {
        $cacheControl = Configuration::get('PP_HTACCESS_CACHE_CONTROL');
        $deflate = Configuration::get('PP_HTACCESS_DEFLATE');

        if ($cacheControl || $deflate) {
            $this->editHtaccess->setContent('<IfModule mod_headers.c>');

            if ($cacheControl) {
                $this->editHtaccess->setContent('    <FilesMatch "\\.(ttf|woff2?|css|js|gif|png|jpe?g|webp|ico|svgz?|pdf)$">
    Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>');
            }

            if ($deflate) {
                $this->editHtaccess->setContent('    <FilesMatch "\\.(ttf|woff2?|css|js|xml|gz|html)$">
    Header append Vary: Accept-Encoding
    </FilesMatch>');
            }

            $this->editHtaccess->setContent('</IfModule>');

            if ($deflate) {
                $this->editHtaccess->setContent('<IfModule mod_deflate.c>
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \\.(?:gif|png|jpe?g|webp)$ no-gzip dont-vary
    Header append Vary User-Agent env=!dont-vary
</IfModule>');
            }

            $this->editHtaccess->replaceContent();
        } else {
            $this->editHtaccess->reset();
        }
    }
}
