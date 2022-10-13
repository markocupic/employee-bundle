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

use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeListElementController;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeSingleElementController;

/*
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes'][EmployeeListElementController::TYPE] = '
    {type_legend},type;
    {employee_legend},showAllPublishedEmployees,selectEmployee;
    {source_legend},size,imagemargin,fullsize;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_content']['palettes'][EmployeeSingleElementController::TYPE] = '
    {type_legend},type;
    {employee_legend},selectEmployee;
    {source_legend},size,imagemargin,fullsize,overwriteMeta;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['showAllPublishedEmployees'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee'] = [
    'exclude'   => true,
    'inputType' => 'checkboxWizard',
    'eval'      => ['mandatory' => true, 'multiple' => true, 'orderField' => 'orderSelectedEmployee'],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['orderSelectedEmployee'] = [
    'sql' => 'blob NULL',
];
