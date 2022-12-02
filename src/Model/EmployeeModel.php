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

namespace Markocupic\EmployeeBundle\Model;

use Contao\Database;
use Contao\Model;
use Contao\Model\Collection;

class EmployeeModel extends Model
{
    protected static $strTable = 'tl_employee';

    /**
     * @param $intId
     */
    public static function findPublishedById($intId, array $arrOptions = []): ?static
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

    /**
     * Find published employee by ID or alias.
     *
     * @param string $idOrAlias
     *
     * @return EmployeeModel|null
     */
    public static function findPublishedByIdOrAlias($idOrAlias): ?static
    {
        $values = [];
        $columns = [];
        $t = static::$strTable;

        // Determine the alias condition
        if (is_numeric($idOrAlias)) {
            $columns[] = "$t.id=?";
            $values[] = (int) $idOrAlias;
        } else {
            $columns[] = "$t.alias=?";
            $values[] = $idOrAlias;
        }

        $columns[] = "$t.published=?";
        $values[] = 1;

        return static::findOneBy($columns, $values);
    }

    /**
     * @return Model|array<Model>|Collection|EmployeeModel|null
     */
    public static function findAllPublished(): ?Collection
    {
        $values = [];
        $columns = [];
        $t = static::$strTable;
        $columns[] = "$t.published=?";
        $values[] = 1;

        return static::findBy($columns, $values);
    }
}
