<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\EventListener\ContaoHooks\ReplaceInsertTags;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\StringUtil;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

#[AsHook(ReplaceEmployeeListener::HOOK, priority: 100)]
class ReplaceEmployeeListener
{
    public const HOOK = 'replaceInsertTags';
    private InsertTagParser $insertTagParser;

    public function __construct(InsertTagParser $insertTagParser)
    {
        $this->insertTagParser = $insertTagParser;
    }

    public function __invoke(string $insertTag, bool $useCache, string $cachedValue, array $flags, array $tags, array $cache, int $_rit, int $_cnt)
    {
        if (!str_starts_with($insertTag, 'employee')) {
            return false;
        }

        $parts = StringUtil::trimsplit('::', $insertTag);

        if (\count($parts) < 2) {
            return false;
        }

        $identifier = $parts[1] ?? null;
        $strField = $parts[2] ?? null;

        if (empty($identifier) || empty($strField)) {
            return false;
        }

        if (null === ($model = EmployeeModel::findByIdOrAlias($identifier))) {
            return false;
        }

        $row = $model->row();

        switch ($strField) {
            // Get the image/picture/figure html markup:
            case 'picture':
            case 'image':
            case 'figure':
                // Usage: {{employee::##emloyee_alias##::picture::size=2&alt=portrait}}
                $strPictureAttr = !empty($parts[3]) ? ltrim($parts[3], '?') : '';

                return $this->getPictureHtml($model, $strField, $strPictureAttr);

            // Get the field value:
            default:

                if (isset($row[$strField])) {
                    // Usage: {{employee::##emloyee_alias##firstname}} or {{employee::##emloyee_alias##role}}, etc.
                    return $row[$strField];
                }
        }

        return false;
    }

    private function getPictureHtml(EmployeeModel $model, string $strField, $strPictureAttr): string
    {
        return $this->insertTagParser->replaceInline(
            sprintf(
                '{{%s::%s?%s}}',
                $strField,
                StringUtil::binToUuid($model->singleSRC),
                $strPictureAttr,
            )
        );
    }
}
