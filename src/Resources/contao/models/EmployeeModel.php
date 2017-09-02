<?php



/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;

/**
 * Class EmployeeModel
 * @package Contao
 */
class EmployeeModel extends \Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_employee';

}
