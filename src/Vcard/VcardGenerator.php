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

namespace Markocupic\EmployeeBundle\Vcard;

use Contao\EmployeeModel;
use Contao\File;
use Contao\FrontendTemplate;

/**
 * Class VcardGenerator
 * @package Markocupic\EmployeeBundle\Vcard
 */
class VcardGenerator
{

	const VCARD_TEMPLATE = 'partial_employee_vcard';

    /**
     * @param EmployeeModel $objEmployee
     * @throws \Exception
     */
	public static function sendToBrowser(EmployeeModel $objEmployee)
	{
		$objTemplate = new FrontendTemplate(static::VCARD_TEMPLATE);

		// Set data from object
		$arrData = $objEmployee->row();
		$row = array();

		foreach ($arrData as $k => $v)
		{
			// utf8 decode strings and html_entity_decode strings f.ex. &#40; => (
			$row[$k] = utf8_decode(html_entity_decode($v));
		}

		$objTemplate->setData($row);

		// Set fname
		$objTemplate->fn = trim(implode(' ', array($row['title'], $row['firstname'], $row['lastname'])));

		// Parse template
		$vcard = $objTemplate->parse();

		// Create tmp-file
		$objFile = new File('system/tmp/' . time() . '.vcf', true);
		$objFile->append($vcard);
		$objFile->close();
		$objFile->sendToBrowser(sprintf('vcard-%s-%s.vcf', $objEmployee->firstname, $objEmployee->lastname));
	}
}
