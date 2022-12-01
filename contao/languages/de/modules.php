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

/*
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['employee'] = ['Mitarbeiter', 'Mitarbeiter erfassen.'];

/*
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['employee_modules'] = 'Mitarbeiter';
$GLOBALS['TL_LANG']['FMD'][EmployeeListController::TYPE] = ['Mitarbeiter-Liste', 'Fügen Sie dem Layout eine Mitarbeiter-Liste hinzu.'];
$GLOBALS['TL_LANG']['FMD'][EmployeeReaderController::TYPE] = ['Mitarbeiter-Reader', 'Fügen Sie dem Layout einen Mitarbeiter Reader hinzu.'];
