<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller;

use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\Model\Collection;
use Contao\StringUtil;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

trait EmployeeTrait
{



    public function getEmployees(ContentModel|ModuleModel $model): Collection|null
    {
        $arrOptions = [
            'order' => 'id ASC', // default order
        ];

        $blnOrderBy = false;

        // Override default order
        if ($model->addSorting) {
            $orderByItems = StringUtil::deserialize($model->orderBy, true);
            $arrOrderBy = [];

            foreach ($orderByItems as $orderBy) {
                $blnOrderBy = true;
                $arrOrderBy[] = trim($orderBy['column'].' '.$orderBy['sortDirection']);
            }

            if ($blnOrderBy) {
                $arrOptions['order'] = implode(', ', $arrOrderBy);
            }
        }

        if ($model->showAllPublishedEmployees) {
            return EmployeeModel::findAllPublished($arrOptions);
        }

        $arrModels = [];
        $arrIds = StringUtil::deserialize($model->selectEmployee, true);

        if ($blnOrderBy) {
            return EmployeeModel::findMultipleAndPublishedByIds($arrIds, $arrOptions);
        }

        // Take the order from the tl_content checkboxWizard if no order is set.
        foreach ($arrIds as $id) {
            if (null !== ($objModel = EmployeeModel::findPublishedById($id, $arrOptions))) {
                $arrModels[] = $objModel;
            }
        }

        return new Collection($arrModels, 'tl_employee');
    }
}

