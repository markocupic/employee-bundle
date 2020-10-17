<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Model;

use Contao\Database;
use Contao\Model;
use Contao\Model\Collection;

/**
 * Class EmployeeModel.
 */
class EmployeeModel extends Model
{
    protected static $strTable = 'tl_employee';

    /**
     * @param $intId
     *
     * @return static|null
     */
    public static function findPublishedById($intId, array $arrOptions = []): ?self
    {
        $t = static::$strTable;
        $arrColumns = [
            "$t.id=?",
            "published='1'",
        ];

        return static::findOneBy($arrColumns, $intId, $arrOptions);
    }

    public static function findMultipleAndPublishedByIds(array $arrIds, array $arrOptions = []): ?Collection
    {
        if (\count($arrIds) < 1) {
            return null;
        }

        $t = static::$strTable;

        $objDb = Database::getInstance()
            ->prepare('SELECT id FROM '.$t.' WHERE published=? AND id IN ('.implode(',', $arrIds).')')
            ->execute('1')
        ;
        $arrIds = $objDb->fetchEach('id');

        return static::findMultipleByIds($arrIds, $arrOptions);
    }
}
