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

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Doctrine\DBAL\Connection;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeDetailController;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeListController;

class Content
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[AsCallback(table: 'tl_content', target: 'config.onload')]
    public function setPalette(DataContainer $dc): void
    {
        if ('edit' === Input::get('act') && '' !== Input::get('id')) {
            $model = ContentModel::findByPk(Input::get('id'));

            if (null !== $model) {
                if (EmployeeListController::TYPE === $model->type) {
                    if ($model->showAllPublishedEmployees) {
                        PaletteManipulator::create()
                            ->removeField('selectEmployee', 'employee_legend')
                            ->applyToPalette(EmployeeListController::TYPE, 'tl_content')
                        ;
                    }
                }
            }
        }
    }

    #[AsCallback(table: 'tl_content', target: 'config.onload')]
    public function setInputType(DataContainer $dc): void
    {
        if ('edit' === Input::get('act') && '' !== Input::get('id')) {
            $model = ContentModel::findByPk(Input::get('id'));

            if (null !== $model) {
                if (EmployeeListController::TYPE === $model->type) {
                    $GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['inputType'] = 'checkboxWizard';
                } elseif (EmployeeDetailController::TYPE === $model->type) {
                    $GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['inputType'] = 'radio';
                }
            }
        }
    }

    #[AsCallback(table: 'tl_content', target: 'fields.selectEmployee.options')]
    public function getPublishedEmployees(): array
    {
        $return = [];
        $result = $this->connection->executeQuery('SELECT * FROM tl_employee WHERE published = ?', [1]);

        while (false !== ($row = $result->fetchAssociative())) {
            $function = '' !== $row['role'] ? ' ('.$row['role'].')' : '';
            $return[$row['id']] = $row['firstname'].' '.$row['lastname'].$function;
        }

        return $return;
    }
}
