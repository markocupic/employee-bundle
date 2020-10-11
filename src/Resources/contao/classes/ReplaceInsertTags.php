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

use Contao\Environment;

class ReplaceInsertTags
{
	/**
	 * @param $strTag
	 * @return bool|string
	 */
	public function replaceInsertTags($strTag)
	{
		if (preg_match('/^vcard_download_url::([\d]+)$/', $strTag, $match))
		{
			return Environment::get('request') . '?downloadVCard=true&amp;id=' . $match[1];
		}

		return false;
	}
}
