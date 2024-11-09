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

namespace Markocupic\EmployeeBundle\Migration\Version300;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;

class RenameContentElementTypeMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function getName(): string
    {
        return 'Employee Bundle version 3.0.0 update: Rename content element type';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        if (!isset($columns['type'])) {
            return false;
        }

        $id = $this->connection->fetchOne('SELECT id FROM tl_content WHERE type = ?', ['employeeDetail'], [Types::STRING]);

        return !(false === $id);
    }

    /**
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $set = [
            'type' => 'employee_detail',
        ];

        $this->connection->update('tl_content', $set, ['type' => 'employeeDetail']);

        return $this->createResult(true);
    }
}
