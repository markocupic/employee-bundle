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

namespace Markocupic\EmployeeBundle\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;

class Employee
{
    private Connection $connection;
    private Slug $slug;

    public function __construct(Connection $connection, Slug $slug)
    {
        $this->connection = $connection;
        $this->slug = $slug;
    }

    /**
     * @Callback(table="tl_employee", target="fields.alias.save")
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $aliasExists = fn (string $alias): bool => false !== $this->connection->fetchOne('SELECT id FROM tl_employee WHERE alias = ? AND id != ?', [$alias, $dc->id]);

        // Generate alias if there is none
        if (!$varValue) {
            $varValue = $this->slug->generate($dc->activeRecord->firstname.' '.$dc->activeRecord->lastname, [], $aliasExists);
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }
}
