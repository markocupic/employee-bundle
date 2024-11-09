<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeDetailController;
use Markocupic\EmployeeBundle\Controller\ContentElement\EmployeeListController;

// Content elements
$GLOBALS['TL_LANG']['CTE']['employee_content_element'] = 'Employee';
$GLOBALS['TL_LANG']['CTE'][EmployeeListController::TYPE] = ['Employee list', 'Add an employee list content element to the article.'];
$GLOBALS['TL_LANG']['CTE'][EmployeeDetailController::TYPE] = ['Employee details', 'Add details of the selected employee to the article.'];

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['eb_contact'] = 'Contact';
$GLOBALS['TL_LANG']['MSC']['eb_contactInfo'] = 'Please contact';
$GLOBALS['TL_LANG']['MSC']['eb_emplyeeInfo'] = 'About';
$GLOBALS['TL_LANG']['MSC']['eb_officeHours'] = 'Office hours';
$GLOBALS['TL_LANG']['MSC']['eb_publications'] = 'Publications';
$GLOBALS['TL_LANG']['MSC']['eb_close'] = 'Close';
