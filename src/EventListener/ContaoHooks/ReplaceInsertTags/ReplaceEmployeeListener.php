<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
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
        if (0 === strpos($insertTag, 'employee')) {
            $parts = StringUtil::trimsplit('::', $insertTag);

            if (1 === \count($parts)) {
                return false;
            }

            $id = $parts[1] ?? null;
            $strField = $parts[2] ?? null;

            if (!empty($id) && !empty($strField)) {
                if (null === ($model = EmployeeModel::findByIdOrAlias($id))) {
                    return false;
                }

                $arrEmployee = $model->row();

                // {{employee::#emloyee_alias##::picture::size=2&alt=portrait}}
                if ('picture' === $strField || 'image' === $strField || 'figure' === $strField) {
                    $pictureParams = isset($parts[3]) && !empty($parts[3]) ? ltrim($parts[3], '?') : '';

                    return $this->insertTagParser->replaceInline(
                        sprintf(
                            '{{%s::%s?%s}}',
                            $strField,
                            StringUtil::binToUuid($model->singleSRC),
                            $pictureParams
                        )
                    );
                }

                if (isset($arrEmployee[$strField])) {
                    // {{employee::#emloyee_alias##firstname}} or {{employee::#emloyee_alias##role}}, etc.
                    return $arrEmployee[$strField];
                }
            }
        }

        return false;
    }
}
