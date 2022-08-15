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

use Contao\Backend;
use Contao\ContentModel;
use Contao\Input;

// Onload callback
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_employee', 'setPalette');

// Palette
$GLOBALS['TL_DCA']['tl_content']['palettes']['employeeList'] = '{type_legend},type;{employee_legend},showAllPublishedEmployees,selectEmployee;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['employeeDetail'] = '{type_legend},type;{employee_legend},selectEmployee;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

// Fields
$GLOBALS['TL_DCA']['tl_content']['fields']['showAllPublishedEmployees'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_content']['showAllPublishedEmployees'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'eval'      => array('submitOnChange' => true),
	'sql'       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee'] = array(
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['selectEmployee'],
	'exclude'          => true,
	'inputType'        => 'checkboxWizard',
	'eval'             => array('multiple' => true, 'orderField' => 'orderSelectedEmployee', 'mandatory' => false),
	'sql'              => "blob NULL",
	'options_callback' => array('tl_content_employee', 'getPublishedMitarbeiter'),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['orderSelectedEmployee'] = array(
	'label' => &$GLOBALS['TL_LANG']['tl_content']['orderSelectedEmployee'],
	'sql'   => "blob NULL",
);

/**
 * Class tl_content_employee
 */
class tl_content_employee extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Set palette
	 */
	public function setPalette()
	{
		if (Input::get('act') == 'edit' && Input::get('id') != '')
		{
			$objContent = ContentModel::findByPk(Input::get('id'));

			if ($objContent !== null)
			{
				if ($objContent->type == 'employeeDetail')
				{
					$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['inputType'] = 'radio';
					$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['eval']['fieldType'] = 'radio';
					$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['eval']['multiple'] = 'false';
				}

				if ($objContent->type == 'employeeList')
				{
					if ($objContent->showAllPublishedEmployees)
					{
						$GLOBALS['TL_DCA']['tl_content']['palettes']['employeeList'] = str_replace(',selectEmployee', '', $GLOBALS['TL_DCA']['tl_content']['palettes']['employeeList']);
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getPublishedMitarbeiter()
	{
		$return = array();
		$objDb = $this->Database->prepare('SELECT * FROM tl_employee WHERE published=?')->execute(1);

		while ($objDb->next())
		{
			$function = $objDb->funktion != '' ? ' (' . $objDb->funktion . ')' : '';
			$return[$objDb->id] = $objDb->firstname . ' ' . $objDb->lastname . $function;
		}

		return $return;
	}
}
