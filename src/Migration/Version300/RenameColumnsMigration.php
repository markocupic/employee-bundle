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

namespace Markocupic\EmployeeBundle\Migration\Version300;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class RenameColumnsMigration extends AbstractMigration
{
    private const ALTERATION_TYPE_RENAME_COLUMN = 'alteration_type_rename_column';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getName(): string
    {
        return 'Employee Bundle version 3.0.0 update';
    }

    public function shouldRun(): bool
    {
        $doMigration = false;
        $schemaManager = $this->connection->getSchemaManager();
        $arrAlterations = $this->getAlterationData();

        foreach ($arrAlterations as $arrAlteration) {
            $type = $arrAlteration['type'];

            // Version 2 migration: "Rename columns"
            if (self::ALTERATION_TYPE_RENAME_COLUMN === $type) {
                $strTable = $arrAlteration['table'];
                // If the database table itself does not exist we should do nothing
                if ($schemaManager->tablesExist([$strTable])) {
                    $columns = $schemaManager->listTableColumns($strTable);

                    if (isset($columns[strtolower($arrAlteration['old'])]) && !isset($columns[strtolower($arrAlteration['new'])])) {
                        $doMigration = true;
                    }
                }
            }
        }

        // Rename content type
        if ($schemaManager->tablesExist(['tl_employee'])) {
            $columns = $schemaManager->listTableColumns('tl_employee');

            if (isset($columns['interview'])) {
                if ($this->connection->fetchOne("SELECT id FROM tl_employee WHERE interview LIKE '%\"interview_question\";%' OR interview LIKE '%\"interview_answer\";%'")) {
                    $doMigration = true;
                }
            }

            if (isset($columns['businesshours'])) {
                if ($this->connection->fetchOne("SELECT id FROM tl_employee WHERE businessHours LIKE '%\"businessHoursWeekday\";%' OR interview LIKE '%\"businessHoursTime\";%'")) {
                    $doMigration = true;
                }
            }
        }

        return $doMigration;
    }

    /**
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $resultMessages = [];

        $schemaManager = $this->connection->getSchemaManager();
        $arrAlterations = $this->getAlterationData();

        foreach ($arrAlterations as $arrAlteration) {
            $type = $arrAlteration['type'];

            // Version 2 migration: "Rename columns"
            if (self::ALTERATION_TYPE_RENAME_COLUMN === $type) {
                $strTable = $arrAlteration['table'];

                if ($schemaManager->tablesExist([$strTable])) {
                    $columns = $schemaManager->listTableColumns($strTable);

                    if (isset($columns[strtolower($arrAlteration['old'])]) && !isset($columns[strtolower($arrAlteration['new'])])) {
                        $strQuery = sprintf(
                            'ALTER TABLE `%s` CHANGE `%s` `%s` %s',
                            $strTable,
                            $arrAlteration['old'],
                            $arrAlteration['new'],
                            $arrAlteration['sql'],
                        );

                        $this->connection->executeQuery($strQuery);

                        $resultMessages[] = sprintf(
                            'Rename column %s.%s to %s.%s. ',
                            $strTable,
                            $arrAlteration['old'],
                            $strTable,
                            $arrAlteration['new'],
                        );
                    }
                }
            }
        }

        // Rename content type
        if ($schemaManager->tablesExist(['tl_employee'])) {
            $columns = $schemaManager->listTableColumns('tl_employee');

            if (isset($columns['interview'])) {
                $result = $this->connection->executeQuery("SELECT id,interview FROM tl_employee WHERE interview LIKE '%\"interview_question\";%' OR interview LIKE '%\"interview_answer\";%'");

                while (false !== ($row = $result->fetchAssociative())) {
                    $arrInterview = StringUtil::deserialize($row['interview']);
                    $arrNew = [];

                    if (!empty($arrInterview)) {
                        foreach ($arrInterview as $item) {
                            $arrNew[] = [
                                'question' => $item['interview_question'],
                                'answer' => $item['interview_answer'],
                            ];
                        }
                    }

                    $set = ['interview' => serialize($arrNew)];

                    $this->connection->update('tl_employee', $set, ['id' => $row['id']]);
                }
            }

            if (isset($columns['businesshours'])) {
                $result = $this->connection->executeQuery("SELECT id,businessHours FROM tl_employee WHERE businessHours LIKE '%\"businessHoursWeekday\";%' OR businessHours LIKE '%\"businessHoursTime\";%'");

                while (false !== ($row = $result->fetchAssociative())) {
                    $arrInterview = StringUtil::deserialize($row['businessHours']);
                    $arrNew = [];

                    if (!empty($arrInterview)) {
                        foreach ($arrInterview as $item) {
                            $arrNew[] = [
                                'weekday' => $item['businessHoursWeekday'],
                                'time' => $item['businessHoursTime'],
                            ];
                        }
                    }

                    $set = ['businessHours' => serialize($arrNew)];

                    $this->connection->update('tl_employee', $set, ['id' => $row['id']]);
                }
            }
        }

        return $this->createResult(true, $resultMessages ? implode("\n", $resultMessages) : null);
    }

    private function getAlterationData(): array
    {
        return [
            [
                'type' => self::ALTERATION_TYPE_RENAME_COLUMN,
                'table' => 'tl_employee',
                'old' => 'funktion',
                'new' => 'role',
                'sql' => 'varchar(255)',
            ],
            [
                'type' => self::ALTERATION_TYPE_RENAME_COLUMN,
                'table' => 'tl_employee',
                'old' => 'description',
                'new' => 'roleDetail',
                'sql' => 'mediumtext',
            ],
        ];
    }
}
