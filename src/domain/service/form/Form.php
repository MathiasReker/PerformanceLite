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

namespace PrestaShop\Module\PerformanceLite\domain\service\form;

use Configuration;
use HelperForm;
use Language;
use PrestaShop\Module\PerformanceLite\domain\service\util\ContextService;
use PrestaShop\Module\PerformanceLite\resources\config\Config;
use PrestaShop\Module\PerformanceLite\resources\config\Field;
use PrestaShop\Module\PerformanceLite\web\util\View;
use Tools;

class Form
{
    public function render(array $forms, string $submitName): string
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = ContextService::getLanguage()->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->name_controller = Config::CONTROLLER_NAME;
        $helper->submit_action = $submitName;
        $helper->currentIndex = ContextService::getLink()->getAdminLink(Config::CONTROLLER_NAME, false, false);
        $helper->token = Tools::getAdminTokenLite(Config::CONTROLLER_NAME);

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => ContextService::getController()->getLanguages(),
            'id_language' => ContextService::getLanguage()->id,
        ];

        return $this->upgradeToPerformancePro() . $helper->generateForm($forms);
    }

    private function upgradeToPerformancePro(): string
    {
        $readMore = View::displayLink('https://addons.prestashop.com/en/website-performance/86977-performance-pro-all-in-one.html', 'read more.');

        return '<div class="alert alert-info">You can unlock all the features by purchacing Performance Pro. ' . $readMore . '</div>';
    }

    /**
     * @return array<string,mixed>
     */
    private function getConfigFormValues(): array
    {
        $languages = Language::getLanguages(false);
        $result = [];

        foreach (Field::getFieldValues() as $key => $multiLang) {
            if ($multiLang) {
                $confKey = Configuration::get($key);
                if ($confKey) {
                    $fields = (array) json_decode($confKey, true);
                    foreach ($languages as $lang) {
                        $idLang = $lang['id_lang'];
                        $result[$key][$idLang] = $fields[$idLang];
                    }
                }
            } else {
                $result[$key] = Configuration::get($key);
            }
        }

        return $result;
    }
}
