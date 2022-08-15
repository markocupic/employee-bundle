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

namespace Markocupic\EmployeeBundle;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\StringUtil;
use Contao\Validator;
use Patchwork\Utf8;

/**
 * Class ContentEmployeeList
 */
class ContentEmployeeList extends ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_employeeList';

	/**
	 * Do not display the module if there are no articles
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE']['employeeList'][0]) . ' ###';

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

		if ($this->showAllPublishedEmployees)
		{
			$objDb = $this->Database
				->prepare('SELECT * FROM tl_employee WHERE published=? ORDER BY lastname, firstname')
				->execute(1);
			$arrEmployees = $objDb->fetchEach('id');
		}
		else
		{
			$arrEmployees = StringUtil::deserialize($this->selectEmployee, true);
		}

		foreach ($arrEmployees as $userId)
		{
			$objDb = $this->Database
				->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')
				->execute($userId, 1);

			while ($objDb->next())
			{
				$row = $objDb->row();

				if (Validator::isUuid($row['singleSRC']))
				{
					$row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
				}
				$row['interview'] = StringUtil::deserialize($row['interview'], true);
				$row['businessHours'] = StringUtil::deserialize($row['businessHours'], true);

				$rows[] = $row;
			}
		}
		$this->Template->rows = $rows;
	}
}
