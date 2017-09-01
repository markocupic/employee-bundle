<?php
namespace Markocupic\EmployeeBundle;

use Contao\BackendTemplate;
use Contao\Validator;
use Contao\StringUtil;



class ModuleEmployeeList extends \ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_employee_list';


    /**
     * Do not display the module if there are no articles
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['employeeList'][0]) . ' ###';

            return $objTemplate->parse();
        }
        return parent::generate();

    }


    /**
     * Generate the module
     */
    protected function compile()
    {

        $rows = array();
        if($this->showAllPublishedEmployees)
        {
            $objDb = $this->Database->prepare('SELECT * FROM tl_employee WHERE published=? ORDER BY lastname, firstname')->execute(1);
            $arrEmployees = $objDb->fetchEach('id');
        }
        else
        {
            $arrEmployees = deserialize($this->selectEmployee, true);
        }
        foreach ($arrEmployees as $userId)
        {
            $objDb = $this->Database->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')->execute($userId, 1);
            while ($objDb->next())
            {
                $row = $objDb->row();
                if (Validator::isUuid($row['singleSRC']))
                {
                    $row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
                }
                $row['interview'] = deserialize($row['interview'], true);

                $rows[] = $row;
            }
        }
        $this->Template->rows = $rows;
    }
}