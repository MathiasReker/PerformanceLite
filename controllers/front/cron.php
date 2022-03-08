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

use PrestaShop\Module\PerformanceLite\data\repository\DatabaseCleanerRepository;
use PrestaShop\Module\PerformanceLite\data\repository\DatabaseOptimizerRepository;
use PrestaShop\Module\PerformanceLite\domain\service\cache\CacheWarmer;
use PrestaShop\Module\PerformanceLite\domain\service\cache\ClearCache;
use PrestaShop\Module\PerformanceLite\domain\service\command\CacheWarmerCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ChangeEngineToInnoDbCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\CleanTablesCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearApcCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearConnectionTablesCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearLogsCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearLogTableCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearMailTableCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearMediaCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearOpCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearPageNotFoundCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearStatsSearchTableCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ClearXmlCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ConfigurationUpdateCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ConfigurationUpdateConfigValueCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\CronRemoteControl;
use PrestaShop\Module\PerformanceLite\domain\service\command\FlushQueryCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\GetPreConnectLinksCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\GetPrefetchLinkCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\OptimizeTablesCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\RepairTablesCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\ResetQueryCacheCommand;
use PrestaShop\Module\PerformanceLite\domain\service\command\UpdateCurrentSettingCommand;
use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseConfiguration;
use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseOptimizer;
use PrestaShop\Module\PerformanceLite\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\domain\service\provider\GooglePageSpeedProvider;
use PrestaShop\Module\PerformanceLite\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformanceLite\resources\config\Config;
use PrestaShop\Module\PerformanceLite\web\util\View;

class PerformanceLiteCronModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    /**
     * @var CronRemoteControl
     */
    private $remote;

    /**
     * @var string
     */
    private $className;

    public function __construct()
    {
        $this->remote = new CronRemoteControl();
        $this->className = 'cron';

        parent::__construct();
    }

    public function displayAjax(): void
    {
        header('Access-Control-Allow-Origin: *');

        $this->verifyAccess('ajax');

        $content = json_encode($this->runAjax());

        try {
            $this->ajaxRender($content);
        } catch (PrestaShopException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
        }
    }

    private function verifyAccess(string $key): void
    {
        if (!Tools::isPHPCLI()) {
            $token = Tools::hashIV(Config::MODULE_NAME . '/' . $key . Tools::getValue('name'));
            $isValidToken = $token !== Tools::getValue('token');
            $isModuleInstalled = Module::isInstalled(Config::MODULE_NAME);

            if ($isValidToken || !$isModuleInstalled) {
                exit($this->module->l('Forbidden call.', $this->className));
            }
        }
    }

    private function runAjax(): array
    {
        $name = (string) Tools::getValue('name');
        $key = (string) Tools::getValue('key');

        return $this->caller($name, $key);
    }

    private function caller(string $name, string $key): array
    {
        $name = $this->dashesToCamelCase($name);

        if (method_exists($this, $name)) {
            return $this->$name($key);
        }

        return [
            'result' => $this->module->l('The command does not exist.', $this->className),
        ];
    }

    private function dashesToCamelCase(string $string): string
    {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    public function display(): void
    {
        $this->runAsCron();
    }

    private function runAsCron(): void
    {
        $this->module->cron = true;

        $this->verifyAccess('cron');

        $stopTime = $this->startTime();
        $this->runAjax();
        $executionTime = $stopTime();

        try {
            $this->ajaxRender(
                sprintf(
                    $this->module->l('%s Total execution time: %s sec.', $this->className),
                    'Success.',
                    $executionTime
                )
            );
        } catch (PrestaShopException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
        }
    }

    private function startTime(): Closure
    {
        $startTime = microtime(true);

        return static function () use ($startTime) {
            return number_format(microtime(true) - $startTime, 2);
        };
    }

    public function formatStrong(string $text): string
    {
        return $text;
    }

    private function buildCache(): array
    {
        $sitemaps = [Configuration::get('PP_CACHE_WARMER_SITEMAPS')];

        $response = $this->remote
            ->setCommand(new CacheWarmerCommand(new CacheWarmer($sitemaps)))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf($this->module->l('%s pages warmed up.', $this->className), View::formatStrong((string) $response['amount'])),
        ];
    }

    private function clearSmartyCacheAndSfCache(): array
    {
        $this->remote
            ->setCommand(new ClearCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Smarty- and SF-cache cleared.', $this->className),
        ];
    }

    private function clearImageCache(): array
    {
        return $this->proFeature();
    }

    private function clearHttpCache(): array
    {
        return $this->proFeature();
    }

    private function proFeature(): array
    {
        return [
            'result' => $this->module->l('This is a PRO feature.', $this->className),
        ];
    }

    private function clearMediaCache(): array
    {
        $this->remote
            ->setCommand(new ClearMediaCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Theme cache cleared.', $this->className),
        ];
    }

    private function clearXmlCache(): array
    {
        $this->remote
            ->setCommand(new ClearXmlCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('XML cache cleared.', $this->className),
        ];
    }

    private function clearOpCache(): array
    {
        $this->remote
            ->setCommand(new ClearOpCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('OP cache cleared.', $this->className),
        ];
    }

    private function clearApcCache(): array
    {
        $this->remote
            ->setCommand(new ClearApcCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Theme cache cleared.', $this->className),
        ];
    }

    private function clearLogs(): array
    {
        $response = $this->remote
            ->setCommand(new ClearLogsCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s log(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function cleanTables(): array
    {
        $response = $this->remote
            ->setCommand(new CleanTablesCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) fixed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearCartTable(): array
    {
        return $this->proFeature();
    }

    private function clearStatsSearchTable(): array
    {
        $response = $this->remote
            ->setCommand(new ClearStatsSearchTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearLogTable(): array
    {
        $response = $this->remote
            ->setCommand(new ClearLogTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearMailTable(): array
    {
        $response = $this->remote
            ->setCommand(new ClearMailTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearPageNotFoundTable(): array
    {
        $response = $this->remote
            ->setCommand(new ClearPageNotFoundCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearConnectionTables(): array
    {
        $response = $this->remote
            ->setCommand(new ClearConnectionTablesCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function resetQueryCache(): array
    {
        $this->remote
            ->setCommand(new ResetQueryCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Query cache resat.', $this->className),
        ];
    }

    private function flushQueryCache(): array
    {
        $this->remote
            ->setCommand(new FlushQueryCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Query cache flushed.', $this->className),
        ];
    }

    private function updateDbValue(string $key): array
    {
        $response = $this->remote
            ->setCommand(new UpdateCurrentSettingCommand(new DatabaseSettings(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('The setting %s has been updated to %s.', $this->className),
                View::formatStrong($key),
                View::formatStrong((string) $response['value'])
            ),
            'amount' => $response['value'],
        ];
    }

    private function getPrefetchLink(): array
    {
        $response = $this->remote
            ->setCommand(new GetPrefetchLinkCommand(new GooglePageSpeedProvider()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s link(s) has been added.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
            'content' => $response['content'],
        ];
    }

    private function changeEngineToInnodb(): array
    {
        $response = $this->remote
            ->setCommand(new ChangeEngineToInnoDbCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been converted to InnoDb.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function repairTables(): array
    {
        $response = $this->remote
            ->setCommand(new RepairTablesCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been repaired.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function optimizeTables(): array
    {
        $response = $this->remote
            ->setCommand(new OptimizeTablesCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been optimized.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    private function clearCartRuleTable(): array
    {
        return $this->proFeature();
    }

    private function deleteEmptyImagesFolder(): array
    {
        return $this->proFeature();
    }

    private function deleteExpiredSpecificPrices(): array
    {
        return $this->proFeature();
    }

    private function deleteBrokenImages(): array
    {
        return $this->proFeature();
    }

    private function clearImageTmpDir(): array
    {
        return $this->proFeature();
    }

    private function deleteUnusedImages(): array
    {
        return $this->proFeature();
    }

    private function getPreConnectLinks(): array
    {
        $response = $this->remote
            ->setCommand(new GetPreConnectLinksCommand(new GooglePageSpeedProvider()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s link(s) has been added.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
            'content' => $response['content'],
        ];
    }

    private function configurationUpdate(string $key): array
    {
        $this->remote
            ->setCommand(new ConfigurationUpdateCommand(new DatabaseConfiguration(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('The setting has been updated.', $this->className),
        ];
    }

    private function configurationUpdateConfigValue(string $key): array
    {
        $this->remote
            ->setCommand(new ConfigurationUpdateConfigValueCommand(new DefineValueService(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('The setting has been updated.', $this->className),
        ];
    }
}
