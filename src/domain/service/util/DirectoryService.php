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

namespace PrestaShop\Module\PerformanceLite\domain\service\util;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tools;

class DirectoryService
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var float|int
     */
    private $size;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function calcDirectorySize(): self
    {
        $result = 0;

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            $result += $file->getSize();
        }
        $this->size = $result;

        return $this;
    }

    public function getAsBytes(): string
    {
        return Tools::formatBytes($this->size);
    }

    public function countFilesInDirectory(): int
    {
        return iterator_count(new FilesystemIterator($this->path, FilesystemIterator::SKIP_DOTS));
    }
}
