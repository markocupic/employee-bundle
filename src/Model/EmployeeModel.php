<?php

declare(strict_types=1);

/*
 * This file is part of Test Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * @link https://github.com/markocupic/a
 */

namespace Markocupic\EmployeeBundle\Model;

use Contao\Model;

/**
 * Class EmployeeModel
 * @package Markocupic\EmployeeBundle\Model
 */
class EmployeeModel extends Model
{
    protected static $strTable = 'tl_employee';
}
