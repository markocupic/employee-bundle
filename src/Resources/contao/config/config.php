<?php

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

use Markocupic\EmployeeBundle\ContentEmployeeDetail;
use Markocupic\EmployeeBundle\ContentEmployeeList;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['employee'] = array(
	'tables' => array('tl_employee')
);

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['employee'] = array(
	//'employeeList'   => ContentEmployeeList::class,
	//'employeeDetail' => ContentEmployeeDetail::class,
);

/**
 * Do not index a page if one of the following parameters is set
 */
$GLOBALS['TL_NOINDEX_KEYS'][] = 'downloadVCard';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_employee'] = EmployeeModel::class;
