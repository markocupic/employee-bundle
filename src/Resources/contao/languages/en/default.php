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
 * Content elements
 */
$GLOBALS['TL_LANG']['CTE']['employee_elements'] = 'Mitarbeiter Inhaltselemente';
$GLOBALS['TL_LANG']['CTE'][EmployeeListElementController::TYPE] = ['Mitarbeiter-Liste', 'Fügen Sie dem Layout eine Mitarbeiter-Liste hinzu.'];
$GLOBALS['TL_LANG']['CTE'][EmployeeSingleElementController::TYPE] = ['Mitarbeiter-Einzelelement', 'Fügen Sie dem Layout einen Mitarbeiter hinzu.'];

/*
 * MSC
 */
$GLOBALS['TL_LANG']['MSC']['eb_contact'] = 'Kontakt';
$GLOBALS['TL_LANG']['MSC']['eb_contactInfo'] = 'Bitte kontaktieren Sie';
$GLOBALS['TL_LANG']['MSC']['eb_emplyeeInfo'] = 'Infos zur Person';
$GLOBALS['TL_LANG']['MSC']['eb_officeHours'] = 'Bürozeiten';
$GLOBALS['TL_LANG']['MSC']['eb_publications'] = 'Publikationen';
$GLOBALS['TL_LANG']['MSC']['eb_close'] = 'Schliessen';
