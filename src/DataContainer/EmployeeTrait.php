<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\DataContainer;

use Doctrine\DBAL\Connection;

trait EmployeeTrait{
    protected function getPublishedEmployees(Connection $connection){
        $opt = [];

        $rows = $connection->fetchAllAssociative(
            'SELECT * FROM tl_employee WHERE published = ? ORDER BY lastname, firstname',
            [1],
        );

        foreach ($rows as $row) {
            $strRole = '' !== $row['role'] ? ' ('.$row['role'].')' : '';
            $opt[$row['id']] = $row['firstname'].' '.$row['lastname'].$strRole;
        }

        return $opt;
    }

}
