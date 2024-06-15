<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Migration\Version400;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * @internal
 */
class EmployeeContentElementMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function getName(): string
    {
        return 'Employee Bundle version 4.0.0 update: Create for each employee a standalone content element.';
    }

    /**
     * Disabled!
     *
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        return false; // Disabled

        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        if (!isset($schemaManager->listTableColumns('tl_content')['selectemployee'])) {
            return false;
        }

        $test = $this->connection->fetchOne('SELECT id FROM tl_content WHERE selectEmployee LIKE "a:%"');

        return false !== $test;
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $this->connection->beginTransaction();

        try {
            $dataAll = $this->connection->fetchAllAssociative('SELECT * FROM tl_content WHERE selectEmployee LIKE "a:%"');

            foreach ($dataAll as $set) {
                $arrIds = StringUtil::deserialize($set['selectEmployee'], true);

                if (!isset($arrIds[0]) || !is_numeric($arrIds[0])) {
                    continue;
                }

                $model = ContentModel::findByPk($set['id']);

                if (null !== $model) {
                    $model->selectEmployee = $arrIds[0];
                    $model->save();
                }

                unset($set['id']);

                $i = 1;

                while (true === isset($arrIds[$i])) {
                    $model = new ContentModel();
                    $model->setRow($set);
                    $model->selectEmployee = $arrIds[$i];
                    $model->sorting = $set['sorting'] + $i;
                    $model->save();

                    ++$i;
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }

        return $this->createResult(true);
    }
}
