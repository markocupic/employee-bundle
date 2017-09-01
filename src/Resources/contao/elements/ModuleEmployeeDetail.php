<?php
namespace Markocupic\EmployeeBundle;


class ModuleEmployeeDetail extends \ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_gmk_employee_detail';


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
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['employeeDetail'][0]) . ' ###';
            return $objTemplate->parse();
        }
        $userId = $this->gmkSelectedMitarbeiter;

        $objDb = $this->Database->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')->execute($userId, 1);
        if(!$objDb->numRows)
        {
            return '';
        }
        $this->objUser = $objDb;


        return parent::generate();

    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        $row =  $this->objUser->row();
        if (\Validator::isUuid($row['singleSRC']))
        {
            $row['singleSRC'] = \StringUtil::binToUuid($row['singleSRC']);
        }
        $row['interview'] = deserialize($row['interview'], true);

        $this->Template->row = $row;
    }
}