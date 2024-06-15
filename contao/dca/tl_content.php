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

use Contao\BackendUser;
use Contao\System;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeDetailContentElementController;

$GLOBALS['TL_DCA']['tl_content']['palettes'][EmployeeDetailContentElementController::TYPE] = '
    {type_legend},type,headline;
    {employee_legend},selectEmployee,
    {employee_image_legend},addEmployeeImage;
    {employee_gallery_legend},addEmployeeGallery;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop
';

// Selectors
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addEmployeeImage';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addEmployeeGallery';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addSorting';

// Subpalettes
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addEmployeeImage'] = 'imgSize,imgFullsize';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addEmployeeGallery'] = 'galSize,galFullsize';

// Fields
$GLOBALS['TL_DCA']['tl_content']['fields']['addEmployeeImage'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['addEmployeeGallery'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee'] = [
    'exclude'    => true,
    'inputType'  => 'radio',
    'eval'       => ['mandatory' => true, 'multiple' => false, 'tl_class' => 'clr'],
    'foreignKey' => "tl_employee.CONCAT(firstname,' ',lastname)",
    'sql'        => 'int(10) unsigned NOT NULL default 0',
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['galSize'] = [
    'label'            => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
    'exclude'          => true,
    'inputType'        => 'imageSize',
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'options_callback' => static function () {
        return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
    },
    'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'clr'],
    'sql'              => "varchar(128) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['imgSize'] = [
    'label'            => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
    'exclude'          => true,
    'inputType'        => 'imageSize',
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'options_callback' => static function () {
        return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
    },
    'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'clr'],
    'sql'              => "varchar(128) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['imgFullsize'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'clr m12'],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['galFullsize'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'clr m12'],
    'sql'       => "char(1) COLLATE ascii_bin NOT NULL default ''",
];
