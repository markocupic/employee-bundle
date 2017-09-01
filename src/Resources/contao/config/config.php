<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   FondspolicenVergleich
 * @author    Marko Cupic
 * @license   SHAREWARE
 * @copyright Marko Cupic 2016
 */







/**
 * BACK END MODULES
 *
 * Back end modules are stored in a global array called "BE_MOD". You can add
 * your own modules by adding them to the array.
 */
$GLOBALS['BE_MOD']['content']['employee'] = array(
        'tables' => array('tl_employee'),
        'icon'   => 'system/modules/employee/assets/icon.png'
);


/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['employee'] = array(
        'employeeList' => 'Markocupic\EmployeeBundle\ModuleEmployeeList',
        'employeeDetail' => 'Markocupic\EmployeeBundle\ModuleEmployeeDetail',
);

