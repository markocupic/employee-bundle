<?php

namespace Markocupic\EmployeeBundle;

use Contao\EmployeeModel;
use Contao\PageModel;
use Contao\LayoutModel;
use Contao\PageRegular;
use Contao\Input;

/**
 * Class GeneratePage
 * @package Markocupic\EmployeeBundle
 */
class GeneratePage
{
    public function generatePage(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular)
    {
        // Trigger VCard download
        if (Input::get('downloadVCard') == 'true' && Input::get('id') != '')
        {
            $objEmployee = EmployeeModel::findByPk(Input::get('id'));
            if ($objEmployee !== null)
            {
                EmployeeVcard::sendToBrowser($objEmployee);
            }
        }
    }
}