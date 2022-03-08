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

use PrestaShop\Module\PerformanceLite\domain\service\file\PublicHtaccessFactory;
use PrestaShop\Module\PerformanceLite\domain\service\file\PublicHtaccessFile;
use PrestaShop\Module\PerformanceLite\domain\service\form\Form;
use PrestaShop\Module\PerformanceLite\domain\service\form\FormValidate;
use PrestaShop\Module\PerformanceLite\domain\service\log\LogService;
use PrestaShop\Module\PerformanceLite\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformanceLite\exception\PerformanceLiteDefineValueException;
use PrestaShop\Module\PerformanceLite\resources\config\Field;
use PrestaShop\Module\PerformanceLite\web\form\CacheForm;
use PrestaShop\Module\PerformanceLite\web\form\DashboardForm;
use PrestaShop\Module\PerformanceLite\web\form\DatabaseAnalyticsForm;
use PrestaShop\Module\PerformanceLite\web\form\HelpForm;
use PrestaShop\Module\PerformanceLite\web\form\HtmlOptimizationForm;
use PrestaShop\Module\PerformanceLite\web\form\ImageOptimizationForm;
use PrestaShop\Module\PerformanceLite\web\form\LazyLoadingForm;
use PrestaShop\Module\PerformanceLite\web\form\ModuleAnalyticsForm;
use PrestaShop\Module\PerformanceLite\web\form\PageOptimizationForm;
use PrestaShop\Module\PerformanceLite\web\form\ProfilerForm;
use PrestaShop\Module\PerformanceLite\web\form\ResourceLoadingForm;
use PrestaShop\Module\PerformanceLite\web\form\ServerAnalyticsForm;
use PrestaShop\Module\PerformanceLite\web\form\SystemAnalyticsForm;
use PrestaShop\Module\PerformanceLite\web\form\ToolsConfigurationForm;

class AdminPerformanceLiteController extends ModuleAdminController
{
    private const SUBMIT_NAME = 'submitConfig';

    /**
     * @var array
     */
    private $customErrors = [];

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
    }

    public function renderList(): string
    {
        $result = '';

        if (Tools::isSubmit(self::SUBMIT_NAME)) {
            if ($this->submitInputData()) {
                (new PublicHtaccessFactory(new PublicHtaccessFile()))->create();
                $result .= $this->displaySaveNotification();
                $result .= $this->displayErrorNotification();
            } else {
                $result .= $this->displayDemoNotification();
            }
        }

        $result .= $this->renderAdminForm();

        return $result;
    }

    private function submitInputData(): bool
    {
        $languages = Language::getLanguages(false);

        foreach (Field::getFieldValues() as $key => $multiLang) {
            if ($multiLang) {
                $fields = [];
                foreach ($languages as $lang) {
                    $idLang = $lang['id_lang'];
                    $field = (new FormValidate($key, (string) Tools::getValue($key . '_' . $idLang)))
                        ->validate()
                        ->getResponse();

                    $this->setError($field['error'] ?: '');
                    $fields[$idLang] = $field['result'];
                }
                Configuration::updateValue($key, json_encode($fields));
            } else {
                $field = (new FormValidate($key, (string) Tools::getValue($key)))
                    ->validate()
                    ->getResponse();

                $this->setError($field['error'] ?: '');
                Configuration::updateValue($key, $field['result']);
            }
        }

        $this->defineGlobalValues();

        return true;
    }

    private function setError(string $text): void
    {
        if (!empty($text)) {
            $this->customErrors[] = $text;
        }
    }

    private function defineGlobalValues(): void
    {
        $value = Configuration::get('PP_DEBUG_PROFILING') ? 'true' : 'false';

        try {
            (new DefineValueService())->updateValue('_PS_DEBUG_PROFILING_', $value);
        } catch (PerformanceLiteDefineValueException $e) {
            LogService::error($e->getMessage(), $e->getTrace());
        }
    }

    public function displayDemoNotification(): string
    {
        $error = $this->module->l('The configuration has been disabled in the demo mode.');

        return $this->module->getSuccessTemplate($error);
    }

    public function displaySaveNotification(): string
    {
        $error = $this->module->l('Settings saved.');

        return $this->module->getSuccessTemplate($error);
    }

    private function displayErrorNotification(): string
    {
        $result = '';

        foreach ($this->customErrors as $error) {
            $result .= $this->module->getWarningTemplate($error);
        }

        return $result;
    }

    private function renderAdminForm(): string
    {
        $forms = [
            (new DashboardForm($this->module))->getFields(),
            (new ToolsConfigurationForm($this->module))->getFields(),
            (new ResourceLoadingForm($this->module))->getFields(),
            (new CacheForm($this->module))->getFields(),
            (new LazyLoadingForm($this->module))->getFields(),
            (new PageOptimizationForm($this->module))->getFields(),
            (new HtmlOptimizationForm($this->module))->getFields(),
            (new ImageOptimizationForm($this->module))->getFields(),
            (new SystemAnalyticsForm($this->module))->getFields(),
            (new ModuleAnalyticsForm($this->module))->getFields(),
            (new ServerAnalyticsForm($this->module))->getFields(),
            (new DatabaseAnalyticsForm($this->module))->getFields(),
            (new ProfilerForm($this->module))->getFields(),
            (new HelpForm($this->module))->getFields(),
        ];

        return (new Form())->render($forms, self::SUBMIT_NAME);
    }
}
