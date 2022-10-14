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

use Contao\DC_Table;
use Contao\Config;
use Contao\DataContainer;
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
        'sorting'           => [
            'mode'   => DataContainer::MODE_SORTED,
            'fields' => ['lastname'],
            'flag'   => DataContainer::SORT_INITIAL_LETTER_ASC,
        ],
        'label'             => [
            'fields' => ['lastname', 'firstname'],
            'format' => '%s %s',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy'   => [
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'href'    => 'act=toggle&amp;field=published',
                'icon'    => 'visible.svg',
                'reverse' => false,
            ],
            'show'   => [
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => ['addImage'],
        'default'      => '
            {personal_legend},gender,title,firstname,lastname;
            {contact_legend},phone,mobile,email,fax,skype,website,businessHours;
            {address_legend},street, postal, city, state, country;
            {work_legend},company,funktion,description,publications;
            {image_legend},addImage;{interview_legend},interview;
        ',
    ],
    'subpalettes' => [
        'addImage' => 'singleSRC',
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
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'gender'        => [
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['male', 'female'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval'      => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'firstname'     => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'lastname'      => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'street'        => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'postal'        => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'city'          => [
            'exclude'   => true,
            'filter'    => true,
            'search'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'country'       => [
            'exclude'          => true,
            'filter'           => true,
            'sorting'          => true,
            'inputType'        => 'select',
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'address', 'tl_class' => 'w50'],
            'options_callback' => static fn() => System::getCountries(),
            'sql'              => "varchar(2) NOT NULL default ''",
        ],
        'funktion'      => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => false,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'company'       => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'description'   => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => false,
            'flag'      => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'clr', 'rte' => null],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'publications'  => [
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['rte' => 'tinyMCE', 'helpwizard' => true],
            'explanation' => 'insertTags',
            'sql'         => 'mediumtext NULL',
        ],
        'phone'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'fax'           => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'skype'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'mobile'        => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'email'         => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'website'       => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'addImage'      => [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'singleSRC'     => [
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'fieldType' => 'radio', 'mandatory' => true],
            'sql'       => 'binary(16) NULL',
        ],
        'interview'     => [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'interview_question' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['interview_question'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:200px'],
                    ],
                    'interview_answer'   => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['interview_answer'],
                        'exclude'   => true,
                        'inputType' => 'textarea',
                        'eval'      => ['style' => 'width:200px', 'rte' => null],
                    ],
                ],
            ],
            'sql'       => 'blob NULL',
        ],
        'businessHours' => [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'businessHoursWeekday' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['businessHoursWeekday'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:100px'],
                    ],
                    'businessHoursTime'    => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_employee']['businessHoursTime'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:100px'],
                    ],
                ],
            ],
            'sql'       => 'blob NULL',
        ],
    ],
];
