<?php


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
        'employeeList' => 'Markocupic\EmployeeBundle\ContentEmployeeList',
        'employeeDetail' => 'Markocupic\EmployeeBundle\ContentEmployeeDetail',
);


/**
 * Hooks
 */
// Return VCard url from {{vcard_download_url::*}}   => * tl_employee.id
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Markocupic\EmployeeBundle\ReplaceInsertTags', 'replaceInsertTags');

// Trigger VCard Download
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Markocupic\EmployeeBundle\GeneratePage', 'generatePage');


/**
 * Do not index a page if one of the following parameters is set
 */
$GLOBALS['TL_NOINDEX_KEYS'][] = 'downloadVCard';

