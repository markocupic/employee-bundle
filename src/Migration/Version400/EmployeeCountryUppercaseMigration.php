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

namespace Markocupic\EmployeeBundle\Migration\Version400;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * @internal
 */
class EmployeeCountryUppercaseMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'Employee Bundle version 4.0.0 update: Emplyee country uppercase migration';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_employee'])) {
            return false;
        }

        if (!isset($schemaManager->listTableColumns('tl_employee')['country'])) {
            return false;
        }

        $test = $this->connection->fetchOne('SELECT TRUE FROM tl_employee WHERE BINARY country!=BINARY UPPER(country) LIMIT 1');

        return false !== $test;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement('UPDATE tl_employee SET country=UPPER(country) WHERE BINARY country!=BINARY UPPER(country)');

        return $this->createResult(true);
    }
}
