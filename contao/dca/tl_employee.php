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

use Contao\Config;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

$GLOBALS['TL_DCA']['tl_employee'] = [
    'config'      => [
        'dataContainer'    => DC_Table::class,
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'sorting'    => [
            'mode'   => DataContainer::MODE_SORTED,
            'fields' => ['lastname'],
            'flag'   => DataContainer::SORT_INITIAL_LETTER_ASC,
        ],
        'label'      => [
            'fields' => ['lastname', 'firstname'],
            'format' => '%s %s',
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle',
            'show',
        ],
    ],
    'palettes'    => [
        '__selector__' => ['addImage', 'addGallery'],
        'default'      => '
            {personal_legend},title,firstname,lastname,gender,dateOfBirth,alias;
            {contact_legend},phone,mobile,email,fax,skype,businessHours;
            {social_media_legend},linkedIn,xing,website;
            {address_legend},street,postal,city,state,country;
            {work_legend},company,role,roleDetail,publications;
            {image_legend},addImage;
            {gallery_legend},addGallery;
            {interview_legend},interview
        ',
    ],
    'subpalettes' => [
        'addImage'   => 'singleSRC',
        'addGallery' => 'multiSRC',
    ],
    'fields'      => [
        'id'            => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'        => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'published'     => [
            'exclude'   => true,
            'toggle'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['mandatory' => false],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'title'         => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'firstname'     => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'lastname'      => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'gender'        => [
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['male', 'female'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval'      => ['includeBlankOption' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'dateOfBirth'   => [
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w25 wizard'],
            'sql'       => "varchar(11) NOT NULL default ''",
        ],
        'alias'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) BINARY NOT NULL default ''",
        ],
        'phone'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'mobile'        => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'email'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'fax'           => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'skype'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'decodeEntities' => true, 'tl_class' => 'w25'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'businessHours' => [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr w50',
                'columnFields' => [
                    'weekday' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['weekday'],
                        'exclude'   => true,
                        'inputType' => 'text',
                    ],
                    'time'    => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['time'],
                        'exclude'   => true,
                        'inputType' => 'text',
                    ],
                ],
            ],
            'sql'       => 'blob NULL',
        ],
        'linkedIn'      => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'xing'          => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'website'       => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'street'        => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'postal'        => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w25'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'city'          => [
            'exclude'   => true,
            'filter'    => true,
            'search'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'state'         => [
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w25'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'country'       => [
            'exclude'          => true,
            'filter'           => true,
            'sorting'          => true,
            'inputType'        => 'select',
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'address', 'tl_class' => 'w25'],
            'options_callback' => static fn() => System::getContainer()->get('contao.intl.countries')->getCountries(),
            'sql'              => "varchar(2) NOT NULL default ''",
        ],
        'company'       => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'role'          => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => false,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w25'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'roleDetail'    => [
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr x50'],
            'explanation' => 'insertTags',
            'sql'         => 'mediumtext NULL',
        ],
        'publications'  => [
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'w50'],
            'explanation' => 'insertTags',
            'sql'         => 'mediumtext NULL',
        ],
        'addImage'      => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'singleSRC'     => [
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'fieldType' => 'radio', 'mandatory' => true],
            'sql'       => 'binary(16) NULL',
        ],
        'addGallery'    => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'multiSRC'      => [
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['isGallery' => true, 'extensions' => System::getContainer()->getParameter('contao.image.valid_extensions'), 'multiple' => true, 'fieldType' => 'checkbox', 'files' => true, 'mandatory' => true],
            'sql'       => "blob NULL",
        ],
        'interview'     => [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr w50',
                'columnFields' => [
                    'question' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['question'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:200px'],
                    ],
                    'answer'   => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['answer'],
                        'exclude'   => true,
                        'inputType' => 'textarea',
                        'eval'      => ['style' => 'width:200px', 'rte' => null],
                    ],
                ],
            ],
            'sql'       => 'blob NULL',
        ],
    ],
];
