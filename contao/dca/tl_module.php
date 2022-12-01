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

use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeListController;
use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeReaderController;

// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes'][EmployeeListController::TYPE] = '
    {title_legend},name,type;
    {employee_legend},showAllPublishedEmployees,selectEmployee;
    {source_legend},addPortraitImage,addGallery;
    {redirect_legend},jumpTo;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

$GLOBALS['TL_DCA']['tl_module']['palettes'][EmployeeReaderController::TYPE] = '
    {title_legend},name,type;
    {source_legend},addPortraitImage,addGallery;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

// Selectors
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addPortraitImage';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addGallery';

// Subpalettes
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addPortraitImage'] = 'imgSize,fullsize';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addGallery'] = 'imgSize,fullsize';

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['addPortraitImage'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addGallery'] = [
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
    'eval'      => ['mandatory' => true, 'multiple' => true, 'orderField' => 'orderSelectedEmployee'],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['orderSelectedEmployee'] = [
    'sql' => 'blob NULL',
];
