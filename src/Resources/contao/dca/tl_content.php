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
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Input;

/**
 * Onload callbacks
 */
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_employee', 'setPalette');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['employee_list_element'] = '{type_legend},type;{employee_legend},showAllPublishedEmployees,selectEmployee;{source_legend},size,imagemargin,fullsize;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['employee_reader_element'] = '{type_legend},type;{employee_legend},selectEmployee;{source_legend},size,imagemargin,fullsize,overwriteMeta;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

/**
 * Fields
 */
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
	'eval'             => array('mandatory' => true, 'multiple' => true, 'orderField' => 'orderSelectedEmployee'),
	'sql'              => "blob NULL",
	'options_callback' => array('tl_content_employee', 'getPublishedEmployees'),
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
	 * Set palette
	 */
	public function setPalette()
	{
		if (Input::get('act') === 'edit' && Input::get('id') != '')
		{
			$objContent = ContentModel::findByPk(Input::get('id'));

			if ($objContent !== null)
			{
				if ($objContent->type === 'employee_reader_element')
				{
					$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['inputType'] = 'radio';
					$GLOBALS['TL_DCA']['tl_content']['fields']['selectEmployee']['eval']['multiple'] = 'false';
				}

				if ($objContent->type === 'employee_list_element')
				{
					if ($objContent->showAllPublishedEmployees)
					{
						PaletteManipulator::create()
							->removeField('selectEmployee', 'employee_legend')
							->applyToPalette('employee_list_element', 'tl_content');
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getPublishedEmployees(): array
	{
		$return = array();
		$objDb = $this->Database
			->prepare('SELECT * FROM tl_employee WHERE published=?')
			->execute('1')
		;

		while ($objDb->next())
		{
			$function = $objDb->funktion != '' ? ' (' . $objDb->funktion . ')' : '';
			$return[$objDb->id] = $objDb->firstname . ' ' . $objDb->lastname . $function;
		}

		return $return;
	}
}
