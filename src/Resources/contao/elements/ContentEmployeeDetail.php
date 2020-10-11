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
 * Class ContentEmployeeDetail
 */
class ContentEmployeeDetail extends ContentElement
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
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE']['employeeDetail'][0]) . ' ###';

			return $objTemplate->parse();
		}

		$userId = $this->selectEmployee;
		$objDb = $this->Database
			->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')
			->limit(1)
			->execute($userId, 1);

		if (!$objDb->numRows)
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
		$row = $this->objEmployee->row();

		if (Validator::isUuid($row['singleSRC']))
		{
			$row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
		}
		$row['interview'] = StringUtil::deserialize($row['interview'], true);
		$row['businessHours'] = StringUtil::deserialize($row['businessHours'], true);

		$this->Template->row = $row;
	}
}
