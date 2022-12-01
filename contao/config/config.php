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

use Markocupic\EmployeeBundle\Model\EmployeeModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['employee'] = [
    'tables' => ['tl_employee'],
];

/*
 * Do not index a page if one of the following parameters is set
 */
$GLOBALS['TL_NOINDEX_KEYS'][] = 'downloadVCard';

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_employee'] = EmployeeModel::class;
