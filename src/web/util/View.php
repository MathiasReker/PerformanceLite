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

namespace PrestaShop\Module\PerformanceLite\web\util;

use Module;
use PrestaShop\Module\PerformanceLite\domain\service\util\ContextService;
use PrestaShop\Module\PerformanceLite\domain\service\util\LinkService;
use Tools;

class View
{
    public static function displayArrayAsTable(array $array, bool $table = true, bool $top = false): string
    {
        $result = [];
        if ($top) {
            $result[] = '<div style="padding-top: 20px"></div>';
        }
        $tableHeader = '';
        foreach ($array as $value) {
            if (\is_array($value)) {
                if ('' === $tableHeader) {
                    $tableHeader = sprintf(
                        '<th><strong>%s</strong></th>',
                        implode(
                            '</strong></th><th><strong>',
                            array_keys($value)
                        )
                    );
                }
                $result[] = '<tr>' . self::displayArrayAsTable($value, false) . '</tr>';
            } else {
                $result[] = '<td height="30">' . $value . '</td>';
            }
        }

        if ($table) {
            return sprintf(
                '<table class="table"><thead><tr>%s</tr></thead>%s</table>',
                $tableHeader,
                implode('', $result)
            );
        }

        return implode('', $result);
    }

    public static function displayHeader(string $text, bool $noTop = false): string
    {
        if ($noTop) {
            return '<h2 style="margin-top: -10px">' . $text . '</h2>';
        }

        return '<h2>' . $text . '</h2>';
    }

    public static function displayParagraph(string $text, bool $italic = false): string
    {
        if ($italic) {
            return '<p style="font-size: 13px; font-style: italic;">'
                . $text
                . '</p>';
        }

        return '<p style="font-size: 13px;">' . $text . '</p>';
    }

    /**
     * @param array<string> $array
     */
    public static function displayList(array $array, string $class = ''): string
    {
        return '<ul class="'
            . $class
            . '"><li>'
            . implode('</li><li>', $array)
            . '</li></ul>';
    }

    public static function displayBtnLink(string $link, string $href): string
    {
        return '<a class="btn btn-default" href="'
            . $href
            . '" target="_blank" rel="noopener noreferrer nofollow">'
            . $link
            . '</a>';
    }

    public static function displayLinkCopy(string $text): string
    {
        return '<span style="white-space:nowrap;"><kbd>'
            . $text
            . '</kbd><a href="javascript:void(0)" onclick="copyToClipboard(\''
            . $text
            . '\')" <i class="icon icon-clipboard"></i></a></span>';
    }

    public static function displayLabelInfo(string $text): string
    {
        return '<span class="label label-info">' . $text . '</span>';
    }

    public static function displayLabelDanger(string $text): string
    {
        return '<span class="label label-danger">' . $text . '</span>';
    }

    public static function displayLabelSuccess(string $text): string
    {
        return '<span class="label label-success">' . $text . '</span>';
    }

    public static function displayAlign(string $text): string
    {
        if (ContextService::getLanguage()->is_rtl) {
            $align = 'left';
        } else {
            $align = 'right';
        }

        return '<span style="float:' . $align . ';white-space:nowrap;">' . $text . '</span>';
    }

    public static function displayMagicIcon(): string
    {
        return '<i class="icon icon-magic"></i>';
    }

    public static function displayBoltIcon(): string
    {
        return '<i class="icon icon-bolt"></i>';
    }

    public static function displayInformationIcon(): string
    {
        return '<i class="icon icon-info-circle"></i>';
    }

    public static function displayWarningIcon(): string
    {
        return '<i class="icon icon-warning"></i>';
    }

    public static function displayBtnAjax(string $technicalName, string $displayName, string $confMsg, $key = null): string
    {
        $link = LinkService::createCronLink($technicalName, $key, true);

        $id = Tools::hashIV($technicalName . $key);

        return '<button id="ajaxCall-'
            . $id
            . '" class="btn btn-default" onclick="callAjax(\''
            . $link
            . '\', \''
            . $id
            . '\', \''
            . $confMsg
            . '\'); return false;" id="'
            . $id
            . '">'
            . $displayName
            . '</button>';
    }

    public static function displayLink(string $href, $link = null, bool $target = true): string
    {
        if (null === $link) {
            $link = $href;
        }

        if ($target) {
            $blank = 'target="_blank"';
        } else {
            $blank = '';
        }

        return '<a style="white-space:nowrap;" href="'
            . $href
            . '"'
            . $blank
            . ' rel="noopener noreferrer nofollow"><i class="icon-external-link-sign"></i> '
            . $link
            . '</a>';
    }

    public static function displayMonospaceLink(string $text, bool $copy = false): string
    {
        $result = '<span style="white-space:nowrap;">';

        $result .= '<kbd>' . $text . '</kbd>';

        if ($copy) {
            $result
                .= ' <a href="javascript:void(0)" onclick="copyToClipboard(\''
                . $text
                . '\')" <i class="icon icon-clipboard"></i></a>';
        }

        $result .= '</span>';

        return $result;
    }

    public static function formatStrong(string $text): string
    {
        return '<strong>' . $text . '</strong>';
    }

    public static function arrayToStringList(array $array): string
    {
        return '<ul><li>' . implode('</li><li>', $array) . '</li></ul>';
    }

    public static function filterModules(array $modules): array
    {
        $result = [];

        foreach ($modules as $module) {
            if (Module::isEnabled($module)) {
                $result[] = Module::getModuleName($module) . ' (' . $module . ')';
            }
        }

        return $result;
    }

    public static function displayProTag()
    {
        return '<strong style="color: #25b9d7">PRO FEATURE</strong>';
    }
}
