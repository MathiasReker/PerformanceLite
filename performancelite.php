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

use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\install\Disabler;
use PrestaShop\Module\PerformanceLite\install\Enabler;
use PrestaShop\Module\PerformanceLite\install\Installer;
use PrestaShop\Module\PerformanceLite\install\Uninstaller;
use PrestaShop\Module\PerformanceLite\resources\config\Config;

class PerformanceLite extends Module
{
    /**
     * @var bool
     */
    public $cron = false;

    public function __construct()
    {
        $this->name = 'performancelite';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Mathias R.';
        $this->need_instance = 0;
        $this->module_key = '';
        $this->bootstrap = true;

        parent::__construct();

        $this->autoLoad();

        $this->displayName = $this->l('Performance Lite');
        $this->description = $this->l('This module increases the overall performance of your website.');
        $this->ps_versions_compliancy = [
            'min' => '1.7.1',
            'max' => _PS_VERSION_,
        ];
    }

    /**
     * Autoload project files from /src directory.
     */
    public function autoLoad(): void
    {
        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }

    public function hookActionDispatcherBefore(): void
    {
        $this->autoLoad();
    }

    public function install(): bool
    {
        $this->setShopContextAll();

        try {
            if (!(new Installer($this))->execute() || !parent::install()) {
                $this->uninstall();

                return false;
            }
        } catch (Exception $e) {
            LogService::error($e->getMessage(), $e->getTrace());

            return false;
        }

        return true;
    }

    private function setShopContextAll(): void
    {
        if (Shop::isFeatureActive()) {
            try {
                Shop::setContext(Shop::CONTEXT_ALL);
            } catch (PrestaShopException $e) {
                LogService::error($e->getMessage(), $e->getTrace());
            }
        }
    }

    public function uninstall(): bool
    {
        $this->setShopContextAll();

        return parent::uninstall() && (new Uninstaller($this))->execute();
    }

    public function enable($force_all = true): bool
    {
        return parent::enable($force_all) && (new Enabler($this))->execute();
    }

    public function disable($force_all = true): bool
    {
        return parent::disable($force_all) && (new Disabler($this))->execute();
    }

    /**
     * Redirects the user to the admin front controller.
     */
    private function redirectToModuleAdminController(): void
    {
        $redirect = $this->context->link->getAdminLink(
            Config::CONTROLLER_NAME,
            true,
            false
        );

        Tools::redirectAdmin($redirect);
    }

    /**
     * Gets the content of the module page.
     */
    public function getContent(): void
    {
        $this->redirectToModuleAdminController();
    }

    private function renderTemplate(string $template, array $params = []): string
    {
        $id = sha1($this->name . $template . $this->context->language->id . $this->context->shop->id);

        $cacheId = $this->getCacheId($id);

        if (!$this->isCached($template, $cacheId)) {
            $this->context->smarty->assign($params);
        }

        return $this->display(__FILE__, $template, $cacheId);
    }

    public function getSuccessTemplate(string $message): string
    {
        $params = ['pp_message' => $message];

        return $this->renderTemplate('success.tpl', $params);
    }

    public function getWarningTemplate(string $message): string
    {
        $params = ['pp_message' => $message];

        return $this->renderTemplate('warning.tpl', $params);
    }

    public function hookActionAdminControllerSetMedia(): void
    {
        if (Config::CONTROLLER_NAME !== $this->context->controller->controller_name) {
            return;
        }

        $currentIndex = $this->context->link->getAdminLink(
            Config::CONTROLLER_NAME,
            true,
            false
        );

        if (!$this->active) {
            $error = $this->l('You must activate the module before running this command.');
        } else {
            $error = $this->l('An error occurred.');
        }

        Media::addJsDef([
            $this->name => [
                'moduleVersion' => $this->version,
                'cmsName' => Config::CMS_NAME,
                'cmsVersion' => _PS_VERSION_,
                'versionName' => $this->l('Version'),
                'currentIndex' => $currentIndex,
                'canceled' => $this->l('Canceled.'),
                'copy' => $this->l('Copied to clipboard.'),
                'copyError' => $this->l('Copy failed. Your browser does not allow copy.'),
                'error' => $error,
                'reset' => $this->l('Well done!'),
            ],
        ]);

        $this->context->controller->addJS(
            [
                Config::getJsLink() . 'back.js',
                Config::getJsLink() . 'menu.js',
            ]
        );

        $this->context->controller->addCSS(Config::getCssLink() . 'back.css');
    }
}
