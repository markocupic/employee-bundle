<?php
namespace Markocupic\EmployeeBundle;

use Contao\BackendTemplate;
use Contao\Validator;
use Contao\Input;
use Contao\StringUtil;
use Contao\EmployeeModel;

use Patchwork\Utf8;


/**
 * Class ContentEmployeeDetail
 * @package Markocupic\EmployeeBundle
 */
class ContentEmployeeDetail extends \ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_employeeDetail';

    /**
     * Employee object
     * @var
     */
    protected $objEmployee;



    /**
     * Do not display the module if there are no articles
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE']['employeeDetail'][0]) . ' ###';
            return $objTemplate->parse();
        }

        // Download VCard
        if (Input::get('downloadVCard') && Input::get('id') > 0)
        {
            $objEmployee = EmployeeModel::findByPk(Input::get('id'));
            if ($objEmployee !== null)
            {
                EmployeeVcard::sendToBrowser($objEmployee);
            }
        }



        $userId = $this->selectEmployee;
        $objDb = $this->Database->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')->limit(1)->execute($userId, 1);
        if(!$objDb->numRows)
        {
            return '';
        }
        $this->objEmployee = $objDb;


        return parent::generate();

    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        $row =  $this->objEmployee->row();
        if (Validator::isUuid($row['singleSRC']))
        {
            $row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
        }
        $row['interview'] = deserialize($row['interview'], true);
        $row['businessHours'] = deserialize($row['businessHours'], true);


        $this->Template->row = $row;
    }
}