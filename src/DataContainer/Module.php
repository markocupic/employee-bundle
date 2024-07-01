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

namespace Markocupic\EmployeeBundle\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;
use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeListController;

class Module
{
    use EmployeeTrait;

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[AsCallback(table: 'tl_module', target: 'config.onload')]
    public function adjustPalette(DataContainer $dc): void
    {
        if ('edit' === Input::get('act')) {
            $model = ModuleModel::findByPk($dc->id);

            if (null !== $model) {
                if (EmployeeListController::TYPE === $model->type) {
                    if ($model->showAllPublishedEmployees) {
                        PaletteManipulator::create()
                            ->removeField('selectEmployee', 'employee_legend')
                            ->applyToPalette(EmployeeListController::TYPE, 'tl_module')
                        ;
                    }
                }
            }
        }
    }

    #[AsCallback(table: 'tl_module', target: 'fields.selectEmployee.options')]
    public function getEmployees(): array
    {
        return $this->getPublishedEmployees($this->connection);
    }
}
