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

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Contao\Input;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeListElementController;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeSingleElementController;

class Content
{
    /**
     * @Callback(table="tl_content", target="config.onload")
     */
    public function setPalette(): void
    {
        if ('edit' === Input::get('act') && '' !== Input::get('id')) {
            $objContent = ContentModel::findByPk(Input::get('id'));

            if (null !== $objContent) {
                if (EmployeeSingleElementController::TYPE === $objContent->type) {
                    $GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['inputType'] = 'radio';
                    $GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['eval']['multiple'] = 'false';
                }

                if (EmployeeListElementController::TYPE === $objContent->type) {
                    if ($objContent->showAllPublishedEmployees) {
                        PaletteManipulator::create()
                            ->removeField('selectEmployee', 'employee_legend')
                            ->applyToPalette('employee_list_element', 'tl_content')
                        ;
                    }
                }
            }
        }
    }

    /**
     * @Callback(table="tl_content", target="fields.selectEmployee.options")
     */
    public function getPublishedEmployees(): array
    {
        $return = [];
        $objDb = Database::getInstance()
            ->prepare('SELECT * FROM tl_employee WHERE published=?')
            ->execute('1')
        ;

        while ($objDb->next()) {
            $function = '' !== $objDb->funktion ? ' ('.$objDb->funktion.')' : '';
            $return[$objDb->id] = $objDb->firstname.' '.$objDb->lastname.$function;
        }

        return $return;
    }
}
