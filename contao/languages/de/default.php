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

use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeDetailController;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeListController;

// Content elements
$GLOBALS['TL_LANG']['CTE']['employee_content_element'] = 'Mitarbeiter';
$GLOBALS['TL_LANG']['CTE'][EmployeeListController::TYPE] = ['Mitarbeiter-Liste', 'Fügen Sie dem Artikel eine Mitarbeiter-Liste hinzu.'];
$GLOBALS['TL_LANG']['CTE'][EmployeeDetailController::TYPE] = ['Mitarbeiter Einzelelement', 'Fügen Sie dem Artikel Detailangaben des ausgewählten Mitarbeiters hinzu.'];

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['eb_contact'] = 'Kontakt';
$GLOBALS['TL_LANG']['MSC']['eb_contactInfo'] = 'Bitte kontaktieren Sie';
$GLOBALS['TL_LANG']['MSC']['eb_emplyeeInfo'] = 'Infos zur Person';
$GLOBALS['TL_LANG']['MSC']['eb_officeHours'] = 'Bürozeiten';
$GLOBALS['TL_LANG']['MSC']['eb_publications'] = 'Publikationen';
$GLOBALS['TL_LANG']['MSC']['eb_close'] = 'Schliessen';
