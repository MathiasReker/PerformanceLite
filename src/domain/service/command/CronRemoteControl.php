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

class CronRemoteControl
{
    /**
     * @var object
     */
    private $command;

    /**
     * @var array
     */
    private $response;

    public function setCommand($command): self
    {
        $this->command = $command;

        return $this;
    }

    public function execute(): self
    {
        $this->response = $this->command->execute();

        return $this;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
