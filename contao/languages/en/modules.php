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

use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeListController;
use Markocupic\EmployeeBundle\Controller\FrontendModule\EmployeeReaderController;

/*
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['employee'] = ['Mitarbeiter', 'Mitarbeiter erfassen.'];

/*
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['employee_modules'] = 'Employee';
$GLOBALS['TL_LANG']['FMD'][EmployeeListController::TYPE] = ['Employee list', 'Add an employee list module to the layout.'];
$GLOBALS['TL_LANG']['FMD'][EmployeeReaderController::TYPE] = ['Employee reader', 'Add an employee reader module to the layout.'];
