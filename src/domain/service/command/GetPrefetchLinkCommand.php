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

use Exception;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\domain\service\provider\GooglePageSpeedProvider;
use PrestaShop\Module\PerformanceLite\domain\service\util\LinkService;

class GetPrefetchLinkCommand implements Command
{
    /**
     * @var GooglePageSpeedProvider
     */
    private $output;

    public function __construct(GooglePageSpeedProvider $output)
    {
        $this->output = $output;
    }

    /**
     * @return array{result: bool, amount: int, content: string}
     */
    public function execute(): array
    {
        try {
            $results = $this->output
                ->setUrl(LinkService::getBaseLink())
                ->getFontDisplay();
            $amount = \count($results);
            $content = implode('|', $results);
        } catch (Exception $e) {
            LogService::error($e->getMessage(), $e->getTrace());
            $amount = 0;
            $content = '';
        }

        return [
            'amount' => $amount,
            'content' => $content,
        ];
    }
}
