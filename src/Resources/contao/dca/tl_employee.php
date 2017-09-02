<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   FondspolicenVergleich
 * @author    Marko Cupic
 * @license   SHAREWARE
 * @copyright Marko Cupic 2016
 */


/**
 * Table tl_employee
 */
$GLOBALS['TL_DCA']['tl_employee'] = array(

    // Config
    'config' => array(
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'onload_callback' => array(// array('tl_employee', 'checkPermission'),
        ),
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),
    // List
    'list' => array(
        'sorting' => array(
            'mode' => 1,
            'fields' => array('lastname'),
            'flag' => 1,
        ),
        'label' => array(
            'fields' => array('lastname', 'firstname'),
            'format' => '%s %s',
        ),
        'global_operations' => array(
            'all' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ),
        ),
        'operations' => array(
            'edit' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_employee']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'copy' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_employee']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_employee']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'toggle' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_employee']['toggle'],
                'icon' => 'visible.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => array('tl_employee', 'toggleIcon'),
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_employee']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ),
        ),
    ),
    // Select
    'select' => array(
        'buttons_callback' => array(),
    ),
    // Edit
    'edit' => array(
        'buttons_callback' => array(),
    ),
    // Palettes
    'palettes' => array(
        '__selector__' => array('addImage'),
        'default' => '{personal_legend},gender,title,firstname,lastname;{contact_legend},phone,mobile,email,website;{address_legend},street, postal, city, state, country;{work_legend},company,funktion,description,publications;{image_legend},addImage;{interview_legend},interview;',
    ),
    // Subpalettes
    'subpalettes' => array(
        'addImage' => 'singleSRC',
    ),
    // Fields
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'published' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['published'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array('mandatory' => false),
            'sql' => "char(1) NOT NULL default ''",
        ),
        'title' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['title'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'gender' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['gender'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => array('includeBlankOption' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'firstname' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['firstname'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'lastname' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['lastname'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'street' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['street'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'postal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['postal'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 32, 'tl_class' => 'w50'),
            'sql' => "varchar(32) NOT NULL default ''"

        ),
        'city' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['city'],
            'exclude' => true,
            'filter' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''"

        ),

        'country' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['country'],
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'inputType' => 'select',
            'eval' => array('includeBlankOption' => true, 'chosen' => true, 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'address', 'tl_class' => 'w50'),
            'options_callback' => function ()
            {
                return System::getCountries();
            },

            'sql' => "varchar(2) NOT NULL default ''"
        ),
        'funktion' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['funktion'],
            'exclude' => true,
            'search' => true,
            'sorting' => false,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'company' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['company'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'description' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['description'],
            'exclude' => true,
            'search' => true,
            'sorting' => false,
            'flag' => 1,
            'inputType' => 'textarea',
            'eval' => array('mandatory' => false, 'tl_class' => 'clr'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'publications' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['publications'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => array('mandatory' => false, 'rte' => 'tinyMCE', 'helpwizard' => true),
            'explanation' => 'insertTags',
            'sql' => "mediumtext NULL"
        ),
        'phone' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['phone'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(64) NOT NULL default ''",
        ),
        'mobile' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['mobile'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(64) NOT NULL default ''",
        ),
        'email' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['email'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'website' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['website'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'addImage' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['addImage'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => array('submitOnChange' => true),
            'sql' => "char(1) NOT NULL default ''",
        ),
        'singleSRC' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['singleSRC'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => array('filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'fieldType' => 'radio', 'mandatory' => true),
            'sql' => "binary(16) NULL",
        ),
        'interview' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['interview'],
            'exclude' => true,
            'inputType' => 'multiColumnWizard',
            'eval' => array
            (
                'columnFields' => array
                (

                    'interview_question' => array
                    (
                        'label' => &$GLOBALS['TL_LANG']['tl_employee']['interview_question'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => array('style' => 'width:180px')
                    ),
                    'interview_answer' => array
                    (
                        'label' => &$GLOBALS['TL_LANG']['tl_employee']['interview_answer'],
                        'exclude' => true,
                        'inputType' => 'textarea',
                        'eval' => array('style' => 'width:300px')
                    )
                )
            ),
            'sql' => "blob NULL"
        ),
        'businessHours' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_employee']['businessHours'],
            'exclude' => true,
            'inputType' => 'multiColumnWizard',
            'eval' => array
            (
                'columnFields' => array
                (
                    'businessHoursWeekday' => array
                    (
                        'label' => &$GLOBALS['TL_LANG']['tl_employee']['businessHoursWeekday'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => array('style' => 'width:180px')
                    ),
                    'businessHoursTime' => array
                    (
                        'label' => &$GLOBALS['TL_LANG']['tl_employee']['businessHoursTime'],
                        'exclude' => true,
                        'inputType' => 'text',
                        'eval' => array('style' => 'width:180px')
                    )
                )
            ),
            'sql' => "blob NULL"
        )
    ),
);


class tl_employee extends Backend
{

    /**
     * Check permissions to edit table tl_employee
     */
    public function checkPermission()
    {
        //
    }

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    /**
     * Return the "toggle visibility" button
     *
     * @param array $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * Disable/enable a user group
     *
     * @param integer $intId
     * @param boolean $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc = null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }

        $this->checkPermission();


        $objVersions = new Versions('tl_news', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_employee']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_employee']['fields']['published']['save_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
                }
                elseif (is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, ($dc ?: $this));
                }
            }
        }

        // Update the database
        $this->Database->prepare("UPDATE tl_employee SET tstamp=" . time() . ", published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")->execute($intId);

        $objVersions->create();

    }


}
