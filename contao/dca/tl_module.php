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

use Contao\BackendUser;
use Contao\Controller;
use Contao\System;
use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeListController;
use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeReaderController;

// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes'][EmployeeListController::TYPE] = '
    {title_legend},name,type;
    {employee_legend},showAllPublishedEmployees,selectEmployee,addSorting;
    {employee_image_legend},addEmployeeImage;
    {employee_gallery_legend},addEmployeeGallery;
    {redirect_legend},jumpTo;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_module']['palettes'][EmployeeReaderController::TYPE] = '
    {title_legend},name,type;
    {employee_image_legend},addEmployeeImage;
    {employee_gallery_legend},addEmployeeGallery;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

// Selectors
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addEmployeeImage';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addEmployeeGallery';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addSorting';

// Subpalettes
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addEmployeeImage'] = 'imgSize,imgFullsize';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addEmployeeGallery'] = 'galSize,galFullsize';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addSorting'] = 'orderBy';

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['addEmployeeImage'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addEmployeeGallery'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['showAllPublishedEmployees'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['selectEmployee'] = [
    'exclude'   => true,
    'inputType' => 'checkboxWizard',
    'eval'      => ['mandatory' => true, 'multiple' => true],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addSorting'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

Controller::loadDataContainer('tl_employee');

$GLOBALS['TL_DCA']['tl_module']['fields']['orderBy'] = [
    'exclude'   => true,
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'tl_class'     => 'clr w50',
        'columnFields' => [
            'column'        => [
                'label'     => &$GLOBALS['TL_LANG']['tl_employee']['column'],
                'exclude'   => true,
                'inputType' => 'select',
                'options'   => array_keys($GLOBALS['TL_DCA']['tl_employee']['fields']),
                'eval'      => ['style' => 'width:140px'],
            ],
            'sortDirection' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_employee']['sortDirection'],
                'exclude'   => true,
                'inputType' => 'select',
                'options'   => ['ASC', 'DESC'],
                'eval'      => ['style' => 'width:80px', 'rte' => null],
            ],
        ],
    ],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['galSize'] = [
    'label'            => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
    'exclude'          => true,
    'inputType'        => 'imageSize',
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'options_callback' => static function () {
        return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
    },
    'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'sql'              => "varchar(128) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['imgFullsize'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['galFullsize'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];
